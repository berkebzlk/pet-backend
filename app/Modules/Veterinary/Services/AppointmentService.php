<?php

namespace App\Modules\Veterinary\Services;

use App\Modules\Veterinary\Models\Appointment;
use App\Modules\Veterinary\Models\VeterinaryAvailability;
use App\Modules\Veterinary\Models\VeterinaryException;
use App\Modules\Veterinary\Models\VeterinaryProfile;
use App\Modules\Pet\Models\Pet;
use App\Modules\Notification\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class AppointmentService
{
    public function getAvailableSlots(int $profileId, string $date)
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

        // 1. Check exceptions first
        $exception = VeterinaryException::where('veterinary_profile_id', $profileId)
            ->whereDate('date', $date)
            ->first();

        $isWorking = true;
        $startTime = null;
        $endTime = null;
        $slotDuration = 30; // default

        if ($exception) {
            if (!$exception->is_working) {
                return [];
            }
            $startTime = $exception->start_time;
            $endTime = $exception->end_time;
        }

        // If no exception, or if exception didn't specify start/end, load from routine availability
        if (!$exception || is_null($startTime) || is_null($endTime)) {
            $availability = VeterinaryAvailability::where('veterinary_profile_id', $profileId)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            if (!$availability) {
                return [];
            }

            $startTime = $availability->start_time;
            $endTime = $availability->end_time;
            $slotDuration = $availability->slot_duration;
        }

        // 2. Generate all slots
        $slots = [];
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);

        while ($start->copy()->addMinutes($slotDuration)->lte($end)) {
            $slotStart = $start->format('H:i');
            $slotEnd = $start->copy()->addMinutes($slotDuration)->format('H:i');
            $slots[] = [
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
            ];
            $start->addMinutes($slotDuration);
        }

        // 3. Query existing appointments on that date
        $appointments = Appointment::where('veterinary_profile_id', $profileId)
            ->whereDate('appointment_date', $date)
            ->get();

        // 4. Decorate slots with their booking status
        $decoratedSlots = [];
        $isToday = $carbonDate->isToday();
        $now = Carbon::now();

        foreach ($slots as $slot) {
            $slotStart = $slot['start_time'];
            
            // Find if there is an active booking on this slot
            $activeApp = $appointments->first(function ($a) use ($slotStart) {
                $timeMatch = Carbon::createFromFormat('H:i:s', $a->start_time)->format('H:i') === $slotStart;
                return $timeMatch && in_array($a->status, ['confirmed', 'completed']);
            });

            $status = 'available';
            $isAvailable = true;

            if ($activeApp) {
                $status = 'booked';
                $isAvailable = false;
            } elseif ($isToday) {
                $slotTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $slotStart);
                if ($slotTime->lt($now)) {
                    $status = 'past';
                    $isAvailable = false;
                }
            }

            $decoratedSlots[] = [
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'status' => $status,
                'is_available' => $isAvailable,
            ];
        }

        return $decoratedSlots;
    }

    public function bookAppointment(array $data)
    {
        $profileId = $data['veterinary_profile_id'];
        $petId = $data['pet_id'];
        $date = $data['appointment_date'];
        $startTime = $data['start_time'];

        $pet = Pet::findOrFail($petId);
        if ($pet->user_id !== auth()->id()) {
            throw new Exception('Unauthorized pet selection.', 403);
        }

        $profile = VeterinaryProfile::findOrFail($profileId);

        if ($profile->user_id === auth()->id()) {
            throw new Exception('You cannot book an appointment at your own clinic.', 400);
        }

        $availableSlots = $this->getAvailableSlots($profileId, $date);
        $isSlotFree = false;
        $endTime = null;
        foreach ($availableSlots as $slot) {
            if ($slot['start_time'] === $startTime && $slot['is_available']) {
                $isSlotFree = true;
                $endTime = $slot['end_time'];
                break;
            }
        }

        if (!$isSlotFree) {
            throw new Exception('The selected appointment slot is no longer available.', 400);
        }

        return DB::transaction(function () use ($profileId, $petId, $date, $startTime, $endTime, $data, $profile, $pet) {
            $appointment = Appointment::create([
                'veterinary_profile_id' => $profileId,
                'pet_id' => $petId,
                'appointment_date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'confirmed',
                'notes' => $data['notes'] ?? null,
            ]);

            // Notify veterinarian user
            $this->sendNotification(
                $profile->user_id,
                'Yeni Randevu',
                "{$pet->name} isimli evcil hayvan için {$date} tarihinde saat {$startTime} konumuna randevu alındı.",
                $appointment->id,
                'confirmed'
            );

            return $appointment;
        });
    }

    public function updateWeeklyAvailability(int $profileId, array $availabilities)
    {
        return DB::transaction(function () use ($profileId, $availabilities) {
            // Cancel conflicting appointments first
            $this->cancelConflictingAppointmentsForWeekly($profileId, $availabilities);

            // Clear old availabilities
            VeterinaryAvailability::where('veterinary_profile_id', $profileId)->delete();

            // Insert new ones
            $records = [];
            foreach ($availabilities as $avail) {
                $records[] = [
                    'veterinary_profile_id' => $profileId,
                    'day_of_week' => $avail['day_of_week'],
                    'start_time' => $avail['start_time'],
                    'end_time' => $avail['end_time'],
                    'slot_duration' => $avail['slot_duration'] ?? 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            VeterinaryAvailability::insert($records);
            return true;
        });
    }

    public function saveException(int $profileId, array $data)
    {
        $date = $data['date'];
        $isWorking = (bool)($data['is_working'] ?? true);
        $startTime = $data['start_time'] ?? null;
        $endTime = $data['end_time'] ?? null;

        return DB::transaction(function () use ($profileId, $date, $isWorking, $startTime, $endTime) {
            // Cancel conflicting appointments first
            $this->cancelConflictingAppointmentsForException($profileId, $date, $isWorking, $startTime, $endTime);

            // Save exception record
            $exception = VeterinaryException::updateOrCreate(
                [
                    'veterinary_profile_id' => $profileId,
                    'date' => $date,
                ],
                [
                    'is_working' => $isWorking,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]
            );

            return $exception;
        });
    }

    public function deleteException(int $profileId, int $exceptionId)
    {
        $exception = VeterinaryException::where('veterinary_profile_id', $profileId)
            ->where('id', $exceptionId)
            ->firstOrFail();

        $exception->delete();
        return true;
    }

    public function updateAppointmentStatus(int $appointmentId, string $status, ?string $notes = null)
    {
        if (!in_array($status, ['completed', 'no_show', 'cancelled_by_user', 'cancelled_by_clinic'])) {
            throw new Exception('Invalid appointment status.', 400);
        }

        return DB::transaction(function () use ($appointmentId, $status, $notes) {
            $appointment = Appointment::findOrFail($appointmentId);
            $profile = $appointment->veterinaryProfile;
            $pet = $appointment->pet;

            // Authorization check
            $userId = auth()->id();
            if ($status === 'cancelled_by_user') {
                if ($pet->user_id !== $userId) {
                    throw new Exception('Unauthorized action.', 403);
                }
            } else {
                // Clinic actions (completed, no_show, cancelled_by_clinic)
                if ($profile->user_id !== $userId) {
                    throw new Exception('Unauthorized action.', 403);
                }
            }

            // Backend validation: completed and no_show require that the slot time has already passed!
            if (in_array($status, ['completed', 'no_show'])) {
                $dateTimeStr = $appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->start_time;
                $appointmentDateTime = Carbon::parse($dateTimeStr);
                
                if ($appointmentDateTime->gt(Carbon::now())) {
                    throw new Exception('You cannot mark this appointment as completed or no-show before the scheduled date and time.', 400);
                }
            }

            $appointment->update([
                'status' => $status,
                'notes' => $notes ?? $appointment->notes,
            ]);

            // Notify other party
            if ($status === 'cancelled_by_user') {
                $this->sendNotification(
                    $profile->user_id,
                    'Randevu İptali',
                    "{$pet->name} sahibi {$appointment->appointment_date->format('Y-m-d')} tarihindeki saat {$appointment->start_time} randevusunu iptal etti.",
                    $appointmentId,
                    $status
                );
            } else {
                $title = '';
                $message = '';

                if ($status === 'completed') {
                    $title = 'Randevunuz Tamamlandı';
                    $message = "{$profile->clinic_name} klinikteki randevunuz tamamlandı. Deneyiminizi değerlendirmek için lütfen yorum bırakın.";
                } elseif ($status === 'no_show') {
                    $title = 'Randevuya Katılmadınız';
                    $message = "{$profile->clinic_name} klinikteki randevunuza katılım durumunuz 'Gelinmedi' olarak işaretlendi.";
                } elseif ($status === 'cancelled_by_clinic') {
                    $title = 'Randevunuz İptal Edildi';
                    $message = "{$profile->clinic_name} klinikteki randevunuz klinik tarafından iptal edildi.";
                }

                $this->sendNotification(
                    $pet->user_id,
                    $title,
                    $message,
                    $appointmentId,
                    $status
                );
            }

            return $appointment;
        });
    }

    public function rescheduleAppointment(int $id, array $data)
    {
        $appointment = Appointment::findOrFail($id);
        $pet = $appointment->pet;
        $profile = $appointment->veterinaryProfile;

        if ($pet->user_id !== auth()->id()) {
            throw new Exception('Unauthorized action.', 403);
        }

        if ($appointment->status !== 'confirmed') {
            throw new Exception('Only confirmed appointments can be rescheduled.', 400);
        }

        $newDate = $data['appointment_date'];
        $newStartTime = $data['start_time'];
        $newPetId = $data['pet_id'] ?? $appointment->pet_id;
        $notes = $data['notes'] ?? $appointment->notes;

        $newPet = Pet::findOrFail($newPetId);
        if ($newPet->user_id !== auth()->id()) {
            throw new Exception('Unauthorized pet selection.', 403);
        }

        $availableSlots = $this->getAvailableSlots($profile->id, $newDate);
        $isSlotFree = false;
        $endTime = null;
        foreach ($availableSlots as $slot) {
            if ($slot['start_time'] === $newStartTime) {
                if ($appointment->appointment_date->format('Y-m-d') === $newDate && 
                    Carbon::createFromFormat('H:i:s', $appointment->start_time)->format('H:i') === $newStartTime) {
                    $isSlotFree = true;
                    $endTime = $slot['end_time'];
                    break;
                }
                
                if ($slot['is_available']) {
                    $isSlotFree = true;
                    $endTime = $slot['end_time'];
                    break;
                }
            }
        }

        if (!$isSlotFree) {
            throw new Exception('The selected appointment slot is no longer available.', 400);
        }

        return DB::transaction(function () use ($appointment, $newPetId, $newDate, $newStartTime, $endTime, $notes, $profile, $newPet) {
            $oldDate = $appointment->appointment_date->format('Y-m-d');
            $oldTime = Carbon::createFromFormat('H:i:s', $appointment->start_time)->format('H:i');

            $appointment->update([
                'pet_id' => $newPetId,
                'appointment_date' => $newDate,
                'start_time' => $newStartTime,
                'end_time' => $endTime,
                'notes' => $notes,
            ]);

            $this->sendNotification(
                $profile->user_id,
                'Randevu Güncellendi',
                "{$newPet->name} isimli evcil hayvanın {$oldDate} tarihindeki saat {$oldTime} olan randevusu, {$newDate} tarihi saat {$newStartTime} olarak güncellendi.",
                $appointment->id,
                'confirmed'
            );

            return $appointment;
        });
    }

    protected function cancelConflictingAppointmentsForWeekly(int $profileId, array $availabilities)
    {
        $futureAppointments = Appointment::where('veterinary_profile_id', $profileId)
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['confirmed'])
            ->get();

        $newAvails = [];
        foreach ($availabilities as $a) {
            $newAvails[(int)$a['day_of_week']] = $a;
        }

        foreach ($futureAppointments as $app) {
            $dayOfWeek = $app->appointment_date->dayOfWeek; // 0 = Sunday, 6 = Saturday

            $exception = VeterinaryException::where('veterinary_profile_id', $profileId)
                ->whereDate('date', $app->appointment_date->toDateString())
                ->first();

            if ($exception) {
                continue;
            }

            $appStart = Carbon::createFromFormat('H:i:s', $app->start_time)->format('H:i');
            $appEnd = Carbon::createFromFormat('H:i:s', $app->end_time)->format('H:i');

            if (!isset($newAvails[$dayOfWeek])) {
                $this->cancelAppointment($app);
                continue;
            }

            $avail = $newAvails[$dayOfWeek];
            $availStart = Carbon::parse($avail['start_time'])->format('H:i');
            $availEnd = Carbon::parse($avail['end_time'])->format('H:i');

            if ($appStart < $availStart || $appEnd > $availEnd) {
                $this->cancelAppointment($app);
            }
        }
    }

    protected function cancelConflictingAppointmentsForException(int $profileId, string $date, bool $isWorking, ?string $startTime, ?string $endTime)
    {
        $appointments = Appointment::where('veterinary_profile_id', $profileId)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['confirmed'])
            ->get();

        foreach ($appointments as $app) {
            if (!$isWorking) {
                $this->cancelAppointment($app);
            } else if ($startTime && $endTime) {
                $appStart = Carbon::createFromFormat('H:i:s', $app->start_time)->format('H:i');
                $appEnd = Carbon::createFromFormat('H:i:s', $app->end_time)->format('H:i');

                $exceptStart = Carbon::parse($startTime)->format('H:i');
                $exceptEnd = Carbon::parse($endTime)->format('H:i');

                if ($appStart < $exceptStart || $appEnd > $exceptEnd) {
                    $this->cancelAppointment($app);
                }
            }
        }
    }

    protected function cancelAppointment($appointment)
    {
        $appointment->update(['status' => 'cancelled_by_clinic']);
        $profile = $appointment->veterinaryProfile;
        $pet = $appointment->pet;

        $this->sendNotification(
            $pet->user_id,
            'Randevunuz İptal Edildi',
            "{$profile->clinic_name} klinikteki {$appointment->appointment_date->format('Y-m-d')} tarihindeki randevunuz takvim güncellenmesi nedeniyle iptal edildi.",
            $appointment->id,
            'cancelled_by_clinic'
        );
    }

    protected function sendNotification(int $userId, string $title, string $message, int $appointmentId, string $status)
    {
        try {
            $notification = Notification::create([
                'id' => Str::uuid()->toString(),
                'type' => 'App\\Notifications\\AppointmentNotification',
                'notifiable_type' => 'App\\Modules\\User\\Models\\User',
                'notifiable_id' => $userId,
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'appointment_id' => $appointmentId,
                    'status' => $status,
                ],
                'read_at' => null,
            ]);

            event(new \App\Modules\Veterinary\Events\AppointmentNotificationEvent($userId, $notification));
        } catch (Exception $e) {
            Log::warning("Failed to dispatch appointment notification: " . $e->getMessage());
        }
    }
}

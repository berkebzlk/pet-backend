<?php

namespace App\Modules\Veterinary\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Veterinary\Models\Appointment;
use App\Modules\Veterinary\Models\VeterinaryProfile;
use App\Modules\Veterinary\Payload\Resources\AppointmentResource;
use App\Modules\Veterinary\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {
    }

    public function getAvailableSlots($id, Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        $slots = $this->appointmentService->getAvailableSlots((int)$id, $request->input('date'));
        return ResponseHelper::success($slots);
    }

    public function book(Request $request)
    {
        $request->validate([
            'veterinary_profile_id' => 'required|integer|exists:veterinary_profiles,id',
            'pet_id' => 'required|integer|exists:pets,id',
            'appointment_date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string'
        ]);

        try {
            $appointment = $this->appointmentService->bookAppointment($request->all());
            return ResponseHelper::success(
                new AppointmentResource($appointment),
                HttpStatusEnum::CREATED->value,
                'Randevu başarıyla oluşturuldu.'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), HttpStatusEnum::BAD_REQUEST->value);
        }
    }

    public function reschedule($id, Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'pet_id' => 'sometimes|integer',
            'notes' => 'nullable|string'
        ]);

        try {
            $appointment = $this->appointmentService->rescheduleAppointment((int)$id, $request->all());
            return ResponseHelper::success(
                new AppointmentResource($appointment),
                HttpStatusEnum::OK->value,
                'Randevu başarıyla güncellendi.'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), HttpStatusEnum::BAD_REQUEST->value);
        }
    }

    public function myAppointments()
    {
        $user = auth()->user();
        $appointments = Appointment::whereIn('pet_id', $user->pets->pluck('id'))
            ->with(['pet', 'veterinaryProfile'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return ResponseHelper::success(AppointmentResource::collection($appointments));
    }

    public function clinicAppointments(Request $request)
    {
        $profile = VeterinaryProfile::where('user_id', auth()->id())->first();
        if (!$profile) {
            return ResponseHelper::error('Klinik profili bulunamadı.', HttpStatusEnum::NOT_FOUND->value);
        }

        $query = Appointment::where('veterinary_profile_id', $profile->id)
            ->with(['pet', 'veterinaryProfile']);

        // Time filter
        $todayStr = now()->toDateString();
        $timeFilter = $request->query('time_filter', 'all');
        if ($timeFilter === 'today') {
            $query->where('appointment_date', $todayStr);
        } elseif ($timeFilter === 'upcoming') {
            $query->where('appointment_date', '>', $todayStr);
        } elseif ($timeFilter === 'past') {
            $query->where('appointment_date', '<', $todayStr);
        }

        // Status filter
        $status = $request->query('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search query (pet name, owner username, breed, notes)
        $search = $request->query('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('pet', function ($petQuery) use ($search) {
                    $petQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('breed', 'like', "%{$search}%");
                })->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Order
        $query->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc');

        // Pagination
        $perPage = (int)$request->query('per_page', 10);
        $appointments = $query->paginate($perPage);

        return ResponseHelper::success([
            'items' => AppointmentResource::collection($appointments->items()),
            'meta' => [
                'current_page' => $appointments->currentPage(),
                'last_page' => $appointments->lastPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
            ]
        ]);
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:completed,no_show,cancelled_by_user,cancelled_by_clinic',
            'notes' => 'nullable|string'
        ]);

        try {
            $appointment = $this->appointmentService->updateAppointmentStatus((int)$id, $request->status, $request->notes);
            return ResponseHelper::success(
                new AppointmentResource($appointment),
                HttpStatusEnum::OK->value,
                'Randevu durumu güncellendi.'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), HttpStatusEnum::BAD_REQUEST->value);
        }
    }

    public function updateWeeklyAvailability(Request $request)
    {
        $profile = VeterinaryProfile::where('user_id', auth()->id())->first();
        if (!$profile) {
            return ResponseHelper::error('Klinik profili bulunamadı.', HttpStatusEnum::NOT_FOUND->value);
        }

        $request->validate([
            'availabilities' => 'required|array',
            'availabilities.*.day_of_week' => 'required|integer|between:0,6',
            'availabilities.*.start_time' => 'required|date_format:H:i',
            'availabilities.*.end_time' => 'required|date_format:H:i',
            'availabilities.*.slot_duration' => 'nullable|integer|min:5|max:120',
        ]);

        try {
            $this->appointmentService->updateWeeklyAvailability($profile->id, $request->input('availabilities'));
            return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Haftalık çalışma saatleri güncellendi.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), HttpStatusEnum::BAD_REQUEST->value);
        }
    }

    public function saveException(Request $request)
    {
        $profile = VeterinaryProfile::where('user_id', auth()->id())->first();
        if (!$profile) {
            return ResponseHelper::error('Klinik profili bulunamadı.', HttpStatusEnum::NOT_FOUND->value);
        }

        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'is_working' => 'required|boolean',
            'start_time' => 'nullable|required_if:is_working,true|date_format:H:i',
            'end_time' => 'nullable|required_if:is_working,true|date_format:H:i',
        ]);

        try {
            $exception = $this->appointmentService->saveException($profile->id, $request->all());
            return ResponseHelper::success($exception, HttpStatusEnum::OK->value, 'Takvim istisnası kaydedildi.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), HttpStatusEnum::BAD_REQUEST->value);
        }
    }

    public function deleteException($id)
    {
        $profile = VeterinaryProfile::where('user_id', auth()->id())->first();
        if (!$profile) {
            return ResponseHelper::error('Klinik profili bulunamadı.', HttpStatusEnum::NOT_FOUND->value);
        }

        try {
            $this->appointmentService->deleteException($profile->id, (int)$id);
            return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Takvim istisnası silindi.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), HttpStatusEnum::BAD_REQUEST->value);
        }
    }
}

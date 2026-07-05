<?php

namespace App\Modules\Veterinary\Payload\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VeterinaryProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'clinicName' => $this->clinic_name,
            'city' => $this->city,
            'phone' => $this->phone,
            'website' => $this->website,
            'about' => $this->about,
            'specialties' => $this->specialties ?? [],
            'profilePhoto' => $this->profile_photo ? asset(Storage::url($this->profile_photo)) : null,
            'coverPhoto' => $this->cover_photo ? asset(Storage::url($this->cover_photo)) : null,
            'averageRating' => (float) ($this->average_rating ?? 0),
            'reviewsCount' => (int) ($this->reviews_count ?? 0),
            'availabilities' => $this->availabilities ? $this->availabilities->map(function ($avail) {
                return [
                    'id' => $avail->id,
                    'dayOfWeek' => $avail->day_of_week,
                    'startTime' => substr($avail->start_time, 0, 5),
                    'endTime' => substr($avail->end_time, 0, 5),
                    'slotDuration' => $avail->slot_duration,
                ];
            }) : [],
            'exceptions' => $this->exceptions ? $this->exceptions->map(function ($except) {
                return [
                    'id' => $except->id,
                    'date' => $except->date instanceof \Carbon\Carbon ? $except->date->format('Y-m-d') : substr($except->date, 0, 10),
                    'isWorking' => (bool)$except->is_working,
                    'startTime' => $except->start_time ? substr($except->start_time, 0, 5) : null,
                    'endTime' => $except->end_time ? substr($except->end_time, 0, 5) : null,
                ];
            }) : [],
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Modules\Veterinary\Payload\Resources;

use App\Modules\Pet\Payload\Resources\PetResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'veterinary_profile_id' => $this->veterinary_profile_id,
            'pet_id' => $this->pet_id,
            'appointment_date' => $this->appointment_date->format('Y-m-d'),
            'start_time' => substr($this->start_time, 0, 5),
            'end_time' => substr($this->end_time, 0, 5),
            'status' => $this->status,
            'notes' => $this->notes,
            'pet' => $this->pet ? new PetResource($this->pet) : null,
            'veterinary_profile' => $this->veterinaryProfile ? new VeterinaryProfileResource($this->veterinaryProfile) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

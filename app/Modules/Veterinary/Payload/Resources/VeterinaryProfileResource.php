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
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}

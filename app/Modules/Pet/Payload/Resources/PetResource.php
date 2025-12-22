<?php

namespace App\Modules\Pet\Payload\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'breed' => $this->breed,
            'gender' => $this->gender,
            'birthDate' => $this->birthdate->format('Y-m-d'),
            'age' => $this->birthdate->age,
            'weight' => (float) $this->weight,
            'isNeutered' => (boolean) $this->is_neutered,
            'bio' => $this->bio,
            'image' => $this->image ? url('storage/' . $this->image) : null,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Modules\Pet\Payload\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'username' => $this->username,
            'postsCount' => (int) $this->posts_count,
            'matchCount' => (int) ($this->initiated_matches_count + $this->received_matches_count),
            'likesCount' => (int) $this->received_likes_count,
            'image' => $this->image ? asset(Storage::url($this->image)) : null,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}

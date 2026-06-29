<?php

namespace App\Modules\Veterinary\Payload\Resources;

use App\Modules\Pet\Payload\Resources\PetResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VeterinaryReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'createdAt' => $this->created_at,
            'pet' => $this->pet ? new PetResource($this->pet) : null,
        ];
    }
}

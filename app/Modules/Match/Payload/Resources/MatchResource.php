<?php

namespace App\Modules\Match\Payload\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'initiator_pet_id' => $this->initiator_pet_id,
            'target_pet_id' => $this->target_pet_id,
            'initiator_pet' => new \App\Modules\Pet\Payload\Resources\PetResource($this->whenLoaded('initiatorPet')),
            'target_pet' => new \App\Modules\Pet\Payload\Resources\PetResource($this->whenLoaded('targetPet')),
            'status' => strtolower($this->status->name),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

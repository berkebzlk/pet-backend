<?php

namespace App\Modules\Message\Payload\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Pet\Payload\Resources\PetResource;

class MessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sender_pet_id' => $this->sender_pet_id,
            'receiver_pet_id' => $this->receiver_pet_id,
            'content' => $this->content,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sender' => new PetResource($this->whenLoaded('sender')),
            'receiver' => new PetResource($this->whenLoaded('receiver')),
        ];
    }
}

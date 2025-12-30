<?php

namespace App\Modules\Post\Payload\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'pet_id' => $this->pet_id,
            'image_url' => asset(Storage::url($this->image_url)),
            'description' => $this->description,
            'created_at' => $this->created_at,
            'pet' => $this->whenLoaded('pet', function () {
                return [
                    'id' => $this->pet->id,
                    'name' => $this->pet->name,
                    'username' => $this->pet->username,
                    'image' => $this->pet->image ? asset(Storage::url($this->pet->image)) : null,
                ];
            }),
            'likes_count' => $this->likes_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,
            'is_liked' => $this->isLikedBy(auth()->user()),
            'is_saved' => $this->isSavedBy(auth()->user()),
        ];
    }
}

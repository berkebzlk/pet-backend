<?php

namespace App\Modules\Post\Payload\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'pet_id' => $this->pet_id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'pet' => $this->pet ? [
                'id' => $this->pet->id,
                'name' => $this->pet->name,
                'username' => $this->pet->username,
                'image' => $this->pet->image ? asset(Storage::url($this->pet->image)) : null,
            ] : null,
        ];
    }
}

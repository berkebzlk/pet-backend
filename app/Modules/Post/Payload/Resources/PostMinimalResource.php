<?php

namespace App\Modules\Post\Payload\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostMinimalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_url' => asset(Storage::url($this->image_url)),
        ];
    }
}

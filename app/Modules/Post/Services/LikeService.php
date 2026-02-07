<?php

namespace App\Modules\Post\Services;

use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Post\Models\Like;

class LikeService extends BaseEloquentService
{
    public function __construct(
        protected Like $like
    ) {
        parent::__construct($like);
    }

    public function like(int $postId, int $petId)
    {
        auth()->user()->pets()->findOrFail($petId);

        return $this->like->firstOrCreate([
            'post_id' => $postId,
            'pet_id' => $petId
        ]);
    }

    public function unlike(int $postId, int $petId)
    {
        auth()->user()->pets()->findOrFail($petId);

        return $this->like->where('post_id', $postId)
            ->where('pet_id', $petId)
            ->delete();
    }
}

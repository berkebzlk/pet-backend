<?php

namespace App\Modules\Post\Services;

use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Post\Models\SavedPost;

class SavedPostService extends BaseEloquentService
{
    public function __construct(
        protected SavedPost $savedPost
    ) {
        parent::__construct($savedPost);
    }

    public function save(int $postId, int $petId)
    {
        auth()->user()->pets()->findOrFail($petId);

        return $this->savedPost->firstOrCreate([
            'post_id' => $postId,
            'pet_id' => $petId
        ]);
    }

    public function unsave(int $postId, int $petId)
    {
        auth()->user()->pets()->findOrFail($petId);

        return $this->savedPost->where('post_id', $postId)
            ->where('pet_id', $petId)
            ->delete();
    }
}

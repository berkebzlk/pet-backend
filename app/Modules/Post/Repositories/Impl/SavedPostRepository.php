<?php

namespace App\Modules\Post\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Post\Models\SavedPost;
use App\Modules\Post\Repositories\SavedPostRepositoryInterface;

class SavedPostRepository extends BaseRepositoryEloquent implements SavedPostRepositoryInterface
{
    public function __construct(SavedPost $model)
    {
        parent::__construct($model);
    }

    public function save(int $postId, int $userId)
    {
        $this->model->firstOrCreate([
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }

    public function unsave(int $postId, int $userId)
    {
        $this->model->where('post_id', $postId)
            ->where('user_id', $userId)
            ->delete();
    }
}

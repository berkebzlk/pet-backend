<?php

namespace App\Modules\Post\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Post\Models\Like;
use App\Modules\Post\Repositories\LikeRepositoryInterface;

class LikeRepository extends BaseRepositoryEloquent implements LikeRepositoryInterface
{
    public function __construct(Like $model)
    {
        parent::__construct($model);
    }

    public function like(int $postId, int $petId)
    {
        $this->model->firstOrCreate([
            'post_id' => $postId,
            'pet_id' => $petId
        ]);
    }

    public function unlike(int $postId, int $petId)
    {
        $this->model->where('post_id', $postId)
            ->where('pet_id', $petId)
            ->delete();
    }
}

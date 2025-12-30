<?php

namespace App\Modules\Post\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Repositories\PostRepositoryInterface;

class PostRepository extends BaseRepositoryEloquent implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function getFeed()
    {
        return $this->model->with('pet')->latest()->get();
    }

    public function getByPetId(int $petId)
    {
        return $this->model->where('pet_id', $petId)->with('pet')->latest()->get();
    }
}

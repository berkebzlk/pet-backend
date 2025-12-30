<?php

namespace App\Modules\Post\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Post\Models\Comment;
use App\Modules\Post\Repositories\CommentRepositoryInterface;

class CommentRepository extends BaseRepositoryEloquent implements CommentRepositoryInterface
{
    public function __construct(Comment $model)
    {
        parent::__construct($model);
    }

    public function getByPostIdQuery(int $postId)
    {
        return $this->model->where('post_id', $postId)->with('pet');
    }
}

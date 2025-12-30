<?php

namespace App\Modules\Post\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface CommentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByPostIdQuery(int $postId);
}

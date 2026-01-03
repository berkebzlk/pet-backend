<?php

namespace App\Modules\Post\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface LikeRepositoryInterface extends BaseRepositoryInterface
{
    public function like(int $postId, int $petId);
    public function unlike(int $postId, int $petId);
}

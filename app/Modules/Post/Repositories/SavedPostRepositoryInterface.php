<?php

namespace App\Modules\Post\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface SavedPostRepositoryInterface extends BaseRepositoryInterface
{
    public function save(int $postId, int $userId);
    public function unsave(int $postId, int $userId);
}

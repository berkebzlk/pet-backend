<?php

namespace App\Modules\Post\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface SavedPostRepositoryInterface extends BaseRepositoryInterface
{
    public function save(int $postId, int $petId);
    public function unsave(int $postId, int $petId);
}

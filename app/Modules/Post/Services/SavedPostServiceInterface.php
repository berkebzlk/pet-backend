<?php

namespace App\Modules\Post\Services;

use App\Modules\Core\Services\BaseServiceInterface;

interface SavedPostServiceInterface extends BaseServiceInterface
{
    public function save(int $postId, int $userId);
    public function unsave(int $postId, int $userId);
}

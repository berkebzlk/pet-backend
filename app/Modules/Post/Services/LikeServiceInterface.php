<?php

namespace App\Modules\Post\Services;

use App\Modules\Core\Services\BaseServiceInterface;

interface LikeServiceInterface extends BaseServiceInterface
{
    public function like(int $postId, int $userId);
    public function unlike(int $postId, int $userId);
}

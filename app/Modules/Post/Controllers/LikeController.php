<?php

namespace App\Modules\Post\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Post\Services\LikeServiceInterface;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct(
        private LikeServiceInterface $likeService
    ) {
    }

    public function like($postId)
    {
        $this->likeService->like($postId, Auth::id());
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Post liked');
    }

    public function unlike($postId)
    {
        $this->likeService->unlike($postId, Auth::id());
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Post unliked');
    }
}

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
        $request = request();
        $request->validate(['pet_id' => 'required|integer|exists:pets,id']);

        $this->likeService->like($postId, $request->pet_id);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Post liked');
    }

    public function unlike($postId)
    {
        $request = request();
        $request->validate(['pet_id' => 'required|integer|exists:pets,id']);

        $this->likeService->unlike($postId, $request->pet_id);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Post unliked');
    }
}

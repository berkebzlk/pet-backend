<?php

namespace App\Modules\Post\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Post\Services\SavedPostServiceInterface;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class SavedPostController extends Controller
{
    public function __construct(
        private SavedPostServiceInterface $savedPostService
    ) {
    }

    public function save($postId)
    {
        $this->savedPostService->save($postId, Auth::id());
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Post saved');
    }

    public function unsave($postId)
    {
        $this->savedPostService->unsave($postId, Auth::id());
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Post unsaved');
    }
}

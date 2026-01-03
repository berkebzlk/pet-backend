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
        $request = request();
        $request->validate(['pet_id' => 'required|integer|exists:pets,id']);

        $this->savedPostService->save($postId, $request->pet_id);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, trans('post::post.saved'));
    }

    public function unsave($postId)
    {
        $request = request();
        $request->validate(['pet_id' => 'required|integer|exists:pets,id']);

        $this->savedPostService->unsave($postId, $request->pet_id);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, trans('post::post.unsaved'));
    }
}

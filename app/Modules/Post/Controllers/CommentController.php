<?php

namespace App\Modules\Post\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Post\Payload\Requests\StoreCommentRequest;
use App\Modules\Post\Services\CommentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct(
        private CommentServiceInterface $commentService
    ) {
    }

    public function store(StoreCommentRequest $request, $postId)
    {
        $data = [
            'post_id' => $postId,
            'pet_id' => $request->validated()['pet_id'],
            'content' => $request->validated()['content']
        ];

        $comment = $this->commentService->store($data);
        return ResponseHelper::success($comment, HttpStatusEnum::CREATED->value, 'Comment added');
    }

    public function destroy($postId, $commentId)
    {
        $this->commentService->deleteFromPost($postId, $commentId, Auth::user()->id);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, 'Comment deleted');
    }
}

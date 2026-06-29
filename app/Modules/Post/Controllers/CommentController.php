<?php

namespace App\Modules\Post\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Core\Payload\Resources\PaginatedResource;
use App\Modules\Post\Payload\Requests\StoreCommentRequest;
use App\Modules\Post\Payload\Resources\CommentResource;
use App\Modules\Post\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct(
        private CommentService $commentService
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
        $comment->load('pet');
        return ResponseHelper::success(new CommentResource($comment), HttpStatusEnum::CREATED->value, trans('post::post.comment_added'));
    }

    public function destroy($postId, $commentId)
    {
        $this->commentService->deleteFromPost($postId, $commentId, Auth::user()->id);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value, trans('post::post.comment_deleted'));
    }

    public function getCommentsByPostId($postId)
    {
        $comments = $this->commentService->getCommentsByPostId($postId, request()->all());
        return ResponseHelper::success(new PaginatedResource($comments, CommentResource::class));
    }
}

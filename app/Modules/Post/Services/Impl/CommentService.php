<?php

namespace App\Modules\Post\Services\Impl;

use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\Post\Repositories\CommentRepositoryInterface;
use App\Modules\Post\Services\CommentServiceInterface;
use App\Modules\User\Models\User;
use Exception;
use Illuminate\Support\Facades\Gate;

class CommentService extends BaseService implements CommentServiceInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
    ) {
        parent::__construct($commentRepository);
    }

    public function addComment(int $postId, int $petId, string $content)
    {
        // Verify pet ownership
        $pet = \App\Modules\Pet\Models\Pet::findOrFail($petId);
        if ($pet->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            throw new Exception(__('post::comment.unauthorized_create'));
        }

        return $this->commentRepository->create([
            'post_id' => $postId,
            'pet_id' => $petId,
            'content' => $content
        ]);
    }

    public function deleteFromPost(int $postId, int $commentId, int $userId)
    {
        $comment = $this->show($commentId);

        if ($comment->post_id !== $postId) {
            throw new Exception(__('post::comment.invalid_post'));
        }

        if (!Gate::forUser(User::find($userId))->allows('delete', $comment)) {
            throw new Exception(__('post::comment.unauthorized_delete'));
        }

        return $this->commentRepository->delete($commentId);
    }
}

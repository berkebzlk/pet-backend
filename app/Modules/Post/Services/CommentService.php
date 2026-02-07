<?php

namespace App\Modules\Post\Services;

use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Post\Models\Comment;
use App\Modules\User\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentService extends BaseEloquentService
{
    public function __construct(
        protected Comment $comment
    ) {
        parent::__construct($comment);
    }

    public function addComment(int $postId, int $petId, string $content)
    {
        // Verify pet ownership
        $pet = \App\Modules\Pet\Models\Pet::findOrFail($petId);
        if ($pet->user_id !== Auth::id()) {
            throw new Exception(__('post::comment.unauthorized_create'));
        }

        return $this->comment->create([
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

        return $comment->delete();
    }

    public function getCommentsByPostId(int $postId, array $requestData)
    {
        $query = $this->comment->where('post_id', $postId)->with('pet');

        // Default sort for comments
        if (!isset($requestData['sortBy'])) {
            $requestData['sortBy'] = json_encode(['created_at' => 'desc']);
        }

        return $this->index($requestData, $query);
    }
}

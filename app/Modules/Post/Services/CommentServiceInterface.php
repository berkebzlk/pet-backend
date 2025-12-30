<?php

namespace App\Modules\Post\Services;

use App\Modules\Core\Services\BaseServiceInterface;

interface CommentServiceInterface extends BaseServiceInterface
{
    public function addComment(int $postId, int $petId, string $content);
    public function deleteFromPost(int $postId, int $commentId, int $userId);
    public function getCommentsByPostId(int $postId, array $requestData);
}

<?php

namespace App\Modules\Post\Policies;

use App\Modules\Post\Models\Comment;
use App\Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Modules\User\Models\User  $user
     * @param  \App\Modules\Post\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Comment $comment)
    {
        return $user->id === $comment->pet->user_id;
    }
}

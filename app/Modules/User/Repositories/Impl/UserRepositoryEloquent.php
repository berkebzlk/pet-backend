<?php

namespace App\Modules\User\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepositoryInterface;

class UserRepositoryEloquent extends BaseRepositoryEloquent implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function revokeAllTokens(int $userId)
    {
        $user = $this->findById($userId);
        if ($user) {
            return false;
        }

        $user->tokens()->delete();
        return true;
    }
}

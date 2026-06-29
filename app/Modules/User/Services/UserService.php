<?php

namespace App\Modules\User\Services;

use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\User\Models\User;

class UserService extends BaseEloquentService
{
    public function __construct(
        protected User $user
    ) {
        parent::__construct($user);
    }

    public function delete(int $id)
    {
        $this->revokeAllTokens($id);
        return parent::delete($id);
    }

    public function revokeAllTokens(int $userId)
    {
        $this->user->where('id', $userId)->tokens()->delete();
        return true;
    }

    public function searchUsers(string $searchText, array $requestData)
    {
        $query = $this->user->newQuery()->where('name', 'like', "%{$searchText}%");

        return $this->index($requestData, $query);
    }
}

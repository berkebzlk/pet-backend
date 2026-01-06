<?php

namespace App\Modules\User\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function revokeAllTokens(int $userId);
    public function getSearchUsersQuery(string $searchText);
}
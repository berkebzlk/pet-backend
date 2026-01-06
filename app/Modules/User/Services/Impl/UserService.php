<?php

namespace App\Modules\User\Services\Impl;

use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepositoryInterface;
use App\Modules\User\Services\UserServiceInterface;

class UserService extends BaseService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
        parent::__construct($userRepository);
    }

    public function delete(int $id)
    {
        $this->userRepository->revokeAllTokens($id);
        return parent::delete($id);
    }

    public function searchUsers(string $searchText, array $requestData)
    {
        $query = $this->userRepository->getSearchUsersQuery($searchText);
        return $this->index($requestData, $query);
    }
}

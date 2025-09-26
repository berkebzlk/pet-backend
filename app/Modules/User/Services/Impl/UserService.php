<?php

namespace App\Modules\User\Services\Impl;

use App\Modules\User\Repositories\UserRepositoryInterface;
use App\Modules\User\Services\UserServiceInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}
    /**
     * Get all users
     */
    public function index()
    {
        return $this->userRepository->findAll();
    }

    /**
     * Get single user
     */
    public function show(int $id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Create new user
     */
    public function store(array $data)
    {
        return $this->userRepository->create($data);
    }

    /**
     * Update user
     */
    public function update(int $id, array $data)
    {
        $user = $this->userRepository->update($id, $data);
        return $user;
    }

    /**
     * Delete user
     */
    public function delete(int $id)
    {
        // Revoke all tokens before deleting
        $this->userRepository->revokeAllTokens($id);
        return $this->userRepository->delete($id);
    }

    /**
     * Get current user profile
     */
    public function getCurrentUser(int $id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Update current user profile
     */
    public function updateCurrentUser(int $id, array $data)
    {
        $user = $this->userRepository->update($id, $data);
        return $user;
    }
}

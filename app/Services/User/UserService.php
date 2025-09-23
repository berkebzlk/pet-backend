<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    /**
     * Get all users
     */
    public function index(): Collection
    {
        return User::get();
    }

    /**
     * Get single user
     */
    public function show(User $user): User
    {
        return $user;
    }

    /**
     * Create new user
     */
    public function store(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update user
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    /**
     * Delete user
     */
    public function destroy(User $user): bool
    {
        // Revoke all tokens before deleting
        $user->tokens()->delete();
        
        return $user->delete();
    }

    /**
     * Get current user profile
     */
    public function getCurrentUser(User $user): User
    {
        return $user;
    }

    /**
     * Update current user profile
     */
    public function updateCurrentUser(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }
}

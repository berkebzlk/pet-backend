<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Authenticate user and return token
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw new \Exception('Invalid credentials', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->accessToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout user
     */
    public function logout(User $user): bool
    {
        $user->currentAccessToken()->revoke();
        return true;
    }

    /**
     * Register new user
     */
    public function register(array $data): array
    {
        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth-token')->accessToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(User $user): User
    {
        return $user;
    }

    /**
     * Refresh user token
     */
    public function refreshToken(User $user): array
    {
        // Revoke current token
        $user->currentAccessToken()->revoke();
        
        // Create new token
        $token = $user->createToken('auth-token')->accessToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}

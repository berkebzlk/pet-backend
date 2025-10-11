<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

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

        // Use Password Grant to obtain both access and refresh tokens
        $response = Http::asForm()->post(route('passport.token'), [
            'grant_type' => 'password',
            'client_id' => env('PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSWORD_CLIENT_SECRET'),
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'scope' => '*',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Unable to obtain OAuth tokens', 500);
        }

        $tokenPayload = $response->json();

        return [
            'user' => $user,
            'access_token' => $tokenPayload['access_token'] ?? null,
            'refresh_token' => $tokenPayload['refresh_token'] ?? null,
            'token_type' => $tokenPayload['token_type'] ?? 'Bearer',
            'expires_in' => $tokenPayload['expires_in'] ?? null,
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
    public function register(array $data): bool
    {
        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        return true;
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(User $user): User
    {
        return $user;
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshTokenWithRefreshToken(string $refreshToken): array
    {
        // Find the refresh token in database
        $refreshTokenModel = \Laravel\Passport\RefreshToken::where('id', $refreshToken)->first();

        if (!$refreshTokenModel || $refreshTokenModel->revoked) {
            throw new \Exception('Invalid refresh token', 401);
        }

        // Get the access token associated with this refresh token
        $accessToken = $refreshTokenModel->accessToken;

        if ($accessToken->revoked) {
            throw new \Exception('Access token is revoked', 401);
        }

        // Get the user
        $user = $accessToken->user;

        // Revoke the old access token and refresh token
        $accessToken->revoke();
        $refreshTokenModel->revoke();

        // Create new tokens
        $tokenResult = $user->createToken('auth-token');

        return [
            'user' => $user,
            'access_token' => $tokenResult->accessToken,
            'refresh_token' => $tokenResult->refreshToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
        ];
    }

    /**
     * Refresh user token (legacy method - requires authentication)
     */
    public function refreshToken(User $user): array
    {
        // Revoke current token
        $user->currentAccessToken()->revoke();

        // Create new token
        $tokenResult = $user->createToken('auth-token');

        return [
            'user' => $user,
            'access_token' => $tokenResult->accessToken,
            'refresh_token' => $tokenResult->refreshToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
        ];
    }
}

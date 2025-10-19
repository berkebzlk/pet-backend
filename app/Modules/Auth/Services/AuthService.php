<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Token;

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
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET'),
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'scope' => '*',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Unable to obtain OAuth tokens', 500);
        }

        $responseData = $response->json();

        $parts = explode('.', $responseData['access_token']);

        $payloadB64 = strtr($parts[1], '-_', '+/');
        $payloadB64 .= str_repeat('=', (4 - strlen($payloadB64) % 4) % 4);

        $payloadJson = base64_decode($payloadB64, true);

        $payload = json_decode($payloadJson, true);

        // revoke old tokens (currently this system supports login from only one device)
        Token::where('user_id', $user->id)
            ->where('id', '!=', $payload['jti'])
            ->where('revoked', 0)
            ->update(['revoked' => true]);

        return [
            'user' => $user,
            'access_token' => $responseData['access_token'],
            'refresh_token' => $responseData['refresh_token'] ?? null,
            'token_type' => $responseData['token_type'] ?? 'Bearer',
            'expires_in' => $responseData['expires_in'] ?? null,
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

    public function refresh(string $refreshToken): array
    {
        $response = Http::asForm()->post(route('passport.token'), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET'),
            'scope' => '*',
        ]);
        dd($response->json());

        if (!$response->successful()) {
            throw new \Exception('Unable to refresh access token', $response->status());
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'token_type' => $data['token_type'] ?? 'Bearer',
            'expires_in' => $data['expires_in'] ?? null,
        ];
    }
}

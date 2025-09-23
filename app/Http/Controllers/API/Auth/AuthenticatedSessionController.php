<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\HttpStatusEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Login a user.
     *
     * @return JsonResponse|mixed
     */
    public function store(LoginRequest $request): JsonResponse
    {

        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'status' => StatusEnum::ERROR->value,
                'message' => 'Invalid credentials',
            ], HttpStatusEnum::UNAUTHORIZED->value);
        }

        $token = $request->user()->createToken('auth-token')->accessToken;

        return response()->json([
            'status' => StatusEnum::SUCCESS->value,
            'message' => 'Login successful',
            'data' => [
                'user' => $request->user(),
                'token' => $token,
            ],
        ], HttpStatusEnum::SUCCESS->value);
    }

    /**
     * Logout a user.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->revoke();

        return response()->json([
            'status' => StatusEnum::SUCCESS->value,
            'message' => 'Logout successful',
        ], HttpStatusEnum::SUCCESS->value);
    }
}

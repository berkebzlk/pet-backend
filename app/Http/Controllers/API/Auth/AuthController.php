<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return ResponseHelper::success(new AuthResource($result), 200, __('auth.login_successful'));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ResponseHelper::success(null, 200, __('auth.logout_successful'));
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return ResponseHelper::success($result, 201, __('auth.register_successful'));
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->getCurrentUser($request->user());

        return ResponseHelper::success($user);
    }

    public function refresh(Request $request): JsonResponse
    {
        $result = $this->authService->refreshToken($request->user());

        return ResponseHelper::success($result, 200, __('auth.token_refreshed'));
    }
}

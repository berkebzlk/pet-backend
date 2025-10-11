<?php

namespace App\Modules\Auth\Controllers;

use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\AuthResource;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Core\Helpers\ResponseHelper;
use Auth;
use Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request)
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

        return ResponseHelper::success(null, 201, __('auth.register_successful'));
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->getCurrentUser($request->user());

        return ResponseHelper::success($user);
    }

    public function refresh(Request $request): JsonResponse
    {
        $result = $this->authService->refreshToken($request->user());

        return ResponseHelper::success(new AuthResource($result), 200, __('auth.token_refreshed'));
    }

    public function refreshWithToken(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        $result = $this->authService->refreshTokenWithRefreshToken($request->refresh_token);

        return ResponseHelper::success(new AuthResource($result), 200, __('auth.token_refreshed'));
    }
}

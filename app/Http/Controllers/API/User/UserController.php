<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\API\User\StoreUserRequest;
use App\Http\Requests\API\User\UpdateUserRequest;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Display a listing of users
     */
    public function index(): JsonResponse
    {
        $users = $this->userService->index();

        return ResponseHelper::success($users);
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->store($request->validated());

        return ResponseHelper::success($user, 201, __('user.created_successfully'));
    }

    /**
     * Display the specified user
     */
    public function show(User $user): JsonResponse
    {
        $user = $this->userService->show($user);

        return ResponseHelper::success($user);
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return ResponseHelper::success($user, 200, __('user.updated_successfully'));
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->destroy($user);

        return ResponseHelper::success(null, 200, __('user.deleted_successfully'));
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $this->userService->getCurrentUser($request->user());

        return ResponseHelper::success($user);
    }

    /**
     * Update current user profile
     */
    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $user = $this->userService->updateCurrentUser($request->user(), $request->validated());

        return ResponseHelper::success($user, 200, __('user.profile_updated_successfully'));
    }
}

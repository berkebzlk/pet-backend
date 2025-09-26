<?php

namespace App\Modules\User\Controllers;

use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Services\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    /**
     * Display a listing of users
     */
    public function index(): JsonResponse
    {
        $users = $this->userService->index();

        return ResponseHelper::success(UserResource::collection($users));
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->store($request->validated());

        return ResponseHelper::success(new UserResource($user), 201, __('user.created_successfully'));
    }

    /**
     * Display the specified user
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->show($id);

        return ResponseHelper::success($user);
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->update($id, $request->validated());

        return ResponseHelper::success($user, 200, __('user.updated_successfully'));
    }

    /**
     * Delete the specified user
     */
    public function delete(int $id): JsonResponse
    {
        $this->userService->delete($id);

        return ResponseHelper::success(null, 200, __('user.deleted_successfully'));
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $this->userService->getCurrentUser($request->user()->id);

        return ResponseHelper::success($user);
    }

    /**
     * Update current user profile
     */
    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $user = $this->userService->updateCurrentUser($request->user()->id, $request->validated());

        return ResponseHelper::success($user, 200, __('user.profile_updated_successfully'));
    }
}

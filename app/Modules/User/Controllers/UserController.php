<?php

namespace App\Modules\User\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\User\Payload\Requests\StoreUserRequest;
use App\Modules\User\Payload\Requests\UpdateUserRequest;
use App\Modules\User\Payload\Resources\UserResource;
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

        return ResponseHelper::success(new UserResource($user), HttpStatusEnum::CREATED->value, __('crud.created', ['attribute' => $this->userService->getModelName()]));
    }

    /**
     * Display the specified user
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->show($id);

        return ResponseHelper::success(new UserResource($user));
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->update($id, $request->validated());

        return ResponseHelper::success(new UserResource($user), HttpStatusEnum::OK->value, __('crud.updated', ['attribute' => $this->userService->getModelName()]));
    }

    /**
     * Delete the specified user
     */
    public function delete(int $id): JsonResponse
    {
        $this->userService->delete($id);

        return ResponseHelper::success(null, HttpStatusEnum::OK->value, __('crud.deleted', ['attribute' => $this->userService->getModelName()]));
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $this->userService->show($request->user()->id);

        return ResponseHelper::success(new UserResource($user));
    }
}

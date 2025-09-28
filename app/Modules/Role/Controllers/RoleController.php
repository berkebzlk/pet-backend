<?php

namespace App\Modules\Role\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Role\Payload\Requests\StoreRoleRequest;
use App\Modules\Role\Payload\Requests\UpdateRoleRequest;
use App\Modules\Role\Payload\Resources\RoleResource;
use App\Modules\Role\Services\RoleServiceInterface;
use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    public function __construct(
        private RoleServiceInterface $roleService
    ) {}

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->store($request->validated());

        return ResponseHelper::success(new RoleResource($role), HttpStatusEnum::CREATED->value, __('crud.created', ['attribute' => $this->roleService->getModelName()]));
    }
    
    public function index()
    {
        $roles = $this->roleService->index();

        return ResponseHelper::success(RoleResource::collection($roles));
    }

    public function show(int $id)
    {
        $role = $this->roleService->show($id);

        return ResponseHelper::success(new RoleResource($role));
    }

    public function update(UpdateRoleRequest $request, int $id)
    {
        $role = $this->roleService->update($id, $request->validated());

        return ResponseHelper::success(new RoleResource($role), HttpStatusEnum::OK->value, __('role::role.updated_successfully'));
    }

    public function delete(int $id)
    {
        $this->roleService->delete($id);

        return ResponseHelper::success(null, HttpStatusEnum::OK->value, __('role::role.deleted_successfully'));
    }
}
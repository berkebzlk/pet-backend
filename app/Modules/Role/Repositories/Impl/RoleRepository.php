<?php

namespace App\Modules\Role\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Role\Models\Role;
use App\Modules\Role\Repositories\RoleRepositoryInterface;

class RoleRepository extends BaseRepositoryEloquent implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}
<?php

namespace App\Modules\Role\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Role\Repositories\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepositoryEloquent implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}
<?php

namespace App\Modules\Role\Services;

use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Role\Models\Role;

class RoleService extends BaseEloquentService
{
    public function __construct(
        protected Role $role
    ) {
        parent::__construct($role);
    }
}

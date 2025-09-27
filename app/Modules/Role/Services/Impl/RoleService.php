<?php

namespace App\Modules\Role\Services\Impl;

use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\Role\Repositories\RoleRepositoryInterface;
use App\Modules\Role\Services\RoleServiceInterface;

class RoleService extends BaseService implements RoleServiceInterface
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository
    ) {
        parent::__construct($roleRepository);
    }
}
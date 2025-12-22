<?php

namespace App\Modules\Pet\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Pet\Models\Pet;
use App\Modules\Pet\Repositories\PetRepositoryInterface;

class PetRepository extends BaseRepositoryEloquent implements PetRepositoryInterface
{
    public function __construct(Pet $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace App\Modules\Match\Repositories\Impl;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Match\Models\PetMatch;
use App\Modules\Match\Repositories\MatchRepositoryInterface;

class MatchRepository extends BaseRepositoryEloquent implements MatchRepositoryInterface
{
    public function __construct(PetMatch $model)
    {
        parent::__construct($model);
    }

    public function findExistingMatch(int $pet1Id, int $pet2Id)
    {
        return $this->model->where(function ($query) use ($pet1Id, $pet2Id) {
            $query->where('initiator_pet_id', $pet1Id)
                ->where('target_pet_id', $pet2Id);
        })->orWhere(function ($query) use ($pet1Id, $pet2Id) {
            $query->where('initiator_pet_id', $pet2Id)
                ->where('target_pet_id', $pet1Id);
        })->first();
    }
}

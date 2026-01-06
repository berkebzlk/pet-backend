<?php

namespace App\Modules\Match\Repositories\Impl;

use App\Modules\Core\Enums\StatusEnum;
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

    public function getPendingMatchesForPet(int $petId)
    {
        return $this->model->where('target_pet_id', $petId)
            ->where('status', \App\Modules\Core\Enums\StatusEnum::PENDING->value)
            ->with('initiatorPet')
            ->get();
    }

    public function getMatchesForPetQuery(int $petId, string $search = '')
    {
        $query = $this->model->where('status', StatusEnum::ACCEPTED->value)
            ->where(function ($q) use ($petId) {
                $q->where('initiator_pet_id', $petId)
                    ->orWhere('target_pet_id', $petId);
            });

        if (!empty($search)) {
            $query->where(function ($q) use ($search, $petId) {
                $q->where(function ($sub) use ($search, $petId) {
                    $sub->where('target_pet_id', $petId)
                        ->whereHas('initiatorPet', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        });
                })->orWhere(function ($sub) use ($search, $petId) {
                    $sub->where('initiator_pet_id', $petId)
                        ->whereHas('targetPet', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        });
                });
            });
        }

        return $query->with(['initiatorPet', 'targetPet'])
            ->orderBy('updated_at', 'desc');
    }
}

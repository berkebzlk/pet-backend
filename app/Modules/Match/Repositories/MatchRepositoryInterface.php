<?php

namespace App\Modules\Match\Repositories;

use App\Modules\Core\Repositories\BaseRepositoryInterface;

interface MatchRepositoryInterface extends BaseRepositoryInterface
{
    public function findExistingMatch(int $pet1Id, int $pet2Id);
    public function getPendingMatchesForPet(int $petId);
    public function getMatchesForPetQuery(int $petId, string $search = '');
}

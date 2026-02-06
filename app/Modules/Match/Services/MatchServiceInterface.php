<?php

namespace App\Modules\Match\Services;

use App\Modules\Core\Services\BaseServiceInterface;

interface MatchServiceInterface extends BaseServiceInterface
{
    public function getPendingMatches(int $petId);
    public function respondToMatch(int $matchId, \App\Modules\Core\Enums\StatusEnum $status);
    public function checkMatchStatus(int $initiatorPetId, int $targetPetId);
    public function cancelMatchRequest(int $matchId);
    public function getMatches(int $petId, array $data = []);
    public function arePetsConnected(int $pet1Id, int $pet2Id): bool;
}

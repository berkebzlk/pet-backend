<?php

namespace App\Modules\Match\Services\Impl;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Match\Repositories\MatchRepositoryInterface;
use App\Modules\Match\Services\MatchServiceInterface;
use App\Modules\Pet\Repositories\PetRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Modules\Pet\Services\Impl\PetService;
use Illuminate\Support\Facades\Gate;

class MatchService extends BaseService implements MatchServiceInterface
{
    public function __construct(
        private MatchRepositoryInterface $matchRepository,
        private PetRepositoryInterface $petRepository,
        private PetService $petService
    ) {
        parent::__construct($matchRepository);
    }

    public function store(array $data)
    {
        $initiatorPet = $this->petRepository->findById($data['initiator_pet_id']);

        if (!$initiatorPet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->petService->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }

        // Ensure the authenticated user owns the initiator pet
        if (!Gate::allows('createMatch', $initiatorPet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        // Check if match already exists (in either direction)
        $existingMatch = $this->matchRepository->findExistingMatch($data['initiator_pet_id'], $data['target_pet_id']);

        if ($existingMatch) {
            throw new Exception(__('http.' . HttpStatusEnum::CONFLICT->value), HttpStatusEnum::CONFLICT->value);
        }

        $data['status'] = StatusEnum::PENDING->value;
        $match = $this->matchRepository->create($data);

        // Load relationships needed for the event
        $match->load(['initiatorPet', 'targetPet']);

        // Dispatch event
        \App\Modules\Match\Events\MatchRequestSent::dispatch($match);

        return $match;
    }

    public function getPendingMatches(int $petId)
    {
        $pet = $this->petRepository->findById($petId);

        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->petService->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }

        if (!Gate::allows('viewMatches', $pet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        return $this->matchRepository->getPendingMatchesForPet($petId);
    }

    public function respondToMatch(int $matchId, StatusEnum $status)
    {
        $match = $this->matchRepository->findById($matchId);

        if (!$match) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value), HttpStatusEnum::NOT_FOUND->value);
        }

        // Load target pet to check ownership
        $match->load('targetPet');

        // Check if user owns the target pet
        if ($match->targetPet->user_id !== Auth::id()) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        // Check if match is pending
        if ($match->status !== StatusEnum::PENDING) {
            throw new Exception(__('match.not_pending'), HttpStatusEnum::BAD_REQUEST->value);
        }

        if ($status === StatusEnum::REJECTED) {
            $this->matchRepository->delete($matchId);
            return null;
        }

        $this->matchRepository->update($matchId, ['status' => $status->value]);

        if ($status === StatusEnum::ACCEPTED) {
            $match->load(['initiatorPet', 'targetPet']);
            \App\Modules\Match\Events\MatchAccepted::dispatch($match);
        }

        return $match->refresh();
    }

    public function checkMatchStatus(int $initiatorPetId, int $targetPetId)
    {
        return $this->matchRepository->findExistingMatch($initiatorPetId, $targetPetId);
    }

    public function cancelMatchRequest(int $matchId)
    {
        $match = $this->matchRepository->findById($matchId);

        if (!$match) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value), HttpStatusEnum::NOT_FOUND->value);
        }

        // Load both pets to check ownership
        $match->load(['initiatorPet', 'targetPet']);

        // Check if user owns either the initiator pet or the target pet
        if ($match->initiatorPet->user_id !== Auth::id() && $match->targetPet->user_id !== Auth::id()) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        // Check if match is pending or accepted
        if (!in_array($match->status, [StatusEnum::PENDING, StatusEnum::ACCEPTED])) {
            throw new Exception(__('match.not_pending_or_accepted'), HttpStatusEnum::BAD_REQUEST->value);
        }

        // Determine target user ID (the one who received the request)
        // If I am the initiator, target is targetPet's owner.
        // If I am the target, target is initiatorPet's owner (but in that case I am rejecting, not cancelling request usually).
        // Actually, cancelMatchRequest is for the sender to cancel their request.
        // But we also allow unmatching now.
        // If unmatching, we should notify the OTHER party.

        $currentUserId = Auth::id();
        $targetUserId = $match->initiatorPet->user_id === $currentUserId
            ? $match->targetPet->user_id
            : $match->initiatorPet->user_id;

        $targetPetId = $match->initiatorPet->user_id === $currentUserId
            ? $match->target_pet_id
            : $match->initiator_pet_id;

        \App\Modules\Match\Events\MatchRequestCancelled::dispatch($matchId, $targetUserId, $targetPetId);

        $this->matchRepository->delete($matchId);
        return null;
    }

    public function getMatches(int $petId, array $data = [])
    {
        $pet = $this->petRepository->findById($petId);

        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->petService->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }

        // Get the query builder with custom search logic applied
        $query = $this->matchRepository->getMatchesForPetQuery($petId, $data['search'] ?? '');

        // Remove search from data to avoid double filtering by DataGridHelper (if it tries to search on Match model fields)
        if (isset($data['search'])) {
            unset($data['search']);
        }

        // Use BaseService index method which handles pagination, sorting, etc.
        return $this->index($data, $query);
    }
    public function arePetsConnected(int $pet1Id, int $pet2Id): bool
    {
        $match = $this->matchRepository->findExistingMatch($pet1Id, $pet2Id);
        return $match && $match->status === StatusEnum::ACCEPTED;
    }
}

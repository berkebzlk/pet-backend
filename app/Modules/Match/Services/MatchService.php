<?php

namespace App\Modules\Match\Services;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Match\Models\PetMatch;
use App\Modules\Match\Events\MatchAccepted;
use App\Modules\Match\Events\MatchRequestCancelled;
use App\Modules\Match\Events\MatchRequestSent;
use App\Modules\Pet\Models\Pet;
use App\Modules\Pet\Services\PetService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MatchService extends BaseEloquentService
{
    public function __construct(
        protected PetMatch $petMatch,
        protected PetService $petService
    ) {
        parent::__construct($petMatch);
    }

    private function findExistingMatch(int $pet1Id, int $pet2Id)
    {
        return $this->petMatch->where(function ($query) use ($pet1Id, $pet2Id) {
            $query->where('initiator_pet_id', $pet1Id)
                ->where('target_pet_id', $pet2Id);
        })->orWhere(function ($query) use ($pet1Id, $pet2Id) {
            $query->where('initiator_pet_id', $pet2Id)
                ->where('target_pet_id', $pet1Id);
        })->first();
    }

    public function store(array $data)
    {
        $initiatorPet = Pet::find($data['initiator_pet_id']);

        if (!$initiatorPet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => 'Pet']), HttpStatusEnum::NOT_FOUND->value);
        }

        // Ensure the authenticated user owns the initiator pet
        if (!Gate::allows('createMatch', $initiatorPet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        // Check if match already exists (in either direction)
        $existingMatch = $this->findExistingMatch($data['initiator_pet_id'], $data['target_pet_id']);

        if ($existingMatch) {
            throw new Exception(__('http.' . HttpStatusEnum::CONFLICT->value), HttpStatusEnum::CONFLICT->value);
        }

        $data['status'] = StatusEnum::PENDING->value;
        $match = $this->petMatch->create($data);

        // Load relationships needed for the event
        $match->load(['initiatorPet', 'targetPet']);

        // Dispatch event
        MatchRequestSent::dispatch($match);

        return $match;
    }

    public function getPendingMatches(int $petId)
    {
        $pet = Pet::find($petId);

        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => 'Pet']), HttpStatusEnum::NOT_FOUND->value);
        }

        if (!Gate::allows('viewMatches', $pet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        return $this->petMatch->where('target_pet_id', $petId)
            ->where('status', StatusEnum::PENDING->value)
            ->with('initiatorPet')
            ->get();
    }

    public function respondToMatch(int $matchId, StatusEnum $status)
    {
        $match = $this->show($matchId);

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
            $match->delete();
            return null;
        }

        $match->update(['status' => $status->value]);

        if ($status === StatusEnum::ACCEPTED) {
            $match->load(['initiatorPet', 'targetPet']);
            MatchAccepted::dispatch($match);
        }

        return $match->refresh();
    }

    public function checkMatchStatus(int $initiatorPetId, int $targetPetId)
    {
        return $this->findExistingMatch($initiatorPetId, $targetPetId);
    }

    public function cancelMatchRequest(int $matchId)
    {
        $match = $this->show($matchId);

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

        $currentUserId = Auth::id();
        $targetUserId = $match->initiatorPet->user_id === $currentUserId
            ? $match->targetPet->user_id
            : $match->initiatorPet->user_id;

        $targetPetId = $match->initiatorPet->user_id === $currentUserId
            ? $match->target_pet_id
            : $match->initiator_pet_id;

        MatchRequestCancelled::dispatch($matchId, $targetUserId, $targetPetId);

        $match->delete();
        return null;
    }

    public function getMatches(int $petId, array $data = [])
    {
        $pet = Pet::find($petId);

        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => 'Pet']), HttpStatusEnum::NOT_FOUND->value);
        }

        $data['sortBy'] = json_encode(['updated_at' => 'desc']);

        $search = $data['search'] ?? '';
        unset($data['search']);

        $query = $this->petMatch->newQuery()->where('status', StatusEnum::ACCEPTED->value)
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

        $query->with(['initiatorPet', 'targetPet']);

        return $this->index($data, $query);
    }

    public function arePetsConnected(int $pet1Id, int $pet2Id): bool
    {
        $match = $this->findExistingMatch($pet1Id, $pet2Id);
        return $match && $match->status === StatusEnum::ACCEPTED;
    }
}

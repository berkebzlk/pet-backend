<?php

namespace App\Modules\Breeding\Services;

use App\Modules\Breeding\Models\BreedingConnection;
use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Match\Models\PetMatch;
use App\Modules\Match\Services\MatchService;
use App\Modules\Pet\Models\Pet;
use Exception;
use Illuminate\Support\Facades\Auth;

class BreedingService extends BaseEloquentService
{
    public function __construct(
        protected BreedingConnection $breedingConnection,
        protected MatchService $matchService
    ) {
        parent::__construct($breedingConnection);
    }

    private function findExistingConnection(int $pet1Id, int $pet2Id)
    {
        return $this->breedingConnection->where(function ($query) use ($pet1Id, $pet2Id) {
            $query->where('initiator_pet_id', $pet1Id)
                ->where('target_pet_id', $pet2Id);
        })->orWhere(function ($query) use ($pet1Id, $pet2Id) {
            $query->where('initiator_pet_id', $pet2Id)
                ->where('target_pet_id', $pet1Id);
        })->first();
    }

    public function discover(int $petId, array $filters = [])
    {
        $pet = Pet::find($petId);
        if (!$pet) {
            throw new Exception("Pet not found", HttpStatusEnum::NOT_FOUND->value);
        }

        // Must own the pet to discover for it
        if ($pet->user_id !== Auth::id()) {
            throw new Exception("Forbidden", HttpStatusEnum::FORBIDDEN->value);
        }

        $query = Pet::where('is_breeding_available', true)
            ->where('user_id', '!=', Auth::id()); // Exclude own pets

        // Basic Rules: Must be same type (dog to dog)
        $query->where('type', $pet->type);

        // Optional Gender constraint
        if (isset($filters['gender']) && in_array($filters['gender'], ['male', 'female'])) {
            $query->where('gender', $filters['gender']);
        }

        // Exclude pets we already have a pending/accepted breeding connection with
        $excludeConnected = isset($filters['exclude_connected']) ? filter_var($filters['exclude_connected'], FILTER_VALIDATE_BOOLEAN) : false;

        if ($excludeConnected) {
            $connectedPetIds = $this->breedingConnection->where(function ($q) use ($petId) {
                $q->where('initiator_pet_id', $petId)->orWhere('target_pet_id', $petId);
            })->whereIn('status', [StatusEnum::PENDING->value, StatusEnum::ACCEPTED->value])
                ->get()
                ->map(function ($conn) use ($petId) {
                    return $conn->initiator_pet_id === $petId ? $conn->target_pet_id : $conn->initiator_pet_id;
                })->toArray();

            if (!empty($connectedPetIds)) {
                $query->whereNotIn('id', $connectedPetIds);
            }
        }

        $perPage = $filters['perPage'] ?? 15;
        return $query->paginate($perPage);
    }

    public function store(array $data)
    {
        $initiatorPet = Pet::find($data['initiator_pet_id']);
        $targetPet = Pet::find($data['target_pet_id']);

        if (!$initiatorPet || !$targetPet) {
            throw new Exception("Pet not found", HttpStatusEnum::NOT_FOUND->value);
        }

        if ($initiatorPet->user_id !== Auth::id()) {
            throw new Exception("Forbidden", HttpStatusEnum::FORBIDDEN->value);
        }

        if (!$targetPet->is_breeding_available) {
            throw new Exception("Target pet is not available for breeding", HttpStatusEnum::BAD_REQUEST->value);
        }

        if ($initiatorPet->type !== $targetPet->type) {
            throw new Exception("Pets must be of the same type to breed", HttpStatusEnum::BAD_REQUEST->value);
        }

        // Check existing breeding connection
        if ($this->findExistingConnection($initiatorPet->id, $targetPet->id)) {
            throw new Exception("Breeding connection already exists", HttpStatusEnum::CONFLICT->value);
        }

        $data['status'] = StatusEnum::PENDING->value;
        $connection = $this->breedingConnection->create($data);

        // TODO: Dispatch Event for Real-Time Notification (BreedingRequestSent)

        return $connection->load(['initiatorPet', 'targetPet']);
    }

    public function accept(int $connectionId)
    {
        $connection = $this->show($connectionId);

        if (!$connection || $connection->status !== StatusEnum::PENDING) {
            throw new Exception("Invalid connection request", HttpStatusEnum::BAD_REQUEST->value);
        }

        $connection->load('targetPet');
        if ($connection->targetPet->user_id !== Auth::id()) {
            throw new Exception("Forbidden", HttpStatusEnum::FORBIDDEN->value);
        }

        $connection->update(['status' => StatusEnum::ACCEPTED->value]);

        /** MAGIC HAPPENS HERE: Auto-create Match **/
        $this->ensureMatchExists($connection->initiator_pet_id, $connection->target_pet_id);

        // TODO: Dispatch Event (BreedingAccepted)

        return $connection->refresh()->load(['initiatorPet', 'targetPet']);
    }

    private function ensureMatchExists(int $pet1Id, int $pet2Id)
    {
        // Try to find if Social Connection already exists (maybe they were already friends)
        $exists = $this->matchService->checkMatchStatus($pet1Id, $pet2Id);

        // If not exists, or exists but is pending/rejected, force it to ACCEPTED.
        // Easiest is to just use store() and update if not exists, but store throws exception if exists.
        if (!$exists) {
            // We bypass the standard UI flow and just insert it via the model directly.
            // Using PetMatch model instance
            PetMatch::create([
                'initiator_pet_id' => $pet1Id,
                'target_pet_id' => $pet2Id,
                'status' => StatusEnum::ACCEPTED->value
            ]);
        } elseif ($exists->status !== StatusEnum::ACCEPTED) {
            $exists->update(['status' => StatusEnum::ACCEPTED->value]);
        }
    }

    public function reject(int $connectionId)
    {
        $connection = $this->show($connectionId);
        if (!$connection)
            throw new Exception("Not found", HttpStatusEnum::NOT_FOUND->value);

        $connection->load('targetPet');
        if ($connection->targetPet->user_id !== Auth::id())
            throw new Exception("Forbidden", HttpStatusEnum::FORBIDDEN->value);

        $connection->delete();
        return null;
    }

    public function cancel(int $connectionId)
    {
        $connection = $this->show($connectionId);
        if (!$connection)
            throw new Exception("Not found", HttpStatusEnum::NOT_FOUND->value);

        $connection->load(['initiatorPet', 'targetPet']);
        if ($connection->initiatorPet->user_id !== Auth::id() && $connection->targetPet->user_id !== Auth::id()) {
            throw new Exception("Forbidden", HttpStatusEnum::FORBIDDEN->value);
        }

        $connection->delete();
        return null;
    }

    public function getPendingRequests(int $petId)
    {
        // Require pet ownership
        $pet = Pet::find($petId);
        if (!$pet || $pet->user_id !== Auth::id()) {
            throw new Exception("Forbidden", HttpStatusEnum::FORBIDDEN->value);
        }

        return $this->breedingConnection->where(function ($query) use ($petId) {
            $query->where('target_pet_id', $petId)
                ->orWhere('initiator_pet_id', $petId);
        })
            ->where('status', StatusEnum::PENDING->value)
            ->with(['initiatorPet', 'targetPet'])
            ->get();
    }

    public function getConnections(int $petId)
    {
        $connectedPetIds = $this->breedingConnection->where(function ($q) use ($petId) {
            $q->where('initiator_pet_id', $petId)->orWhere('target_pet_id', $petId);
        })->where('status', StatusEnum::ACCEPTED->value)
            ->get()
            ->map(function ($conn) use ($petId) {
                return $conn->initiator_pet_id === $petId ? $conn->target_pet_id : $conn->initiator_pet_id;
            })->toArray();

        return Pet::whereIn('id', $connectedPetIds)->get();
    }
}

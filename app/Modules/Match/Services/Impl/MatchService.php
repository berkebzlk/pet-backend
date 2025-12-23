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
use App\Modules\Pet\Models\Pet;
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
        return $this->matchRepository->create($data);
    }
}

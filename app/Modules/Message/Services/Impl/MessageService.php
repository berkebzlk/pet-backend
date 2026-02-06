<?php

namespace App\Modules\Message\Services\Impl;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Services\Impl\BaseService;
use App\Modules\Match\Services\MatchServiceInterface;
use App\Modules\Message\Models\Message;
use App\Modules\Message\Services\MessageServiceInterface;
use App\Modules\Pet\Repositories\PetRepositoryInterface;
use App\Modules\Pet\Services\Impl\PetService;
use Exception;
use Illuminate\Support\Facades\Gate;
use App\Modules\Message\Repositories\MessageRepository;
use App\Modules\Message\Events\MessageSent;

class MessageService extends BaseService implements MessageServiceInterface
{
    public function __construct(
        private MatchServiceInterface $matchService,
        private PetRepositoryInterface $petRepository,
        private PetService $petService,
        private MessageRepository $messageRepository
    ) {
        parent::__construct($messageRepository);
    }

    public function sendMessage(int $senderPetId, array $data)
    {
        $senderPet = $this->petRepository->findById($senderPetId);
        $receiverPetId = $data['receiver_pet_id'];
        $receiverPet = $this->petRepository->findById($receiverPetId);

        if (!$senderPet || !$receiverPet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->petService->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }

        // Check if sender is owned by auth user (Policy check should handle this, but double check here or trust controller/policy)
        if (!Gate::allows('send', [Message::class, $senderPet, $receiverPet])) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        // Create message
        // Create message
        if (!$this->matchService->arePetsConnected($senderPetId, $receiverPetId)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        $message = $this->messageRepository->create([
            'sender_pet_id' => $senderPetId,
            'receiver_pet_id' => $receiverPetId,
            'content' => $data['content'],
        ]);

        MessageSent::dispatch($message);

        return $message;
    }

    public function getMessages(int $petId, int $otherPetId)
    {
        $pet = $this->petRepository->findById($petId);
        $otherPet = $this->petRepository->findById($otherPetId);

        if (!$pet || !$otherPet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->petService->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }

        // Check permissions (User must own petId)
        if (!Gate::allows('viewMatches', $pet)) { // reusing viewMatches or create new ability
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        // Check connection
        if (!$this->matchService->arePetsConnected($petId, $otherPetId)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        return Message::where(function ($query) use ($petId, $otherPetId) {
            $query->where('sender_pet_id', $petId)
                ->where('receiver_pet_id', $otherPetId);
        })->orWhere(function ($query) use ($petId, $otherPetId) {
            $query->where('sender_pet_id', $otherPetId)
                ->where('receiver_pet_id', $petId);
        })->orderBy('created_at', 'desc')->paginate(50);
    }

    public function getConversations(int $petId)
    {
        $pet = $this->petRepository->findById($petId);

        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->petService->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }

        // Check permissions (User must own petId) - Assuming 'viewMatches' is enough or create 'viewConversations'
        if (!Gate::allows('viewMatches', $pet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        return $this->messageRepository->getConversations($petId);
    }

    public function getUnreadCount(int $petId)
    {
        $pet = $this->petRepository->findById($petId);
        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->petService->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }
        // Access control: User must own the pet
        if (!Gate::allows('viewMatches', $pet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        return $this->messageRepository->getUnreadCount($petId);
    }

    public function markAsRead(int $petId, int $otherPetId)
    {
        $pet = $this->petRepository->findById($petId);
        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->petService->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }

        if (!Gate::allows('viewMatches', $pet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        $this->messageRepository->markAsRead($petId, $otherPetId);
    }
}

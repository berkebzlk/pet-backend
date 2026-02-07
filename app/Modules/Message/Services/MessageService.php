<?php

namespace App\Modules\Message\Services;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Services\BaseEloquentService;
use App\Modules\Match\Services\MatchService;
use App\Modules\Message\Events\MessageSent;
use App\Modules\Message\Models\Message;
use App\Modules\Pet\Models\Pet;
use Exception;
use Illuminate\Support\Facades\Gate;

class MessageService extends BaseEloquentService
{
    public function __construct(
        protected Message $message,
        protected MatchService $matchService
    ) {
        parent::__construct($message);
    }

    public function sendMessage(int $senderPetId, array $data)
    {
        $senderPet = Pet::find($senderPetId);
        $receiverPetId = $data['receiver_pet_id'];
        $receiverPet = Pet::find($receiverPetId);

        if (!$senderPet || !$receiverPet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => 'Pet']), HttpStatusEnum::NOT_FOUND->value);
        }

        // Check if sender is owned by auth user (Policy check should handle this, but double check here or trust controller/policy)
        if (!Gate::allows('send', [Message::class, $senderPet, $receiverPet])) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        // Create message
        if (!$this->matchService->arePetsConnected($senderPetId, $receiverPetId)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        $message = $this->message->create([
            'sender_pet_id' => $senderPetId,
            'receiver_pet_id' => $receiverPetId,
            'content' => $data['content'],
        ]);

        MessageSent::dispatch($message);

        return $message;
    }

    public function getMessages(int $petId, int $otherPetId)
    {
        $pet = Pet::find($petId);
        $otherPet = Pet::find($otherPetId);

        if (!$pet || !$otherPet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => 'Pet']), HttpStatusEnum::NOT_FOUND->value);
        }

        // Check permissions (User must own petId)
        if (!Gate::allows('viewMatches', $pet)) { // reusing viewMatches or create new ability
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        // Check connection
        if (!$this->matchService->arePetsConnected($petId, $otherPetId)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        return $this->message->where(function ($query) use ($petId, $otherPetId) {
            $query->where('sender_pet_id', $petId)
                ->where('receiver_pet_id', $otherPetId);
        })->orWhere(function ($query) use ($petId, $otherPetId) {
            $query->where('sender_pet_id', $otherPetId)
                ->where('receiver_pet_id', $petId);
        })->orderBy('created_at', 'desc')->paginate(50);
    }

    public function getConversations(int $petId)
    {
        $pet = Pet::find($petId);

        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => 'Pet']), HttpStatusEnum::NOT_FOUND->value);
        }

        // Check permissions (User must own petId)
        if (!Gate::allows('viewMatches', $pet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        $subQuery = $this->message->newQuery()
            ->selectRaw('MAX(id) as id')
            ->where('sender_pet_id', $petId)
            ->orWhere('receiver_pet_id', $petId)
            ->groupByRaw('CASE WHEN sender_pet_id = ? THEN receiver_pet_id ELSE sender_pet_id END', [$petId]);

        return $this->message->newQuery()
            ->whereIn('id', $subQuery)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function getUnreadCount(int $petId)
    {
        $pet = Pet::find($petId);
        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => 'Pet']), HttpStatusEnum::NOT_FOUND->value);
        }
        // Access control: User must own the pet
        if (!Gate::allows('viewMatches', $pet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        return $this->message->where('receiver_pet_id', $petId)
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead(int $petId, int $otherPetId)
    {
        $pet = Pet::find($petId);
        if (!$pet) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => 'Pet']), HttpStatusEnum::NOT_FOUND->value);
        }

        if (!Gate::allows('viewMatches', $pet)) {
            throw new Exception(__('http.' . HttpStatusEnum::FORBIDDEN->value), HttpStatusEnum::FORBIDDEN->value);
        }

        $this->message->where('receiver_pet_id', $petId)
            ->where('sender_pet_id', $otherPetId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}

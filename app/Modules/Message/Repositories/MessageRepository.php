<?php

namespace App\Modules\Message\Repositories;

use App\Modules\Core\Repositories\Impl\BaseRepositoryEloquent;
use App\Modules\Message\Models\Message;

class MessageRepository extends BaseRepositoryEloquent
{
    public function __construct(
        Message $model
    ) {
        parent::__construct($model);
    }

    public function getConversations(int $petId)
    {
        $subQuery = $this->model->newQuery()
            ->selectRaw('MAX(id) as id')
            ->where('sender_pet_id', $petId)
            ->orWhere('receiver_pet_id', $petId)
            ->groupByRaw('CASE WHEN sender_pet_id = ? THEN receiver_pet_id ELSE sender_pet_id END', [$petId]);

        return $this->model->newQuery()
            ->whereIn('id', $subQuery)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function getUnreadCount(int $petId): int
    {
        return $this->model->where('receiver_pet_id', $petId)
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead(int $receiverPetId, int $senderPetId): void
    {
        $this->model->where('receiver_pet_id', $receiverPetId)
            ->where('sender_pet_id', $senderPetId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
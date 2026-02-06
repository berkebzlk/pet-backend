<?php

namespace App\Modules\Message\Services;

use App\Modules\Core\Services\BaseServiceInterface;

interface MessageServiceInterface extends BaseServiceInterface
{
    public function sendMessage(int $senderPetId, array $data);

    public function getMessages(int $petId, int $otherPetId);

    public function getConversations(int $petId);

    public function getUnreadCount(int $petId);

    public function markAsRead(int $petId, int $otherPetId);
}

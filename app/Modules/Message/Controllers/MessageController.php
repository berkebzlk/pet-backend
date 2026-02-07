<?php

namespace App\Modules\Message\Controllers;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Helpers\ResponseHelper;
use App\Modules\Message\Payload\Requests\SendMessageRequest;
use App\Modules\Message\Services\MessageService;
use Illuminate\Routing\Controller;

class MessageController extends Controller
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function store(int $petId, SendMessageRequest $request)
    {
        $message = $this->messageService->sendMessage($petId, $request->validated());
        return ResponseHelper::success($message, HttpStatusEnum::OK->value);
    }

    public function index(int $petId, int $otherPetId)
    {
        $messages = $this->messageService->getMessages($petId, $otherPetId);
        return ResponseHelper::success($messages, HttpStatusEnum::OK->value);
    }

    public function conversations(int $petId)
    {
        $conversations = $this->messageService->getConversations($petId);
        return ResponseHelper::success($conversations, HttpStatusEnum::OK->value);
    }

    public function unreadCount(int $petId)
    {
        $count = $this->messageService->getUnreadCount($petId);
        return ResponseHelper::success(['count' => $count], HttpStatusEnum::OK->value);
    }

    public function markAsRead(int $petId, int $otherPetId)
    {
        $this->messageService->markAsRead($petId, $otherPetId);
        return ResponseHelper::success(null, HttpStatusEnum::OK->value);
    }
}

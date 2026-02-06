<?php

namespace App\Modules\Message\Events;

use App\Modules\Message\Models\Message;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Message $message
    ) {
    }
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('pet.' . $this->message->receiver_pet_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'sender_pet_id' => $this->message->sender_pet_id,
            'sender_name' => $this->message->sender->name,
            'content' => $this->message->content,
            'created_at' => $this->message->created_at->toIsoString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}

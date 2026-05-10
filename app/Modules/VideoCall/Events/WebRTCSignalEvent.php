<?php

namespace App\Modules\VideoCall\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCSignalEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $callId;
    public $senderId;
    public $signalData;
    public $type; // offer, answer, ice-candidate

    public function __construct(string $callId, int $senderId, int $receiverId, array $signalData, string $type)
    {
        $this->callId = $callId;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->signalData = $signalData;
        $this->type = $type;
    }

    public function broadcastOn(): array
    {
        // Broadcast to the other person in the call
        return [
            new PrivateChannel('user.' . $this->receiverId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'video.webrtc.signal';
    }
}

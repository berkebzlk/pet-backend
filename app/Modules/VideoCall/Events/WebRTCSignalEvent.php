<?php

namespace App\Modules\VideoCall\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCSignalEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $callId;
    public $senderId;
    public $receiverId;
    public $signalData;
    public $type; // offer, answer, ice-candidate

    /**
     * Create a new event instance.
     */
    public function __construct(string $callId, int $senderId, int $receiverId, array $signalData, string $type)
    {
        $this->callId = $callId;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->signalData = $signalData;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->receiverId . '.calls'),
        ];
    }
    
    public function broadcastAs()
    {
        return 'WebRTCSignal';
    }
}

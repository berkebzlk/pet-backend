<?php

namespace App\Modules\VideoCall\Events;

use App\Modules\VideoCall\Models\VideoCall;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallEndedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $call;
    protected $targetUserId;

    public function __construct(VideoCall $call, int $targetUserId)
    {
        $this->call = $call;
        $this->targetUserId = $targetUserId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->targetUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'video.call.ended';
    }
}

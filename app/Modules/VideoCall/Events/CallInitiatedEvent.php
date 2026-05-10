<?php

namespace App\Modules\VideoCall\Events;

use App\Modules\VideoCall\Models\VideoCall;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallInitiatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $call;
    public $caller;

    public function __construct(VideoCall $call)
    {
        $this->call = $call;
        $this->caller = $call->caller;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->call->receiver_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'video.call.initiated';
    }
}

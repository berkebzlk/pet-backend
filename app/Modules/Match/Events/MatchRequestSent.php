<?php

namespace App\Modules\Match\Events;

use App\Modules\Match\Models\PetMatch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PetMatch $match;

    /**
     * Create a new event instance.
     */
    public function __construct(PetMatch $match)
    {
        $this->match = $match;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Broadcast to the owner of the target pet
        return [
            new PrivateChannel('user.' . $this->match->targetPet->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'match.request.sent';
    }
}

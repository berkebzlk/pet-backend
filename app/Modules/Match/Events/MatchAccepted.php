<?php

namespace App\Modules\Match\Events;

use App\Modules\Match\Models\PetMatch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PetMatch $match;

    public function __construct(PetMatch $match)
    {
        $this->match = $match;
    }

    public function broadcastOn(): array
    {
        // Broadcast to the owner of the initiator pet (who sent the request)
        return [
            new PrivateChannel('App.Models.User.' . $this->match->initiatorPet->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'match.accepted';
    }
}

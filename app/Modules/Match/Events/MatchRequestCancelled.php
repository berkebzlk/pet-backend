<?php

namespace App\Modules\Match\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class MatchRequestCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $matchId;
    public int $targetUserId;
    public int $targetPetId;

    public function __construct(int $matchId, int $targetUserId, int $targetPetId)
    {
        $this->matchId = $matchId;
        $this->targetUserId = $targetUserId;
        $this->targetPetId = $targetPetId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->targetUserId);
    }

    public function broadcastAs()
    {
        return 'match.request.cancelled';
    }
}

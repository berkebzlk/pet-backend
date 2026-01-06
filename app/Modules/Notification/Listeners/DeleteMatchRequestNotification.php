<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Match\Events\MatchRequestCancelled;
use App\Modules\Notification\Models\Notification;

class DeleteMatchRequestNotification
{
    public function handle(MatchRequestCancelled $event)
    {
        Notification::where('type', 'match_request_sent')
            ->where('data->match_id', $event->matchId)
            ->delete();
    }
}

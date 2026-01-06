<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Match\Events\MatchAccepted;
use App\Modules\Notification\Models\Notification;
use App\Modules\User\Models\User;

class SendMatchAcceptedNotification
{
    public function handle(MatchAccepted $event)
    {
        $match = $event->match;
        $initiatorUser = User::find($match->initiatorPet->user_id);

        if ($initiatorUser) {
            Notification::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'match_accepted',
                'notifiable_type' => get_class($initiatorUser),
                'notifiable_id' => $initiatorUser->id,
                'data' => [
                    'match_id' => $match->id,
                    'accepter_pet_id' => $match->targetPet->id,
                    'accepter_pet_name' => $match->targetPet->name,
                    'accepter_pet_image' => $match->targetPet->image,
                    'message' => "{$match->targetPet->name} accepted your match request!",
                ],
            ]);
        }
    }
}

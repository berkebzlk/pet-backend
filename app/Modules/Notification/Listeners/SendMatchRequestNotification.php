<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Match\Events\MatchRequestSent;
use App\Modules\Notification\Models\Notification;
use App\Modules\User\Models\User;

class SendMatchRequestNotification
{
    public function handle(MatchRequestSent $event)
    {
        $match = $event->match;
        $targetUser = User::find($match->targetPet->user_id);

        if ($targetUser) {
            Notification::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'match_request_sent',
                'notifiable_type' => get_class($targetUser),
                'notifiable_id' => $targetUser->id,
                'data' => [
                    'match_id' => $match->id,
                    'sender_pet_id' => $match->initiatorPet->id,
                    'sender_pet_name' => $match->initiatorPet->name,
                    'sender_pet_username' => $match->initiatorPet->username,
                    'sender_pet_image' => $match->initiatorPet->image,
                    'message' => "{$match->initiatorPet->name} wants to match with {$match->targetPet->name}!",
                ],
            ]);
        }
    }
}

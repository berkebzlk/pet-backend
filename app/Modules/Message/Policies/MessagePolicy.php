<?php

namespace App\Modules\Message\Policies;

use App\Modules\User\Models\User;
use App\Modules\Pet\Models\Pet;
use App\Modules\Message\Models\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    public function send(User $user, Pet $senderPet, Pet $receiverPet): bool
    {
        return $user->id === $senderPet->user_id;
    }

    public function view(User $user, Pet $pet): bool
    {
        return $user->id === $pet->user_id;
    }
}

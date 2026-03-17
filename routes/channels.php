<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}.calls', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('pet.{id}', function ($user, $id) {
    // Check if the user owns the pet
    return $user->pets()->where('id', $id)->exists();
});

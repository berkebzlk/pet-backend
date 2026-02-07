<?php

namespace App\Modules\Message\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Modules\Message\Events\MessageSent;
use App\Modules\Notification\Listeners\SendMessageNotification;

class MessageServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        \Illuminate\Support\Facades\Gate::policy(\App\Modules\Message\Models\Message::class, \App\Modules\Message\Policies\MessagePolicy::class);
    }
}

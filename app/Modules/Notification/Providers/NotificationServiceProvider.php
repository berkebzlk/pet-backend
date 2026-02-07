<?php

namespace App\Modules\Notification\Providers;

use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        \Illuminate\Support\Facades\Event::listen(
            \App\Modules\Match\Events\MatchRequestSent::class,
            \App\Modules\Notification\Listeners\SendMatchRequestNotification::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Modules\Match\Events\MatchAccepted::class,
            \App\Modules\Notification\Listeners\SendMatchAcceptedNotification::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Modules\Match\Events\MatchRequestCancelled::class,
            \App\Modules\Notification\Listeners\DeleteMatchRequestNotification::class
        );
    }
}

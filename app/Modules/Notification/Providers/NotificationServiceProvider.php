<?php

namespace App\Modules\Notification\Providers;

use App\Modules\Notification\Repositories\Impl\NotificationRepository;
use App\Modules\Notification\Repositories\NotificationRepositoryInterface;
use App\Modules\Notification\Services\Impl\NotificationService;
use App\Modules\Notification\Services\NotificationServiceInterface;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
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

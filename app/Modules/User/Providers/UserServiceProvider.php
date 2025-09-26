<?php

namespace App\Modules\User\Providers;

use App\Modules\User\Repositories\Impl\UserRepositoryEloquent;
use App\Modules\User\Repositories\UserRepositoryInterface;
use App\Modules\User\Services\Impl\UserService;
use App\Modules\User\Services\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepositoryEloquent::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 
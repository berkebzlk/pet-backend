<?php

namespace App\Modules\Role\Providers;

use App\Modules\Role\Repositories\Impl\RoleRepository;
use App\Modules\Role\Repositories\RoleRepositoryInterface;
use App\Modules\Role\Services\Impl\RoleService;
use App\Modules\Role\Services\RoleServiceInterface;
use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
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
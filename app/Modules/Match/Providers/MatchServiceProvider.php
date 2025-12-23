<?php

namespace App\Modules\Match\Providers;

use App\Modules\Match\Repositories\Impl\MatchRepository;
use App\Modules\Match\Repositories\MatchRepositoryInterface;
use App\Modules\Match\Services\Impl\MatchService;
use App\Modules\Match\Services\MatchServiceInterface;
use Illuminate\Support\ServiceProvider;

class MatchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MatchRepositoryInterface::class, MatchRepository::class);
        $this->app->bind(MatchServiceInterface::class, MatchService::class);
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
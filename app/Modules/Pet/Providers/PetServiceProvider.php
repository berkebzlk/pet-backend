<?php

namespace App\Modules\Pet\Providers;

use App\Modules\Pet\Repositories\Impl\PetRepository;
use App\Modules\Pet\Repositories\PetRepositoryInterface;
use App\Modules\Pet\Services\Impl\PetService;
use App\Modules\Pet\Services\PetServiceInterface;
use Illuminate\Support\ServiceProvider;

class PetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PetRepositoryInterface::class, PetRepository::class);
        $this->app->bind(PetServiceInterface::class, PetService::class);
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
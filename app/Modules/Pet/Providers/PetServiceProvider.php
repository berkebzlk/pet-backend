<?php

namespace App\Modules\Pet\Providers;

use App\Modules\Pet\Models\Pet;
use App\Modules\Pet\Policies\PetPolicy;
use Illuminate\Support\Facades\Gate;
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
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::policy(Pet::class, PetPolicy::class);
    }
}
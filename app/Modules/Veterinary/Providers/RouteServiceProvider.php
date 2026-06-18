<?php

namespace App\Modules\Veterinary\Providers;

use App\Providers\BaseRouteServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    private string $moduleName = 'Veterinary';

    protected function configureRateLimiting(): void
    {
        parent::configureRateLimiting();

        RateLimiter::for('api.' . lcfirst($this->moduleName), function (Request $request) {
            return Limit::perMinute(100)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    /**
     * Define the routes for the module.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    /**
     * Define the "api" routes for the module.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware('throttle:api.veterinary')
            ->middleware('api')
            ->prefix('api')
            ->group(base_path('app/Modules/' . $this->moduleName . '/Routes/api.php'));
    }
}

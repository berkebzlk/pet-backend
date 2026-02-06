<?php

namespace App\Modules\Message\Providers;

use App\Providers\BaseRouteServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    private $moduleName = 'Message';

    protected function configureRateLimiting(): void
    {
        parent::configureRateLimiting();

        RateLimiter::for('api.' . lcfirst($this->moduleName), function (Request $request) {
            return Limit::perMinute(100)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    public function map()
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::middleware('throttle:api.Message')
            ->middleware('api')
            ->prefix('api')
            ->group(base_path('app/Modules/' . $this->moduleName . '/Routes/api.php'));
    }
}

<?php

namespace App\Modules\VideoCall\Providers;

use App\Providers\BaseRouteServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    private $moduleName = 'VideoCall';

    public function map()
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('app/Modules/' . $this->moduleName . '/Routes/api.php'));
    }
}

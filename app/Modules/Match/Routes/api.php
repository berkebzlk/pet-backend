<?php

use App\Modules\Match\Controllers\MatchController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api'], 'prefix' => 'matches'], function () {
    Route::post('/', [MatchController::class, 'store']);
});



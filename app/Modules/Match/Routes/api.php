<?php

use App\Modules\Match\Controllers\MatchController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api'], 'prefix' => 'matches'], function () {
    Route::post('/', [MatchController::class, 'store']);
    Route::get('/pending', [MatchController::class, 'pending']);
    Route::get('/check', [MatchController::class, 'check']);
    Route::post('/{id}/accept', [MatchController::class, 'accept']);
    Route::post('/{id}/reject', [MatchController::class, 'reject']);
    Route::delete('/{id}', [MatchController::class, 'cancel']);
    Route::get('/{petId}', [MatchController::class, 'index']);
});



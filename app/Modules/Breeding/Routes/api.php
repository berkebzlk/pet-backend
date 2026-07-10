<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Breeding\Controllers\BreedingController;

Route::group(['middleware' => ['auth:api'], 'prefix' => 'breeding'], function () {
    Route::get('/discover', [BreedingController::class, 'discover']);
    Route::post('/request', [BreedingController::class, 'store']);
    Route::get('/pending', [BreedingController::class, 'pending']);
    Route::post('/{id}/accept', [BreedingController::class, 'accept']);
    Route::post('/{id}/reject', [BreedingController::class, 'reject']);
    Route::delete('/remove/connection', [BreedingController::class, 'disconnect']);
    Route::delete('/{id}', [BreedingController::class, 'cancel']);
    Route::get('/{petId}', [BreedingController::class, 'index']); // Get existing accepted connections
});
<?php

use App\Modules\Pet\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('my-pets', [PetController::class, 'myPets']);
    Route::get('pet/search', [PetController::class, 'search']);
    Route::get('pet/username/{username}', [PetController::class, 'getByUsername']);
    Route::apiResource('pet', PetController::class);
});



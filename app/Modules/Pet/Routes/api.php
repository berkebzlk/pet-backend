<?php

use App\Modules\Pet\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('my-pets', [PetController::class, 'myPets']);
    Route::apiResource('pet', PetController::class);
});



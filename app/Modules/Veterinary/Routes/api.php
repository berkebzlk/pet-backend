<?php

use App\Modules\Veterinary\Controllers\VeterinaryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/veterinary-profile', [VeterinaryController::class, 'store']);
    Route::get('/veterinarians', [VeterinaryController::class, 'index']);
    Route::get('/veterinarians/cities', [VeterinaryController::class, 'getCities']);
    Route::get('/veterinarians/{id}', [VeterinaryController::class, 'show']);
    Route::get('/veterinarians/{id}/posts', [VeterinaryController::class, 'getPosts']);
    Route::get('/veterinarians/{id}/reviews', [VeterinaryController::class, 'getReviews']);
    Route::post('/veterinarians/{id}/reviews', [VeterinaryController::class, 'addReview']);
});

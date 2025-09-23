<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::middleware('guest')
    ->prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

// Protected routes (authentication required)
Route::middleware('auth:api')
    ->group(function () {
        // Auth routes

        Route::group(['prefix' => 'auth'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });

        // User routes
        Route::group(['prefix' => 'user'], function () {
            Route::get('/profile', [UserController::class, 'profile']);
            Route::put('/profile', [UserController::class, 'updateProfile']);
            Route::delete('/profile', [UserController::class, 'destroy']);
        });
    });

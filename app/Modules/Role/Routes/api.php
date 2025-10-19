<?php

use App\Modules\Role\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('role')->group(function () {
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/', [RoleController::class, 'index']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'delete']);
});

<?php

use App\Modules\Message\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api'], 'prefix' => 'messages'], function () {
    Route::post('/{petId}', [MessageController::class, 'store']);
    Route::get('/conversations/{petId}', [MessageController::class, 'conversations']);
    Route::get('/unread-count/{petId}', [MessageController::class, 'unreadCount']);
    Route::post('/mark-as-read/{petId}/{otherPetId}', [MessageController::class, 'markAsRead']);
    Route::get('/{petId}/{otherPetId}', [MessageController::class, 'index']);
});

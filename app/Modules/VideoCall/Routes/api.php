<?php

use App\Modules\VideoCall\Controllers\VideoCallController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->prefix('video-calls')->group(function () {
    Route::post('/initiate', [VideoCallController::class, 'initiate']);
    Route::post('/{id}/accept', [VideoCallController::class, 'accept']);
    Route::post('/{id}/end', [VideoCallController::class, 'end']);
    Route::post('/signal', [VideoCallController::class, 'signal']);
});

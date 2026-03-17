<?php

use Illuminate\Support\Facades\Route;
use App\Modules\VideoCall\Controllers\VideoCallController;

Route::middleware('auth:api')->prefix('videoCall')->group(function () {
    Route::post('/initiate', [VideoCallController::class, 'initiate']);
    Route::post('/{call}/accept', [VideoCallController::class, 'accept']);
    Route::post('/{call}/reject', [VideoCallController::class, 'reject']);
    Route::post('/{call}/end', [VideoCallController::class, 'end']);
    Route::post('/signal', [VideoCallController::class, 'signal']);
});



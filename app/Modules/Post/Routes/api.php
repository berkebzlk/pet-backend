<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Post\Controllers\PostController;
use App\Modules\Post\Controllers\CommentController;
use App\Modules\Post\Controllers\LikeController;
use App\Modules\Post\Controllers\SavedPostController;

Route::middleware('auth:api')->group(function () {
    Route::post('/post', [PostController::class, 'store']);
    Route::get('/post', [PostController::class, 'index']);
    Route::get('/post/{id}', [PostController::class, 'show']);
    Route::delete('/post/{id}', [PostController::class, 'delete']);
    Route::get('/pet/{petId}/posts', [PostController::class, 'getPetPosts']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/post/{id}/like', [LikeController::class, 'like']);
    Route::delete('/post/{id}/like', [LikeController::class, 'unlike']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/post/{id}/comments', [CommentController::class, 'store']);
    Route::delete('/post/{id}/comments/{commentId}', [CommentController::class, 'destroy']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/post/{id}/save', [SavedPostController::class, 'save']);
    Route::delete('/post/{id}/save', [SavedPostController::class, 'unsave']);
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PostAttachmentController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    // Login
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::delete('/logout', [AuthController::class, 'logout']);

    // Post
    
    
    

}); 

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // follow
    Route::post('/{username}/follow', [FollowController::class, 'follow']);
    Route::delete('/{username}/unfollow', [FollowController::class, 'unfollow']);
    
    // Followers
    Route::put('/{username}/accept', [FollowController::class, 'accept']);
    Route::get('/{username}/followers', [FollowController::class, 'getfollower']);


    // post
    Route::get('/post', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::post('/post/{id}', [PostController::class, 'destroy']);
    

});
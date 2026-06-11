<?php

use Illuminate\Support\Facades\Route;

// 认证相关 API（不需要登录）
Route::prefix('auth')->group(function () {
    Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('send-code', [App\Http\Controllers\Api\AuthController::class, 'sendCode']);
    Route::post('login-by-code', [App\Http\Controllers\Api\AuthController::class, 'loginByCode']);
});

// 需要登录的 API
Route::middleware('auth:api')->group(function () {
    // 用户相关 API
    Route::prefix('user')->group(function () {
        Route::get('profile', [App\Http\Controllers\Api\UserController::class, 'profile']);
        Route::put('profile', [App\Http\Controllers\Api\UserController::class, 'updateProfile']);
        Route::get('points', [App\Http\Controllers\Api\UserController::class, 'points']);
        Route::post('sign-in', [App\Http\Controllers\Api\UserController::class, 'signIn']);
    });

    // 发现相关 API
    Route::prefix('discover')->group(function () {
        Route::get('recommend', [App\Http\Controllers\Api\DiscoverController::class, 'recommend']);
        Route::post('like/{userId}', [App\Http\Controllers\Api\DiscoverController::class, 'like']);
        Route::post('dislike/{userId}', [App\Http\Controllers\Api\DiscoverController::class, 'dislike']);
        Route::get('matches', [App\Http\Controllers\Api\DiscoverController::class, 'matches']);
    });

    // 聊天相关 API
    Route::prefix('chat')->group(function () {
        Route::get('conversations', [App\Http\Controllers\Api\ChatController::class, 'conversations']);
        Route::get('messages/{userId}', [App\Http\Controllers\Api\ChatController::class, 'messages']);
        Route::post('send/{userId}', [App\Http\Controllers\Api\ChatController::class, 'send']);
    });

    // 好友相关 API
    Route::prefix('friends')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\FriendshipController::class, 'friends']);
        Route::get('requests', [App\Http\Controllers\Api\FriendshipController::class, 'requests']);
        Route::post('request/{userId}', [App\Http\Controllers\Api\FriendshipController::class, 'sendRequest']);
        Route::post('accept/{userId}', [App\Http\Controllers\Api\FriendshipController::class, 'acceptRequest']);
        Route::post('reject/{userId}', [App\Http\Controllers\Api\FriendshipController::class, 'rejectRequest']);
    });

    // 会员相关 API
    Route::prefix('membership')->group(function () {
        Route::get('levels', [App\Http\Controllers\Api\MembershipController::class, 'levels']);
    });
});

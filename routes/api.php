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
Route::group(['middleware' => 'bearer'], function () {
    // 用户相关 API
    Route::prefix('user')->group(function () {
        Route::get('profile', [App\Http\Controllers\Api\UserController::class, 'profile']);
        Route::get('{userId}/profile', [App\Http\Controllers\Api\UserController::class, 'getUserProfile']);
        Route::put('profile', [App\Http\Controllers\Api\UserController::class, 'updateProfile']);
        Route::get('points', [App\Http\Controllers\Api\UserController::class, 'points']);
        Route::post('sign-in', [App\Http\Controllers\Api\UserController::class, 'signIn']);
        Route::get('album', [App\Http\Controllers\Api\UserController::class, 'getAlbum']);
        Route::post('album/upload', [App\Http\Controllers\Api\UserController::class, 'uploadAlbumPhoto']);
    });

    // 发现相关 API
    Route::prefix('discover')->group(function () {
        Route::get('cards', [App\Http\Controllers\Api\DiscoverController::class, 'cards']);
        Route::get('recommend', [App\Http\Controllers\Api\DiscoverController::class, 'recommend']);
        Route::post('like/{userId}', [App\Http\Controllers\Api\DiscoverController::class, 'like']);
        Route::post('dislike/{userId}', [App\Http\Controllers\Api\DiscoverController::class, 'dislike']);
        Route::post('pass/{userId}', [App\Http\Controllers\Api\DiscoverController::class, 'pass']);
        Route::get('matches', [App\Http\Controllers\Api\DiscoverController::class, 'matches']);
    });

    // 聊天相关 API
    Route::prefix('chat')->group(function () {
        Route::get('conversations', [App\Http\Controllers\Api\ChatController::class, 'conversations']);
        Route::get('messages/{userId}', [App\Http\Controllers\Api\ChatController::class, 'messages']);
        Route::post('send/{userId}', [App\Http\Controllers\Api\ChatController::class, 'send']);
        Route::get('fetch/{userId}', [App\Http\Controllers\Api\ChatController::class, 'fetchMessages']);
        Route::get('history/{userId}', [App\Http\Controllers\Api\ChatController::class, 'loadHistory']);
        Route::get('gifts', [App\Http\Controllers\Api\ChatController::class, 'getUserGifts']);
        Route::post('send-gift/{userId}', [App\Http\Controllers\Api\ChatController::class, 'sendGift']);
    });

    // 好友相关 API
    Route::prefix('friends')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\FriendshipController::class, 'friends']);
        Route::get('requests', [App\Http\Controllers\Api\FriendshipController::class, 'requests']);
        Route::post('request/{userId}', [App\Http\Controllers\Api\FriendshipController::class, 'sendRequest']);
        Route::post('accept/{userId}', [App\Http\Controllers\Api\FriendshipController::class, 'acceptRequest']);
        Route::post('reject/{userId}', [App\Http\Controllers\Api\FriendshipController::class, 'rejectRequest']);
        Route::get('blocked', [App\Http\Controllers\Api\FriendshipController::class, 'blocked']);
        Route::post('block/{userId}', [App\Http\Controllers\Api\FriendshipController::class, 'block']);
        Route::post('unblock/{userId}', [App\Http\Controllers\Api\FriendshipController::class, 'unblock']);
        Route::post('delete/{userId}', [App\Http\Controllers\Api\FriendshipController::class, 'delete']);
    });

    // 好感度相关 API
    Route::prefix('attraction')->group(function () {
        Route::post('like/{userId}', [App\Http\Controllers\Api\AttractionController::class, 'like']);
        Route::post('dislike/{userId}', [App\Http\Controllers\Api\AttractionController::class, 'dislike']);
        Route::post('cancel/{userId}', [App\Http\Controllers\Api\AttractionController::class, 'cancel']);
        Route::get('status/{userId}', [App\Http\Controllers\Api\AttractionController::class, 'getStatus']);
        Route::get('likes', [App\Http\Controllers\Api\AttractionController::class, 'getLikes']);
        Route::get('mutual', [App\Http\Controllers\Api\AttractionController::class, 'getMutualLikes']);
    });

    // 会员相关 API
    Route::prefix('membership')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\MembershipApiController::class, 'index']);
        Route::post('purchase', [App\Http\Controllers\Api\MembershipApiController::class, 'purchase']);
        Route::post('convert-points', [App\Http\Controllers\Api\MembershipApiController::class, 'convertPoints']);
        Route::post('cancel', [App\Http\Controllers\Api\MembershipApiController::class, 'cancel']);
    });

    // 说说相关 API
    Route::prefix('status')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\StatusController::class, 'index']);
        Route::get('user/{userId}', [App\Http\Controllers\Api\StatusController::class, 'getUserStatuses']);
        Route::post('/', [App\Http\Controllers\Api\StatusController::class, 'store']);
        Route::post('like/{statusId}', [App\Http\Controllers\Api\StatusController::class, 'like']);
        Route::post('comment/{statusId}', [App\Http\Controllers\Api\StatusController::class, 'comment']);
        Route::delete('{statusId}', [App\Http\Controllers\Api\StatusController::class, 'destroy']);
    });

    // 礼物相关 API
    Route::prefix('gift')->group(function () {
        Route::get('my-gifts', [App\Http\Controllers\Api\GiftController::class, 'getUserGifts']);
        Route::get('all', [App\Http\Controllers\Api\GiftController::class, 'getAllGifts']);
        Route::get('redemptions', [App\Http\Controllers\Api\GiftController::class, 'getRedemptionHistory']);
        Route::get('history', [App\Http\Controllers\Api\GiftController::class, 'getGiftHistory']);
        Route::post('save-redemption-info', [App\Http\Controllers\Api\GiftController::class, 'saveRedemptionInfo']);
        Route::post('redeem', [App\Http\Controllers\Api\GiftController::class, 'redeem']);
    });

    // 相册相关 API
    Route::prefix('album')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\AlbumController::class, 'getUserAlbums']);
        Route::get('{albumId}', [App\Http\Controllers\Api\AlbumController::class, 'getAlbum']);
        Route::post('/', [App\Http\Controllers\Api\AlbumController::class, 'createAlbum']);
        Route::put('{albumId}', [App\Http\Controllers\Api\AlbumController::class, 'updateAlbum']);
        Route::delete('{albumId}', [App\Http\Controllers\Api\AlbumController::class, 'deleteAlbum']);
        Route::post('{albumId}/upload', [App\Http\Controllers\Api\AlbumController::class, 'uploadPhoto']);
        Route::delete('photo/{photoId}', [App\Http\Controllers\Api\AlbumController::class, 'deletePhoto']);
        Route::post('{albumId}/purchase', [App\Http\Controllers\Api\AlbumController::class, 'purchaseAlbum']);
        Route::get('{albumId}/check-purchase', [App\Http\Controllers\Api\AlbumController::class, 'checkPurchase']);
        Route::get('purchases/history', [App\Http\Controllers\Api\AlbumController::class, 'getPurchaseHistory']);
        Route::get('photos/all', [App\Http\Controllers\Api\AlbumController::class, 'getUserPhotos']);
        Route::get('user/{userId}/photos', [App\Http\Controllers\Api\AlbumController::class, 'getUserPhotosById']);
        Route::post('photos/upload', [App\Http\Controllers\Api\AlbumController::class, 'uploadPhotoToDefault']);
    });

    // 积分相关 API
    Route::prefix('points')->group(function () {
        Route::get('history', [App\Http\Controllers\Api\PointsController::class, 'getHistory']);
        Route::post('convert', [App\Http\Controllers\Api\PointsController::class, 'convertToCoins']);
        Route::get('coins/history', [App\Http\Controllers\Api\PointsController::class, 'getCoinsHistory']);
    });
});

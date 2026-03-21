<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\ChatController;

Route::get('/test-locale', function () {
    return response()->json([
        'current_locale' => app()->getLocale(),
        'session_locale' => session('locale'),
        'config_locale' => config('app.locale'),
        'is_zh_tw' => app()->getLocale() == 'zh_TW',
        'is_zh_cn' => app()->getLocale() == 'zh_CN',
        'is_en' => app()->getLocale() == 'en',
        'translations' => [
            'title' => __('messages.title'),
            'nav.discover' => __('messages.nav.discover'),
            'nav.chat' => __('messages.nav.chat'),
            'nav.points' => __('messages.nav.points'),
            'settings.title' => __('messages.settings.title'),
            'settings.language' => __('messages.settings.language'),
            'settings.save' => __('messages.settings.save'),
            'settings.success' => __('messages.settings.success'),
        ],
    ]);
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/blank', function () {
    return view('blank');
})->name('blank');

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/profile/{id?}', [UserController::class, 'profile'])->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::match(['put', 'post'], '/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // 好友相关路由
    Route::post('/friends/request/{user}', [FriendshipController::class, 'sendRequest'])->name('friends.request');
    Route::post('/friends/accept/{friendship}', [FriendshipController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/friends/reject/{friendship}', [FriendshipController::class, 'rejectRequest'])->name('friends.reject');
    Route::post('/friends/remove/{user}', [FriendshipController::class, 'removeFriend'])->name('friends.remove');
    Route::get('/friends', [FriendshipController::class, 'myFriends'])->name('friends');
    Route::get('/friend-requests', [FriendshipController::class, 'pendingRequests'])->name('friend.requests');
    
    // 聊天相关路由
    Route::get('/chats', [ChatController::class, 'index'])->name('chats');
    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{user}/message', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/{user}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::get('/chat/{user}/fetch', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
    Route::get('/chat/{user}/history', [ChatController::class, 'loadHistory'])->name('chat.history');
    
    // 环信测试路由
    Route::get('/easemob-test', [App\Http\Controllers\EasemobTestController::class, 'index'])->name('easemob.test');
    Route::post('/easemob/test-token', [App\Http\Controllers\EasemobTestController::class, 'testToken']);
    Route::post('/easemob/test-register', [App\Http\Controllers\EasemobTestController::class, 'testRegister']);
    Route::post('/easemob/test-send-message', [App\Http\Controllers\EasemobTestController::class, 'testSendMessage']);
    Route::post('/easemob/test-user-info', [App\Http\Controllers\EasemobTestController::class, 'testGetUserInfo']);
    Route::post('/easemob/sync-users', [App\Http\Controllers\EasemobTestController::class, 'syncUsers']);
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UploadController;

require __DIR__.'/test-picbed.php';

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

// 认证路由
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout.post');
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::post('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.reset');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset.token');
Route::post('password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'confirm'])->name('password.confirm');
Route::get('password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::get('verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
Route::get('verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
Route::post('verify/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');

// 退出登录路由（GET 方法，简单直接）
Route::get('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');

// 账户待激活页面
Route::get('/account/pending', [App\Http\Controllers\AccountPendingController::class, 'index'])->name('account.pending');

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
    
    // 聊天礼物相关路由
    Route::get('/api/user/gifts', [ChatController::class, 'getUserGifts'])->name('user.gifts.api');
    Route::post('/chat/{user}/gift', [ChatController::class, 'sendGift'])->name('chat.send.gift');
    
    // 会员中心相关路由
    Route::get('/membership', [App\Http\Controllers\MembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/purchase', [App\Http\Controllers\MembershipController::class, 'purchase'])->name('membership.purchase');
    Route::post('/membership/convert-points', [App\Http\Controllers\MembershipController::class, 'convertPoints'])->name('membership.convertPoints');
    Route::post('/membership/cancel', [App\Http\Controllers\MembershipController::class, 'cancel'])->name('membership.cancel');
    
    // 礼物相关路由
    Route::get('/user/gifts', [App\Http\Controllers\UserGiftController::class, 'index'])->name('user.gifts.index');
    Route::post('/user/gifts/{gift}/purchase', [App\Http\Controllers\UserGiftController::class, 'purchase'])->name('user.gifts.purchase');
    Route::post('/user/gifts/{userGift}/redeem', [App\Http\Controllers\UserGiftController::class, 'storeRedeem'])->name('user.gifts.redeem.store');
    Route::post('/user/gifts/save-redemption-info', [App\Http\Controllers\UserGiftController::class, 'saveRedemptionInfo'])->name('user.gifts.save-redemption-info');
    Route::post('/user/gifts/redeem-multiple', [App\Http\Controllers\UserGiftController::class, 'redeemMultiple'])->name('user.gifts.redeem-multiple');
    Route::get('/user/gifts/history', [App\Http\Controllers\GiftRedemptionController::class, 'history'])->name('user.gifts.history');
    
    // 后台管理路由
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/redemptions', [App\Http\Controllers\Admin\RedemptionController::class, 'index'])->name('redemptions.index');
        Route::get('/redemptions/{redemption}', [App\Http\Controllers\Admin\RedemptionController::class, 'show'])->name('redemptions.show');
        Route::patch('/redemptions/{redemption}/status', [App\Http\Controllers\Admin\RedemptionController::class, 'updateStatus'])->name('redemptions.update-status');
    });
    
    // 环信测试路由
    Route::get('/easemob-test', [App\Http\Controllers\EasemobTestController::class, 'index'])->name('easemob.test');
    Route::post('/easemob/test-token', [App\Http\Controllers\EasemobTestController::class, 'testToken']);
    Route::post('/easemob/test-register', [App\Http\Controllers\EasemobTestController::class, 'testRegister']);
    Route::post('/easemob/test-send-message', [App\Http\Controllers\EasemobTestController::class, 'testSendMessage']);
    Route::post('/easemob/test-user-info', [App\Http\Controllers\EasemobTestController::class, 'testGetUserInfo']);
    Route::post('/easemob/sync-users', [App\Http\Controllers\EasemobTestController::class, 'syncUsers']);
    
    // 图床上传路由
    Route::post('/upload', [UploadController::class, 'upload'])->name('upload');
    Route::post('/upload/url', [UploadController::class, 'uploadFromUrl'])->name('upload.url');
    Route::delete('/upload', [UploadController::class, 'delete'])->name('upload.delete');
    Route::get('/upload/list', [UploadController::class, 'listFiles'])->name('upload.list');
});
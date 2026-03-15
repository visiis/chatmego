<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{id?}', [UserController::class, 'profile'])->name('profile');
    Route::match(['put', 'post'], '/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});
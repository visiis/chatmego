<?php

namespace App\Providers;

use App\Models\Gift;
use App\Models\User;
use App\Observers\GiftObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Colors\Color;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 注册 User Observer
        User::observe(UserObserver::class);
        
        // 注册 Gift Observer
        Gift::observe(GiftObserver::class);

        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): string => '<html lang="zh-TW">'
        );
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use App\Extensions\Storage\PicBedAdapter;

class PicBedStorageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('picbed', function ($app, $config) {
            return new Filesystem(new PicBedAdapter($config));
        });
    }

    public function register()
    {
    }
}
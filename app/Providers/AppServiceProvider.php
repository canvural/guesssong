<?php

namespace App\Providers;

use App\Services\MusicService;
use App\Services\Spotify;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(MusicService::class, Spotify::class);
    }
}

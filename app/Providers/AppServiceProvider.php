<?php

namespace App\Providers;

use App\Services\CachingSpotify;
use App\Services\MusicService;
use App\Services\Spotify;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use SpotifyWebAPI\Session as SpotifySession;

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
        $this->registerMusicService();
    }

    private function registerMusicService(): void
    {
        $this->app->bind(MusicService::class, function (Application $app) {
            $cache = $app->make('cache.store');
            $spotify = $this->createSpotifyService($app);

            return new CachingSpotify($spotify, $cache);
        });
    }

    /**
     * @param $app
     *
     * @return Spotify
     */
    private function createSpotifyService($app): Spotify
    {
        $request = $app->make(Request::class);
        $user = $request->user();

        if ($user) {
            $accessToken = $user->socialLogin->spotify_token;
            $refreshToken = $user->socialLogin->spotify_refresh_token;
        } else {
            $accessToken = \Cache::rememberForever('spotify_access_token', function () {
                $session = new SpotifySession(env('SPOTIFY_CLIENT_ID'), env('SPOTIFY_CLIENT_SECRET'));
                $session->requestCredentialsToken();

                return $session->getAccessToken();
            });

            $refreshToken = '';
        }

        return new Spotify($accessToken, $refreshToken);
    }
}

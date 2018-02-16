<?php

namespace App\Services;

use Illuminate\Support\Collection;
use SpotifyWebAPI\SpotifyWebAPI;

class Spotify
{
    /**
     * @var SpotifyWebAPI
     */
    private $api;

    public function __construct()
    {
        $accessToken = \Cache::remember('spotify_access_token', 60, function () {
            $session = new \SpotifyWebAPI\Session(env('SPOTIFY_CLIENT_ID'), env('SPOTIFY_CLIENT_SECRET'));
            $session->requestCredentialsToken();

            return $session->getAccessToken();
        });

        $api = new SpotifyWebAPI();
        $api->setReturnType(SpotifyWebAPI::RETURN_ASSOC);
        $api->setAccessToken($accessToken);

        $this->api = $api;
    }

    /**
     * @return array
     */
    public function getPlaylistCategoriesForGame(): array
    {
        return $this->api->getCategoriesList([
            'offset' => 0,
            'limit' => 50,
        ])['categories']['items'];
    }

    public function getTracksForGame(): Collection
    {
        $playlist = collect($this->api->getCategoryPlaylists('toplists')['playlists']['items'])
                    ->shuffle()
                    ->first();

        dd($playlist);

        return collect($this->api->getUserPlaylistTracks($playlist['owner']['id'], $playlist['id'])['items'])
            ->reject(function ($track) {
                return null === $track['track']['preview_url'];
            })
            ->shuffle()
            ->take(4);
    }

    /**
     * @param string $category Spotify category id
     *
     * @return array
     */
    public function getCategoryPlaylists($category): array
    {
        return $this->api->getCategoryPlaylists($category)['playlists']['items'];
    }

    /**
     * @param array $playlist
     *
     * @return array
     */
    public function getTracksForPlaylist(array $playlist): array
    {
        return $this->api->getUserPlaylistTracks($playlist['owner']['id'], $playlist['id']);
    }
}

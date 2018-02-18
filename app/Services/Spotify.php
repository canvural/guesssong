<?php

namespace App\Services;

use Illuminate\Support\Collection;
use SpotifyWebAPI\SpotifyWebAPI;

class Spotify implements MusicService
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
    
    /**
     * @param array $tracks
     * @param array $recentlyPlayedTracks
     * @return \Illuminate\Support\Collection
     */
    public function filterTracks(array $tracks, array $recentlyPlayedTracks)
    {
        return \collect($tracks)
            ->pluck('track')
            ->reject(function ($track) {
                return null === $track['preview_url'];
            })
            ->reject(function ($track) {
                return 'track' !== $track['type'];
            })
            ->reject(function ($track) {
                return empty($track['artists']);
            })
            ->reject(function ($track) use ($recentlyPlayedTracks) {
                return collect($recentlyPlayedTracks)->contains($track['id']);
            })
            ->shuffle()
            ->map(function ($track) {
                return [
                    'id' => $track['id'],
                    'artists' => $track['artists'],
                    'name' => $track['name'],
                    'preview_url' => $track['preview_url'],
                ];
            })
            ->take(4);
    }
}

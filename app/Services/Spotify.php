<?php

namespace App\Services;

use App\Playlist;
use App\Track;
use Illuminate\Support\Collection;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIException;

class Spotify implements MusicService
{
    /**
     * @var SpotifyWebAPI
     */
    private $api;

    /**
     * @var string User refresh token for access token
     */
    private $refreshToken;

    public function __construct($accessToken, $refreshToken)
    {
        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        $api->setReturnType(SpotifyWebAPI::RETURN_ASSOC);

        $this->api = $api;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @throws SpotifyWebAPIException
     *
     * @return array
     */
    public function getPlaylistCategoriesForGame()
    {
        return $this->callWithErrorHandling(function () {
            return $this->api->getCategoriesList([
                'offset' => 0,
                'limit' => 50,
            ])['categories']['items'];
        });
    }

    /**
     * @param string $category Spotify category id
     *
     * @throws SpotifyWebAPIException
     *
     * @return Collection
     */
    public function getCategoryPlaylists($category): Collection
    {
        return $this->callWithErrorHandling(function () use ($category) {
            return \collect($this->api->getCategoryPlaylists($category)['playlists']['items'])
                ->map(function ($playlist) {
                    return Playlist::createFromSpotifyData($playlist);
                });
        });
    }

    /**
     * @param Playlist $playlist
     *
     * @throws SpotifyWebAPIException
     *
     * @return Collection
     */
    public function getPlaylistTracks(Playlist $playlist): Collection
    {
        return $this->callWithErrorHandling(function () use ($playlist) {
            return \collect(
                $this->api->getUserPlaylistTracks($playlist->getOwnerId(), $playlist->getId())['items']
            )->map(function ($track) {
                return Track::createFromSpotifyData($track['track']);
            })->filter();
        });
    }

    /**
     * @param string $userId
     * @param string $playlistId
     *
     * @throws SpotifyWebAPIException
     *
     * @return Playlist
     */
    public function getUserPlaylist(string $userId, string $playlistId): Playlist
    {
        return $this->callWithErrorHandling(function () use ($userId, $playlistId) {
            return Playlist::createFromSpotifyData($this->api->getUserPlaylist($userId, $playlistId));
        });
    }

    /**
     * @param string $userId
     *
     * @throws SpotifyWebAPIException
     *
     * @return Collection
     */
    public function getUserPlaylists(string $userId = 'me'): Collection
    {
        return $this->callWithErrorHandling(function () use ($userId) {
            $playlists = 'me' === $userId ?
                $this->api->getMyPlaylists() :
                $this->api->getUserPlaylists($userId);

            return \collect($playlists['items'])->map(function ($playlist) {
                return Playlist::createFromSpotifyData($playlist);
            });
        });
    }

    public function refreshUserAccessToken()
    {
        $session = new SpotifySession(env('SPOTIFY_CLIENT_ID'), env('SPOTIFY_CLIENT_SECRET'));

        $success = $this->refreshToken ? $session->refreshAccessToken($this->refreshToken) : $session->requestCredentialsToken();

        if ($success) {
            $this->api->setAccessToken($session->getAccessToken());

            return [
                'access_token' => $session->getAccessToken(),
                'refresh_token' => $session->getRefreshToken() ?? $this->refreshToken,
            ];
        }

        return false;
    }

    private function callWithErrorHandling(\Closure $callback)
    {
        $return = [];

        try {
            $return = $callback();
        } catch (SpotifyWebAPIException $e) {
            if (\str_contains($e->getMessage(), 'expired')) {
                $updatedTokens = $this->refreshUserAccessToken();

                if ($updatedTokens && $this->refreshToken) {
                    auth()->user()->socialLogin->update([
                        'spotify_token' => $updatedTokens['access_token'],
                        'spotify_refresh_token' => $updatedTokens['refresh_token'],
                    ]);
                }

                // Retry
                return $callback();
            }

            throw $e;
        }

        return $return;
    }
}

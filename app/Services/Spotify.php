<?php

namespace App\Services;

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
     * @var string user refresh token for access token
     */
    private $refreshToken;

    public function __construct($accessToken, $refreshToken)
    {
        $api = new SpotifyWebAPI();
        $api->setReturnType(SpotifyWebAPI::RETURN_ASSOC);
        $api->setAccessToken($accessToken);

        $this->api = $api;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @throws SpotifyWebAPIException
     *
     * @return array
     */
    public function getPlaylistCategoriesForGame(): array
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
     * @return array
     */
    public function getCategoryPlaylists($category): array
    {
        return $this->callWithErrorHandling(function () use ($category) {
            return $this->api->getCategoryPlaylists($category)['playlists']['items'];
        });
    }

    /**
     * @param array $playlist
     *
     * @throws SpotifyWebAPIException
     *
     * @return array
     */
    public function getPlaylistTracks(array $playlist): array
    {
        return $this->callWithErrorHandling(function () use ($playlist) {
            return $this->api->getUserPlaylistTracks($playlist['owner']['id'], $playlist['id'])['items'];
        });
    }

    /**
     * @param string $userId
     * @param string $playlistId
     *
     * @throws SpotifyWebAPIException
     *
     * @return array|mixed
     */
    public function getUserPlaylist(string $userId, string $playlistId)
    {
        return $this->callWithErrorHandling(function () use ($userId, $playlistId) {
            return $this->api->getUserPlaylist($userId, $playlistId);
        });
    }

    /**
     * @param string $userId
     *
     * @throws SpotifyWebAPIException
     *
     * @return array
     */
    public function getUserPlaylists(string $userId = 'me')
    {
        return $this->callWithErrorHandling(function () use ($userId) {
            $playlists = 'me' === $userId ?
                $this->api->getMyPlaylists() :
                $this->api->getUserPlaylists($userId);

            return $playlists['items'];
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

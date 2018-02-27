<?php

namespace App\Services;

use Illuminate\Cache\Repository as Cache;

class CachingSpotify implements MusicService
{
    public const DEFAULT_CACHING_MINUTES = 10080;

    /**
     * @var MusicService
     */
    private $spotify;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(MusicService $spotify, Cache $cache)
    {
        $this->cache = $cache;
        $this->spotify = $spotify;
    }

    public function getPlaylistCategoriesForGame()
    {
        return $this->cache->rememberForever('categories', function () {
            return $this->spotify->getPlaylistCategoriesForGame();
        });
    }

    /**
     * @param array $playlist
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function getPlaylistTracks(array $playlist)
    {
        return $this->cache->tags(['tracks', $playlist['id']])->remember($playlist['id'].'_tracks', self::DEFAULT_CACHING_MINUTES, function () use ($playlist) {
            return $this->spotify->getPlaylistTracks($playlist);
        });
    }

    /**
     * @param string $userId
     * @param string $playlistId
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function getUserPlaylist(string $userId, string $playlistId)
    {
        return $this->cache
            ->tags(
                ['playlists', $userId.'_playlists', $playlistId]
            )
            ->remember($userId.'_'.$playlistId, self::DEFAULT_CACHING_MINUTES, function () use ($userId, $playlistId) {
                return $this->spotify->getUserPlaylist($userId, $playlistId);
            });
    }

    /**
     * @param string $userId
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function getUserPlaylists(string $userId = 'me')
    {
        return $this->cache->tags(
            ['playlists', $userId.'_playlists']
        )->remember($userId.'_playlists', self::DEFAULT_CACHING_MINUTES, function () use ($userId) {
            return $this->spotify->getUserPlaylists($userId);
        });
    }

    /**
     * @param string $category
     *
     * @throws \BadMethodCallException
     *
     * @return array
     */
    public function getCategoryPlaylists($category)
    {
        return $this->cache->tags(
            ['playlists', $category]
        )->remember($category, self::DEFAULT_CACHING_MINUTES, function () use ($category) {
            return $this->spotify->getCategoryPlaylists($category);
        });
    }

    public function filterTracks(array $tracks, array $recentlyPlayedTracks)
    {
        return $this->spotify->filterTracks($tracks, $recentlyPlayedTracks);
    }

    /**
     * Should fetch the new access token and set it.
     * And return the new refresh token if successfull or false otherwise.
     *
     * @return string|bool
     */
    public function refreshUserAccessToken()
    {
        // TODO: Implement refreshUserAccessToken() method.
    }
}

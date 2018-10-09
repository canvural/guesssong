<?php

namespace App\Services;

use App\Playlist;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Support\Collection;

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
     * @param Playlist $playlist
     *
     * @throws \BadMethodCallException
     *
     * @return Collection
     */
    public function getPlaylistTracks(Playlist $playlist): Collection
    {
        return $this->cache->tags(['tracks', $playlist->getId()])->remember($playlist->getId().'_tracks', self::DEFAULT_CACHING_MINUTES, function () use ($playlist) {
            return $this->spotify->getPlaylistTracks($playlist);
        });
    }

    /**
     * @param string $playlistId
     *
     * @throws \BadMethodCallException
     *
     * @return Playlist
     */
    public function getPlaylist(string $playlistId): Playlist
    {
        return $this->cache
            ->tags(
                ['playlists', $playlistId]
            )
            ->remember($playlistId, self::DEFAULT_CACHING_MINUTES, function () use ($playlistId) {
                return $this->spotify->getPlaylist($playlistId);
            });
    }

    /**
     * @param string $userId
     *
     * @throws \BadMethodCallException
     *
     * @return Collection
     */
    public function getUserPlaylists(string $userId = 'me'): Collection
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
     * @return Collection
     */
    public function getCategoryPlaylists($category): Collection
    {
        return $this->cache->tags(
            ['playlists', $category]
        )->remember($category, self::DEFAULT_CACHING_MINUTES, function () use ($category) {
            return $this->spotify->getCategoryPlaylists($category);
        });
    }

    /**
     * Should fetch the new access token and set it.
     * And return the new refresh token if successfull or false otherwise.
     *
     * @return string|bool
     */
    public function refreshUserAccessToken()
    {
        return $this->spotify->refreshUserAccessToken();
    }
}

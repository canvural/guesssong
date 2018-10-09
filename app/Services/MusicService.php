<?php

namespace App\Services;

use App\Playlist;
use Illuminate\Support\Collection;

interface MusicService
{
    public function getPlaylistCategoriesForGame();

    public function getPlaylistTracks(Playlist $playlist): Collection;

    public function getPlaylist(string $playlistId): Playlist;

    public function getUserPlaylists(string $userId = 'me'): Collection;

    public function getCategoryPlaylists($category): Collection;

    /**
     * Should fetch the new access token and set it.
     * And return the new refresh token and access token if successfull or false otherwise.
     *
     * @return string|bool
     */
    public function refreshUserAccessToken();
}

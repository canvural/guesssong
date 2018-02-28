<?php

namespace App\Services;

interface MusicService
{
    public function getPlaylistCategoriesForGame();

    public function getPlaylistTracks(array $playlist);

    public function getUserPlaylist(string $userId, string $playlistId);

    public function getUserPlaylists(string $userId = 'me');

    public function getCategoryPlaylists($category);

    /**
     * Should fetch the new access token and set it.
     * And return the new refresh token and access token if successfull or false otherwise.
     *
     * @return string|bool
     */
    public function refreshUserAccessToken();
}

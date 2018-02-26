<?php

namespace App\Services;

interface MusicService
{
    public function getPlaylistCategoriesForGame();

    public function getTracksForPlaylist(array $playlist);

    public function getUserPlaylists($userId);

    public function getCategoryPlaylists($category);

    public function filterTracks(array $tracks, array $recentlyPlayedTracks);

    /**
     * Should fetch the new access token and set it.
     * And return the new refresh token if successfull or false otherwise.
     *
     * @return string|bool
     */
    public function refreshUserAccessToken();
}

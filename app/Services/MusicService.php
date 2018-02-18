<?php

namespace App\Services;

interface MusicService
{
    public function getPlaylistCategoriesForGame();

    public function getTracksForPlaylist(array $playlist);

    public function getCategoryPlaylists($category);

    public function filterTracks(array $tracks, array $recentlyPlayedTracks);
}

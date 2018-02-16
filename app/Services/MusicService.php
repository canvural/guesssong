<?php

namespace App\Services;


interface MusicService
{
    public function getPlaylistCategoriesForGame();
    
    public function getTracksForPlaylist($playlist);
    
    public function getCategoryPlaylists($category);
}
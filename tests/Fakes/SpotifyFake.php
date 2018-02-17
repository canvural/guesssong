<?php

namespace Tests\Fakes;

use App\Services\MusicService;

class SpotifyFake implements MusicService
{
    public function getPlaylistCategoriesForGame(): array
    {
        return get_fake_data('categories.json');
    }
    
    public function getTracksForPlaylist(array $playlist)
    {
        return \get_fake_data($playlist['id'] . '_tracks.json');
    }
    
    public function getCategoryPlaylists($category)
    {
        return \get_fake_data($category . '_playlists.json');
    }
}
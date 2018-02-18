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
        return \get_fake_data($playlist['id'].'_tracks.json');
    }

    public function getCategoryPlaylists($category)
    {
        return \get_fake_data($category.'_playlists.json');
    }

    public function filterTracks(array $tracks, array $recentlyPlayedTracks)
    {
        return \collect($tracks)
            ->pluck('track')
            ->reject(function ($track) {
                return null === $track['preview_url'];
            })
            ->reject(function ($track) {
                return 'track' !== $track['type'];
            })
            ->reject(function ($track) {
                return empty($track['artists']);
            })
            ->reject(function ($track) use ($recentlyPlayedTracks) {
                return collect($recentlyPlayedTracks)->contains($track['id']);
            })
            ->shuffle()
            ->map(function ($track) {
                return [
                    'id' => $track['id'],
                    'artists' => $track['artists'],
                    'name' => $track['name'],
                    'preview_url' => $track['preview_url'],
                ];
            })
            ->take(4);
    }
}

<?php

namespace Tests\Fakes;

use App\Playlist;
use App\Services\MusicService;
use App\Track;
use Illuminate\Support\Collection;
use SpotifyWebAPI\SpotifyWebAPIException;

class SpotifyFake implements MusicService
{
    public function getPlaylistCategoriesForGame(): array
    {
        return get_fake_data('categories.json');
    }

    public function getPlaylistTracks(Playlist $playlist): Collection
    {
        return collect(
            get_fake_data($playlist->getId().'_tracks.json')['items']
        )->map(function ($track) {
            return Track::createFromSpotifyData($track['track']);
        })->filter();
    }

    public function getCategoryPlaylists($category): Collection
    {
        return collect(get_fake_data($category.'_playlists.json'))->map(function ($playlist) {
            return Playlist::createFromSpotifyData($playlist);
        });
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

    /**
     * @param string $userId
     * @param string $playlistId
     *
     * @throws SpotifyWebAPIException
     *
     * @return Playlist
     */
    public function getUserPlaylist(string $userId, string $playlistId): Playlist
    {
        $playlist = Playlist::createFromSpotifyData(get_playlist('rock-hard'));

        if ($playlist->getId() !== $playlistId) {
            throw new SpotifyWebAPIException('Not found');
        }

        return $playlist;
    }

    public function getUserPlaylists(string $userId = 'me'): Collection
    {
        return collect(get_fake_data('user_playlists.json'))->map(function ($playlist) {
            return Playlist::createFromSpotifyData($playlist);
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
        return '';
    }
}

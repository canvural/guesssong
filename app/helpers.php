<?php

if (! function_exists('filter_tracks')) {
    /**
     * @param array $tracks
     * @param array $recentlyPlayedTracks
     * @return \Illuminate\Support\Collection
     */
    function filter_tracks($tracks, $recentlyPlayedTracks) {
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
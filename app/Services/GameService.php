<?php

namespace App\Services;

use Illuminate\Support\Collection;

class GameService
{
    public function transformTracksForGame(array $tracks): Collection
    {
        return \collect($tracks)
            ->pluck('track')
            ->reject(function ($track) {
                return empty($track['preview_url']);
            })
            ->reject(function ($track) {
                return 'track' !== $track['type'];
            })
            ->reject(function ($track) {
                return empty($track['artists']);
            })
            ->shuffle()
            ->map(function ($track) {
                return [
                    'id' => $track['id'],
                    'artists' => $track['artists'],
                    'name' => $track['name'],
                    'preview_url' => $track['preview_url'],
                ];
            });
    }
}

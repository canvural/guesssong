<?php

namespace App\Http\Controllers;

use App\Services\MusicService;

class UserPlaylistController extends Controller
{
    public function index(MusicService $musicService)
    {
        $playlists = collect(
            $musicService->getUserPlaylists()
        )->reject(function ($playlist) {
            return null === $playlist->getName() || empty($playlist->getImageUrl());
        });

        $playlistCounts = \auth()->user() ? \auth()->user()->getPlayedPlaylistCounts() : [];

        return view('playlists.show')->with([
            'playlists' => $playlists,
            'playlistCounts' => $playlistCounts,
            'route' => 'usergames.create',
        ]);
    }
}

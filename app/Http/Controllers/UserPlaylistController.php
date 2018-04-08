<?php

namespace App\Http\Controllers;

use App\Services\MusicService;

class UserPlaylistController extends Controller
{
    public function index(MusicService $musicService)
    {
        $playlists = $musicService->getUserPlaylists();

        $playlistCounts = \auth()->user()->getPlayedPlaylistCounts();

        return view('playlists.show')->with([
            'playlists' => $playlists,
            'playlistCounts' => $playlistCounts,
            'route' => 'usergames.create',
        ]);
    }
}

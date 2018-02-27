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
            return null === $playlist['name'] || empty($playlist['images']);
        });

        return view('playlists.show')->with([
            'playlists' => $playlists,
            'route' => 'usergames.index',
        ]);
    }
}

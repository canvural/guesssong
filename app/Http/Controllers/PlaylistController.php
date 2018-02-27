<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use Illuminate\View\View;

class PlaylistController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param string       $category Spotify playlist id
     * @param MusicService $spotify
     *
     * @return View
     */
    public function index($category, MusicService $spotify): View
    {
        $playlists = $spotify->getCategoryPlaylists($category);

        return view('playlists.show')->with([
            'playlists' => $playlists,
            'route' => 'games.create',
        ]);
    }
}

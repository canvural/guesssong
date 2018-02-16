<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use Illuminate\View\View;

class PlaylistController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param string  $category Spotify playlist id
     * @param MusicService $spotify
     *
     * @return View
     */
    public function show($category, MusicService $spotify): View
    {
        $playlists = \Cache::remember($category, now()->addDay(), function () use ($category, $spotify) {
            return $spotify->getCategoryPlaylists($category);
        });

        collect($playlists)->each(function ($playlist) {
            \Cache::add('playlist_' . str_slug($playlist['name']), $playlist, now()->addDay());
        });

        return view('playlists.show')->with(compact('playlists'));
    }
}

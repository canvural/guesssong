<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserPlaylistController extends Controller
{
    public function index(Request $request, MusicService $spotify)
    {
        $playlists = \Cache::remember($request->user()->name.'_playlists', \now()->addWeek(), function () use ($spotify, $request) {
            return collect(
               $spotify->getUserPlaylists($request->user()->socialLogin->spotify_id)
           )->reject(function ($playlist) {
               return null === $playlist['name'] || empty($playlist['images']);
           });
        });

        $this->cacheUserPlaylists($request->user()->id, $playlists);

        return view('playlists.show')->with([
            'playlists' => $playlists,
            'route' => 'usergames.index',
        ]);
    }

    private function cacheUserPlaylists($userId, Collection $playlists)
    {
        $playlists->each(function ($playlist) use ($userId) {
            \Cache::add(
                'playlist_user_'.$userId.'_'.str_slug($playlist['name']),
                $playlist,
                now()->addWeek()
            );
        });
    }
}

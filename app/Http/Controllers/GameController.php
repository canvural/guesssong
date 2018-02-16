<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string  $playlistName
     * @param MusicService $spotify
     *
     * @return View|RedirectResponse
     */
    public function index(string $playlistName, MusicService $spotify)
    {
        $playlist = \Cache::get('playlist_'.$playlistName);

        \abort_if(null === $playlist, 404);

        $tracks = \Cache::remember($playlist['id'].'_tracks', now()->addDay(), function () use ($playlist, $spotify) {
            return $spotify->getTracksForPlaylist($playlist);
        });

        $recentlyPlayedTracks = \session('recently_played_tracks', []);

        $tracks = filter_tracks($tracks['items'], $recentlyPlayedTracks);

        if ($tracks->isEmpty()) {
            \session()->forget([
                'recently_played_tracks',
                'answer',
                'current_playlist',
            ]);

            return \redirect(\route('home'))
                ->with('flash', 'You played all the tracks. Choose another one!');
        }

        $answer = $tracks->random(1)->first();

        \session([
            'answer' => $answer['id'],
            'current_playlist' => $playlist['id'],
        ]);

        \session()->push('recently_played_tracks', $answer['id']);

        return \view('games.index')->with([
            'playlistId' => $playlist['id'],
            'tracks' => $tracks->toArray(),
            'current_song_url' => $answer['preview_url'],
            'playlistImage' => $playlist['images'][0]['url'],
        ]);
    }
    
    /**
     * Start a new game for the player.
     *
     * @param Request $request
     * @param string $playlistName
     *
     * @return JsonResponse
     */
    public function store(Request $request, string $playlistName): JsonResponse
    {
        $playlist = $request->input('playlist');
        
        if (! $this->checkValidPlaylist($playlistName, $playlist)) {
            return \response()->json([], 404);
        }
    
        \session([
            'last_game_answer_time' => \now()->timestamp
        ]);
    
        \auth()->user()->scores()->create([
            'score' => 0,
            'playlist_id' => $playlist
        ]);
        
        return \response()->json([], 200);
    }
    
    /**
     * @param string $playlistName
     * @param string $playlist
     * @return bool
     */
    private function checkValidPlaylist(string $playlistName, string $playlist): bool
    {
        return \session('current_playlist') === $playlist &&
            \Cache::has('playlist_' . $playlistName);
    }
}

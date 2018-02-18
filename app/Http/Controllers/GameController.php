<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string  $playlistName
     *
     * @return View|RedirectResponse
     */
    public function index(string $playlistName)
    {
        $playlist = \Cache::get('playlist_'.$playlistName);

        \abort_if(null === $playlist, 404);
    
        \session(['current_playlist' => $playlist['id']]);

        return \view('games.index')->with([
            'playlistId' => $playlist['id'],
            'playlistImage' => $playlist['images'][0]['url'],
        ]);
    }
    
    /**
     * Start a new game for the player.
     *
     * @param Request $request
     * @param string $playlistName
     * @param MusicService $spotify
     *
     * @return JsonResponse
     */
    public function store(Request $request, string $playlistName, MusicService $spotify): JsonResponse
    {
        $playlistId = $request->input('playlist');
        $playlist = \Cache::get('playlist_' . $playlistName);
        
        if (! $this->checkValidPlaylist($playlistName, $playlistId)) {
            return \response()->json([], 404);
        }
    
        $tracks = \Cache::remember($playlist['id'].'_tracks', now()->addDay(), function () use ($playlist, $spotify) {
            return $spotify->getTracksForPlaylist($playlist);
        });
    
        /** @var Collection $tracks */
        $tracks = $spotify->filterTracks($tracks['items'], \session('recently_played_tracks', []));

        $answer = $tracks->random();

        \session([
            'answer' => $answer['id'],
            'last_game_answer_time' => \now()->timestamp
        ]);

        \session()->push('recently_played_tracks', $answer['id']);
        
        $request->user()->scores()->create([
            'score' => 0,
            'playlist_id' => $playlistId
        ]);
        
        return \response()->json([
            'tracks' => $tracks->toArray(),
            'current_song_url' => $answer['preview_url'],
        ], 200);
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

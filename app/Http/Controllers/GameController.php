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
     * @param Request      $request
     * @param MusicService $musicService
     * @param string       $playlistId
     *
     * @return View|RedirectResponse
     */
    public function index(Request $request, MusicService $musicService, string $playlistId)
    {
        $userId = $this->resolveUserIdFromRequest($request);

        $playlist = $musicService->getUserPlaylist($userId, $playlistId);

        \session(['current_playlist' => $playlist['id']]);

        return \view('games.index')->with([
            'playlistId' => $playlist['id'],
            'playlistImage' => $playlist['images'][0]['url'],
        ]);
    }

    /**
     * Start a new game for the player.
     *
     * @param Request      $request
     * @param string       $playlistId
     * @param MusicService $musicService
     *
     * @return JsonResponse
     */
    public function store(Request $request, MusicService $musicService, string $playlistId): JsonResponse
    {
        $userId = $this->resolveUserIdFromRequest($request);

        $playlist = $musicService->getUserPlaylist($userId, $playlistId);

        if (! $this->isValidPlaylist($playlistId)) {
            return \response()->json([], 404);
        }

        $tracks = $musicService->getTracksForPlaylist($playlist);

        /** @var Collection $tracks */
        $tracks = $musicService->filterTracks($tracks['items'], \session('recently_played_tracks', []));

        $answer = $tracks->random();

        \session([
            'answer' => $answer['id'],
            'last_game_answer_time' => \now()->timestamp,
        ]);

        \session()->push('recently_played_tracks', $answer['id']);

        $request->user()->games()->create([
            'score' => 0,
            'playlist_id' => $playlistId,
        ]);

        return \response()->json([
            'tracks' => $tracks->toArray(),
            'current_song_url' => $answer['preview_url'],
        ], 200);
    }
}

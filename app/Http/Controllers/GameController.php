<?php

namespace App\Http\Controllers;

use App\Services\GameService;
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
     * @param Request $request
     * @param string  $playlistId
     *
     * @return View|RedirectResponse
     */
    public function create(Request $request, string $playlistId, MusicService $musicService)
    {
        $userId = $this->determineSpotifyUserIdFromRequest($request);

        $playlist = $musicService->getUserPlaylist($userId, $playlistId);

        \session(['current_playlist' => $playlist['id']]);

        return \view('games.create')->with([
            'playlistImage' => $playlist['images'][0]['url'],
        ]);
    }

    /**
     * Start a new game for the player.
     *
     * @param Request      $request
     * @param string       $playlistId
     * @param MusicService $musicService
     * @param GameService  $gameService
     *
     * @return JsonResponse
     */
    public function store(Request $request, string $playlistId, MusicService $musicService, GameService $gameService): JsonResponse
    {
        if (! $this->isValidPlaylist($playlistId)) {
            return \response()->json([], 404);
        }

        $playlist = $musicService->getUserPlaylist($this->determineSpotifyUserIdFromRequest($request), $playlistId);
        $allTracks = $gameService->transformTracksForGame($musicService->getPlaylistTracks($playlist));
        $gameTracks = $allTracks->take(4);
        $answer = $gameTracks->random();

        $this->setGameSession($answer['id']);
        $request->user()->startGame($playlistId);

        return \response()->json([
            'tracks' => $gameTracks,
            'current_song_url' => $answer['preview_url'],
        ], 200);
    }

    /**
     * @param $answer
     */
    private function setGameSession($answer): void
    {
        \session([
            'answer' => $answer,
            'last_game_answer_time' => \now()->timestamp,
        ]);

        \session()->push('recently_played_tracks', $answer);
    }
}

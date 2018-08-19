<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use App\Track;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    public function create(Request $request, string $playlistId, MusicService $musicService): View
    {
        $playlist = $musicService->getUserPlaylist($request->spotify_id, $playlistId);

        \session(['current_playlist' => $playlist->getId()]);

        return \view('games.create')->with([
            'playlistImage' => $playlist->getImageUrl(),
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
    public function store(Request $request, string $playlistId, MusicService $musicService): JsonResponse
    {
        if (! $this->isValidPlaylist($playlistId)) {
            return \response()->json([], 404);
        }

        $gameTracks = $musicService->getPlaylistTracks(
            $musicService->getUserPlaylist($request->spotify_id, $playlistId)
        )->shuffle()->take(4);

        /** @var Track $answer */
        $answer = $gameTracks->random();

        $this->setGameSession($answer->getId());
        $request->user()->startGame($playlistId);

        return \response()->json([
            'tracks' => $gameTracks,
            'current_song_url' => $answer->getPreviewUrl(),
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

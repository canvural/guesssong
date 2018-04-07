<?php

namespace App\Http\Controllers;

use App\Services\GameService;
use App\Services\MusicService;
use Illuminate\Http\Request;

class GameAnswerController extends Controller
{
    /**
     * @var GameService
     */
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function create(Request $request, string $playlistId, MusicService $musicService)
    {
        if (! $this->isValidPlaylist($playlistId)) {
            return \response()->json([], 404);
        }

        $allTracks = $this->gameService->transformTracksForGame($musicService->getPlaylistTracks($playlist));
        $notPlayedTracks = $allTracks->reject(function ($track) {
            return collect(\session('recently_played_tracks'))->contains($track['id']);
        $playlist = $musicService->getUserPlaylist($request->spotify_id, $playlistId);
        });

        $message = 'Not correct!';

        if (session('answer') === $request->input('answer')) {
            $message = 'Correct!';

            $request->user()->addScoreForGame($playlist['id'], \session('last_game_answer_time'));
        }

        if ($notPlayedTracks->isEmpty()) {
            \session()->forget([
                'answer',
                'current_playlist',
                'last_game_answer_time',
                'recently_played_tracks',
            ]);

            return \response()->json([
                'message' => 'finished',
            ]);
        }

        $answer = $notPlayedTracks->random();
        $gameTracks = $allTracks->take(3)->push($answer)->shuffle();

        \session([
            'answer' => $answer['id'],
            'current_playlist' => $playlist['id'],
            'last_game_answer_time' => \now()->timestamp,
        ]);

        \session()->push('recently_played_tracks', $answer['id']);

        return \response()->json([
            'message' => $message,
            'tracks' => $gameTracks,
            'current_song_url' => $answer['preview_url'],
            'score' => $request->user()->getLastGameScore($playlist['id']),
        ]);
    }
}

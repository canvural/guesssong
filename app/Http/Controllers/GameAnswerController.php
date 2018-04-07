<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use App\Track;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameAnswerController extends Controller
{
    public function create(Request $request, string $playlistId, MusicService $musicService)
    {
        if (! $this->isValidPlaylist($playlistId)) {
            return \response()->json([], 404);
        }

        $playlist = $musicService->getUserPlaylist($request->spotify_id, $playlistId);
        $allTracks = $musicService->getPlaylistTracks($playlist);
        $notPlayedTracks = $allTracks->reject(function (Track $track) {
            return \collect(\session('recently_played_tracks'))->contains($track->getId());
        });

        $message = 'Not correct!';

        if (session('answer') === $request->input('answer')) {
            $message = 'Correct!';

            $request->user()->addScoreForGame($playlist->getId(), \session('last_game_answer_time'));
        }

        if ($notPlayedTracks->isEmpty()) {
            return $this->gameFinished();
        }

        /** @var Track $answer */
        $answer = $notPlayedTracks->random();
        $gameTracks = $allTracks->take(3)->push($answer)->shuffle();

        \session([
            'answer' => $answer->getId(),
            'current_playlist' => $playlist->getId(),
            'last_game_answer_time' => \now()->timestamp,
        ]);

        \session()->push('recently_played_tracks', $answer->getId());

        return \response()->json([
            'message' => $message,
            'tracks' => $gameTracks,
            'current_song_url' => $answer->getPreviewUrl(),
            'score' => $request->user()->getLastGameScore($playlist->getId()),
        ]);
    }

    /**
     * @return JsonResponse
     */
    private function gameFinished(): JsonResponse
    {
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
}

<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use Illuminate\Http\Request;

class GameAnswerController extends Controller
{
    public function create(Request $request, string $playlistName, MusicService $spotify)
    {
        $playlistPrefix = $this->getPlaylistPrefix($request);
        $playlistId = $request->input('playlist');
        $playlist = \Cache::get($playlistPrefix.$playlistName);

        if (! $this->checkValidPlaylist($playlistPrefix, $playlistName, $playlistId)) {
            return \response()->json([], 404);
        }

        $tracks = \Cache::get($playlist['id'].'_tracks');

        $message = 'Not correct!';

        if (session('answer') === $request->input('answer')) {
            $message = 'Correct!';

            $request->user()->addScoreForGame($playlist['id'], \session('last_game_answer_time'));
        }

        $tracks = $spotify->filterTracks($tracks['items'], \session('recently_played_tracks'));

        if ($tracks->isEmpty()) {
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

        $answer = $tracks->random();

        \session([
            'answer' => $answer['id'],
            'current_playlist' => $playlist['id'],
            'last_game_answer_time' => \now()->timestamp,
        ]);

        \session()->push('recently_played_tracks', $answer['id']);

        return \response()->json([
            'message' => $message,
            'tracks' => $tracks->toArray(),
            'score' => $request->user()->games()->lastGameWithPlaylistId($playlist['id'])->select('score')->first()->score,
            'current_song_url' => $answer['preview_url'],
        ]);
    }
}

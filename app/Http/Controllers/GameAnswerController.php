<?php

namespace App\Http\Controllers;

use App\Events\UserAnsweredRight;
use Illuminate\Http\Request;

class GameAnswerController extends Controller
{
    public function create(Request $request, string $playlistName)
    {
        $playlist = \Cache::get('playlist_'.$playlistName);
        $tracks = \Cache::get($playlist['id'].'_tracks');

        $message = 'Not correct!';

        if (session('answer') === $request->input('answer')) {
            $message = 'Correct!';

            \event(new UserAnsweredRight(auth()->user(), $playlist));
        }

        $tracks = filter_tracks($tracks['items'], \session('recently_played_tracks'));

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

        $answer = $tracks->random(1)->first();

        \session([
            'answer' => $answer['id'],
            'current_playlist' => $playlist['id'],
            'last_game_answer_time' => \now()->timestamp,
        ]);

        \session()->push('recently_played_tracks', $answer['id']);

        return \response()->json([
            'message' => $message,
            'tracks' => $tracks->toArray(),
            'score' => \auth()->user()->scores()->select('score')->where('playlist_id', '=', $playlist['id'])->latest()->first()->score,
            'current_song_url' => $answer['preview_url'],
        ]);
    }
}

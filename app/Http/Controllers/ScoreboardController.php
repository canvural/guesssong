<?php

namespace App\Http\Controllers;

use App\Game;
use App\User;
use Illuminate\Http\Request;

class ScoreboardController extends Controller
{
    public function index(Request $request)
    {
        $games = Game::with('user')->withTotalScore()->paginate();
        
        return \view('scoreboard.index', [
            'games' => $games
        ]);
    }
}

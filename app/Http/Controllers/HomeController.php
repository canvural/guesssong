<?php

namespace App\Http\Controllers;

use App\Services\Spotify;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the home screen.
     *
     * @param Spotify $spotify
     *
     * @return View
     */
    public function index(Spotify $spotify): View
    {
        $categories = collect(
            $spotify->getPlaylistCategoriesForGame()
        )
            ->shuffle()
            ->take(10)
            ->toArray();
    
        return view('home')->with(compact('categories'));
    }
}

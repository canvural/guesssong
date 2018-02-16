<?php

namespace App\Http\Controllers;

use App\Services\Spotify;
use Illuminate\View\View;

class CategoryController extends Controller
{
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

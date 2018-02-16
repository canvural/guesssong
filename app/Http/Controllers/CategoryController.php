<?php

namespace App\Http\Controllers;

use App\Services\Spotify;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Show the categories.
     *
     * @param Spotify $spotify
     *
     * @return View
     */
    public function index(Spotify $spotify): View
    {
        dd(json_encode($spotify->getPlaylistCategoriesForGame(), JSON_UNESCAPED_SLASHES));
        $categories = collect(
            $spotify->getPlaylistCategoriesForGame()
        )
            ->shuffle()
            ->take(10)
            ->toArray();
    
        return view('home')->with(compact('categories'));
    }
}

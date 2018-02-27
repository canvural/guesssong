<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Show the home screen.
     *
     * @param MusicService $spotify
     *
     * @return View
     */
    public function index(MusicService $spotify): View
    {
        $categories = collect($spotify->getPlaylistCategoriesForGame())->shuffle();

        return view('home')->with(compact('categories'));
    }
}

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
        $categories = collect(
            \Cache::rememberForever('categories', function () use ($spotify) {
                return $spotify->getPlaylistCategoriesForGame();
            })
        )
            ->shuffle()
            ->toArray();

        return view('home')->with(compact('categories'));
    }
}

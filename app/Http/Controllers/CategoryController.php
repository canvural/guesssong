<?php

namespace App\Http\Controllers;

use App\Services\MusicService;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Show the home screen.
     *
     * @param MusicService $musicService
     *
     * @return View
     */
    public function index(MusicService $musicService): View
    {
        $categories = collect($musicService->getPlaylistCategoriesForGame())->shuffle();

        return view('home')->with(compact('categories'));
    }
}

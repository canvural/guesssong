<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function isValidPlaylist(string $playlistId): bool
    {
        return \session('current_playlist') === $playlistId;
    }

    protected function isUserGame(Request $request): bool
    {
        return \starts_with($request->route()->getName(), 'usergame');
    }
}

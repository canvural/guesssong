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

    protected function resolveUserIdFromRequest(Request $request)
    {
        if ($request->has('u')) {
            return $request->query('u');
        }

        return $this->isUserGame($request) ?
            $request->user()->socialLogin->spotify_id :
            'spotify';
    }
}

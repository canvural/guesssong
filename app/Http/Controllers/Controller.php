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

    protected function checkValidPlaylist(string $playlistPrefix, string $playlistName, string $playlistId): bool
    {
        return \session('current_playlist') === $playlistId &&
            \Cache::has($playlistPrefix.$playlistName);
    }

    protected function getPlaylistPrefix(Request $request): string
    {
        $playlistPrefix = 'playlist_';

        if (\starts_with($request->route()->getName(), 'usergame')) {
            $playlistPrefix .= 'user_'.$request->user()->id.'_';
        }

        return $playlistPrefix;
    }
}

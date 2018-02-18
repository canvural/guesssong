<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * @param string $playlistName
     * @param string $playlistId
     *
     * @return bool
     */
    protected function checkValidPlaylist(string $playlistName, string $playlistId): bool
    {
        return \session('current_playlist') === $playlistId &&
            \Cache::has('playlist_' . $playlistName);
    }
}

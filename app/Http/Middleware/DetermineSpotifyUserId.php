<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetermineSpotifyUserId
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('u')) {
            $spotifyId = $request->query('u');
        } elseif ($this->isUserGame($request)) {
            $spotifyId = $request->user()->socialLogin->spotify_id;
        } else {
            $spotifyId = 'spotify';
        }

        $request->merge([
            'spotify_id' => $spotifyId,
        ]);

        return $next($request);
    }

    protected function isUserGame(Request $request): bool
    {
        return \starts_with($request->route()->getName(), 'usergame');
    }
}

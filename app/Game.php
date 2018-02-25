<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the users last played game with the given playlist.
     *
     * @param Builder $query
     * @param string  $playlistId
     *
     * @return Builder
     */
    public function scopeLastGameWithPlaylistId(Builder $query, string $playlistId): Builder
    {
        return $query->where('playlist_id', '=', $playlistId)->latest()->limit(1);
    }
}

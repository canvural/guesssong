<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Game
 *
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game lastGameWithPlaylistId($playlistId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game withTotalScore()
 * @mixin \Eloquent
 */
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
        return $query
            ->where('playlist_id', '=', $playlistId)
            ->latest()
            ->latest('id')
            ->limit(1);
    }

    public function scopeWithTotalScore($query)
    {
        return $query->selectRaw('user_id, SUM(score) as totalScore, COUNT(user_id) as gamesPlayed')
            ->groupBy('user_id')
            ->orderByDesc('totalScore');
    }
}

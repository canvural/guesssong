<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }
    
    public function addScoreForGame(string $playlistId, $lastAnswerTime): self
    {
        $now = \now()->timestamp;
        $timeDiff = $now - $lastAnswerTime;
        
        // Timeout
        if ($timeDiff > 30) {
            return $this;
        }
        
        $score = (30 - $timeDiff) * 5;
    
        $this
            ->games()
            ->lastGameWithPlaylistId($playlistId)
            ->increment('score', $score);
        
        return $this;
    }
}

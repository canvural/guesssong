<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    protected $eagerLoad = ['socialLogin'];

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

    public function socialLogin()
    {
        return $this->hasOne(SocialLogin::class);
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

    public function startGame($playlistId): Game
    {
        return $this->games()->create([
            'score' => 0,
            'playlist_id' => $playlistId,
        ]);
    }

    public function getLastGameScore($playlistId)
    {
        return $this->games()->lastGameWithPlaylistId($playlistId)->select('score')->first()->score;
    }
}

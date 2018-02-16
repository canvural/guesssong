<?php

namespace App\Listeners;

use App\Events\UserAnsweredRight;

class UpdateScore
{
    /**
     * Handle the event.
     *
     * @param  UserAnsweredRight  $event
     * @return void
     */
    public function handle(UserAnsweredRight $event): void
    {
        $now = \now()->timestamp;
        $lastAnswerTime = \session('last_game_answer_time');
    
        $score = (30 - ($now - $lastAnswerTime)) * 5;
        
        $event
            ->user
            ->scores()
            ->where(
                'playlist_id',
                '=',
                $event->playlist['id']
            )
            ->latest()
            ->limit(1)
            ->increment('score', $score);
    }
}

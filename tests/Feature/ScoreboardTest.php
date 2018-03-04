<?php

namespace Tests\Feature;

use App\Game;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreboardTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function it_will_show_scores_of_users_in_descending_order()
    {
        $userA = \create(User::class);
        $userB = \create(User::class);
        $userC = \create(User::class);
        
        $gameA = \create(Game::class, ['user_id' => $userA->id, 'score' => 100]);
        $gameB = \create(Game::class, ['user_id' => $userB->id, 'score' => 150]);
        $gameC = \create(Game::class, ['user_id' => $userC->id, 'score' => 200]);
        $gameD = \create(Game::class, ['user_id' => $userA->id, 'score' => 150]);
        
        $response = $this->get('scoreboard');
    
        $response->assertStatus(200);
        
        $games = $response->getOriginalContent()->getData()['games'];
    
        $this->assertEquals([250, 200, 150], $games->pluck('totalScore')->toArray());
        $this->assertEquals([2, 1, 1], $games->pluck('gamesPlayed')->toArray());
        $this->assertEquals([
            $userA->id,
            $userC->id,
            $userB->id
        ], $games->pluck('user.id')->toArray());
    }
}

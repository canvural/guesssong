<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function can_start_a_game()
    {
        $user = \create(User::class);
        
        $game = $user->startGame('playlist-1');
        
        $this->assertEquals(0, $game->score);
        $this->assertEquals('playlist-1', $game->playlist_id);
    }
}

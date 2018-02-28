<?php

namespace Tests\Unit;

use App\Game;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_the_last_game_when_a_user_has_more_than_one_game_with_same_playlist()
    {
        $user = create(User::class);
        $game1 = \create(Game::class, ['user_id' => $user->id, 'playlist_id' => 'playlist-1']);
        $game2 = \create(Game::class, ['user_id' => $user->id, 'playlist_id' => 'playlist-1']);

        $this->assertEquals($game2->id, Game::lastGameWithPlaylistId('playlist-1')->first()->id);
    }
}

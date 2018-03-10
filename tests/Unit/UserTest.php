<?php

namespace Tests\Unit;

use App\Game;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_start_a_game()
    {
        $user = \create(User::class);

        $game = $user->startGame('playlist-1');

        $this->assertEquals(0, $game->score);
        $this->assertEquals('playlist-1', $game->playlist_id);
    }

    /** @test */
    public function can_change_score()
    {
        /** @var User $user */
        $user = create(User::class);
        $game = $user->startGame('playlist-1');

        $now = $this->setCarbonTest();
        $this->progressTime(0, 10);

        $user->addScoreForGame('playlist-1', $now->timestamp);

        $this->assertEquals(100, $game->fresh()->score);
    }

    /** @test */
    public function it_will_only_add_score_to_the_most_recent_game_for_the_same_playlist()
    {
        /** @var User $user */
        $user = create(User::class);
        $game1 = $user->startGame('playlist-1');
        $game2 = $user->startGame('playlist-1');

        $now = $this->setCarbonTest();
        $this->progressTime(0, 10);

        $user->addScoreForGame('playlist-1', $now->timestamp);

        $this->assertEquals(0, $game1->fresh()->score);
        $this->assertEquals(100, $game2->fresh()->score);
    }

    /** @test */
    public function it_wont_update_score_if_answered_in_more_than_30_seconds()
    {
        /** @var User $user */
        $user = create(User::class);
        $game = $user->startGame('playlist-1');

        $now = $this->setCarbonTest();
        $this->progressTime(0, 31);

        $user->addScoreForGame('playlist-1', $now->timestamp);

        $this->assertEquals(0, $game->fresh()->score);
    }

    /** @test */
    public function it_can_fetch_a_list_of_playlists_and_count_of_how_many_times_its_played()
    {
        $user = \create(User::class);
        $user2 = \create(User::class);
        $gameA = \create(Game::class, [
            'playlist_id' => 'playlist-1',
            'user_id' => $user->id,
        ]);
        $gameB = \create(Game::class, [
            'playlist_id' => 'playlist-2',
            'user_id' => $user->id,
        ]);
        $gameC = \create(Game::class, [
            'playlist_id' => 'playlist-1',
            'user_id' => $user->id,
        ]);
        $gameD = \create(Game::class, [
            'playlist_id' => 'playlist-1',
            'user_id' => $user2->id,
        ]);

        $this->assertEquals([
            'playlist-1' => 2,
            'playlist-2' => 1,
        ], $user->getPlayedPlaylistCounts());
    }
}

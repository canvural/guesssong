<?php

namespace Tests\Feature;

use App\Game;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameAnswerTest extends TestCase
{
    use RefreshDatabase;

    private $playlist;
    private $tracks;

    public function setUp()
    {
        parent::setUp();

        $this->playlist = \get_playlist('rock-hard');
        $this->tracks = \get_fake_data($this->playlist['id'].'_tracks.json');
    }

    /** @test */
    public function guests_can_not_answer_a_game()
    {
        $response = $this
            ->post(\route('gameAnswers.create', 'rock-hard'), [
                'answer' => 'an-answer',
            ]);

        $response->assertRedirect(\route('login'));
    }

    /** @test */
    public function answering_a_game_with_different_playlist_than_the_one_in_session_will_fail()
    {
        $this->actingAs(\create(User::class));

        $response = $this->withoutExceptionHandling()->withSession([
            'answer' => 'correct-answer',
            'current_playlist' => $this->playlist['id'],
        ])->post(\route('gameAnswers.create', 'rock-hard'), [
            'answer' => 'an-answer',
            'playlist' => 'random-playlist',
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function users_score_will_not_change_when_given_incorrect_answer()
    {
        /** @var User $user */
        $user = \create(User::class);

        /** @var Game $usersGame */
        $usersGame = $this->createGameForUser($user);

        $this
            ->withoutExceptionHandling()
            ->actingAs($user)
            ->withSession([
                'answer' => 'correct-answer',
                'recently_played_tracks' => [],
                'current_playlist' => $this->playlist['id'],
            ])
            ->withPlaylistCache($this->playlist)
            ->withPlaylistCacheExistence($this->playlist)
            ->withPlaylistTracksCache($this->playlist, $this->tracks)
            ->post(\route('gameAnswers.create', 'rock-hard'), [
                'answer' => 'incorrect-answer',
                'playlist' => $this->playlist['id'],
            ]);

        $this->assertSame($usersGame->score, (int) $usersGame->fresh()->score);
    }

    /** @test */
    public function user_should_gain_score_when_answered_right()
    {
        $now = $this->setCarbonTest();

        /** @var User $user */
        $user = \create(User::class);

        $usersGame = $this->createGameForUser($user);

        $this
            ->withoutExceptionHandling()
            ->actingAs($user)
            ->withSession([
                'answer' => 'correct-answer',
                'recently_played_tracks' => [],
                'last_game_answer_time' => $now->timestamp,
                'current_playlist' => $this->playlist['id'],
            ])
            ->withPlaylistCache($this->playlist)
            ->withPlaylistCacheExistence($this->playlist)
            ->withPlaylistTracksCache($this->playlist, $this->tracks)
            ->progressTime(0, 5)
            ->post(\route('gameAnswers.create', 'rock-hard'), [
                'answer' => 'correct-answer',
                'playlist' => $this->playlist['id'],
            ]);

        // Guessed in 5 seconds hence 125 points
        $this->assertEquals(125, $usersGame->fresh()->score);
    }

    /** @test */
    public function user_should_not_gain_score_when_answered_after_30_seconds()
    {
        $now = $this->setCarbonTest();

        /** @var User $user */
        $user = \create(User::class);

        $usersGame = $this->createGameForUser($user);

        $this
            ->withoutExceptionHandling()
            ->actingAs($user)
            ->withSession([
                'answer' => 'correct-answer',
                'recently_played_tracks' => [],
                'last_game_answer_time' => $now->timestamp,
                'current_playlist' => $this->playlist['id'],
            ])
            ->withPlaylistCache($this->playlist)
            ->withPlaylistCacheExistence($this->playlist)
            ->withPlaylistTracksCache($this->playlist, $this->tracks)
            ->progressTime(0, 31)
            ->post(\route('gameAnswers.create', 'rock-hard'), [
                'answer' => 'correct-answer',
                'playlist' => $this->playlist['id'],
            ]);

        $this->assertEquals($usersGame->score, $usersGame->fresh()->score);
    }

    /**
     * @param $user
     *
     * @return Game
     */
    private function createGameForUser($user): Game
    {
        return \create(Game::class, [
            'user_id' => $user->id,
            'score' => 0,
            'playlist_id' => $this->playlist['id'],
        ]);
    }
}

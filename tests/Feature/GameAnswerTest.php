<?php

namespace Tests\Feature;

use App\Game;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SpotifyWebAPI\SpotifyWebAPIException;
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
            ->post(\route('gameAnswers.create', [$this->playlist['id'], 'rock-hard']), [
                'answer' => 'an-answer',
            ]);

        $response->assertRedirect(\route('login'));
    }

    /** @test */
    public function answering_a_game_with_different_playlist_than_the_one_in_session_will_fail()
    {
        $this->expectException(SpotifyWebAPIException::class);

        $this->actingAs(\create(User::class));

        $response = $this->withoutExceptionHandling()->withSession([
            'answer' => 'correct-answer',
            'current_playlist' => $this->playlist['id'],
        ])->post(\route('gameAnswers.create', ['not-valid-playlist-id', 'rock-hard']), [
            'answer' => 'an-answer',
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
            ->post(\route('gameAnswers.create', [$this->playlist['id'], 'rock-hard']), [
                'answer' => 'incorrect-answer',
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
            ->progressTime(0, 5)
            ->post(\route('gameAnswers.create', [$this->playlist['id'], 'rock-hard']), [
                'answer' => 'correct-answer',
            ])
            ->assertStatus(200);

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
            ->progressTime(0, 31)
            ->post(\route('gameAnswers.create', [$this->playlist['id'], 'rock-hard']), [
                'answer' => 'correct-answer',
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

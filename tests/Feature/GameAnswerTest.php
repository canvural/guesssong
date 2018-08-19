<?php

namespace Tests\Feature;

use App\Game;
use App\Playlist;
use App\Track;
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

        $this->playlist = Playlist::createFromSpotifyData(get_playlist('rock-hard'));
        $this->tracks = collect(get_fake_data($this->playlist->getId().'_tracks.json')['items'])->map(function ($track) {
            return Track::createFromSpotifyData($track['track']);
        })->filter();
    }

    /** @test */
    public function guests_can_not_answer_a_game()
    {
        $response = $this
            ->post(\route('gameAnswers.store', [$this->playlist->getId(), 'rock-hard']), [
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
            'current_playlist' => $this->playlist->getId(),
        ])->post(\route('gameAnswers.store', ['not-valid-playlist-id', 'rock-hard']), [
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
                'current_playlist' => $this->playlist->getId(),
            ])
            ->post(\route('gameAnswers.store', [$this->playlist->getId(), 'rock-hard']), [
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
                'current_playlist' => $this->playlist->getId(),
            ])
            ->progressTime(0, 5)
            ->post(\route('gameAnswers.store', [$this->playlist->getId(), 'rock-hard']), [
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
                'current_playlist' => $this->playlist->getId(),
            ])
            ->progressTime(0, 31)
            ->post(\route('gameAnswers.store', [$this->playlist->getId(), 'rock-hard']), [
                'answer' => 'correct-answer',
            ]);

        $this->assertEquals($usersGame->score, $usersGame->fresh()->score);
    }

    /** @test */
    public function it_should_not_have_duplicate_tracks_in_game_tracks()
    {
        $now = $this->setCarbonTest();

        /** @var User $user */
        $user = \create(User::class);

        $usersGame = $this->createGameForUser($user);

        $response = $this
            ->withoutExceptionHandling()
            ->actingAs($user)
            ->withSession([
                'recently_played_tracks' => [],
                'last_game_answer_time' => $now->timestamp,
                'current_playlist' => $this->playlist->getId(),
            ])
            ->post(\route('gameAnswers.store', [$this->playlist->getId(), 'rock-hard']), [
                'answer' => 'correct-answer',
            ]);

        //\dd($response->original);
    }

    /**
     * @param $user
     *
     * @return Game
     */
    public function createGameForUser($user): Game
    {
        return \create(Game::class, [
            'user_id' => $user->id,
            'score' => 0,
            'playlist_id' => $this->playlist->getId(),
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Playlist;
use App\Track;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class GamesTest extends TestCase
{
    use RefreshDatabase;

    /** @var Playlist */
    private $playlist;

    public function setUp()
    {
        parent::setUp();
    
        $this->playlist = Playlist::createFromSpotifyData(get_playlist('rock-hard'));
    }

    /** @test */
    public function guests_can_not_play_a_game()
    {
        $response = $this->get(\route('games.create', 'rock-hard'));

        $response
            ->assertRedirect('/login')
            ->assertSessionMissing('current_playlist');
    }

    /** @test */
    public function logged_in_users_can_play_game()
    {
        $response = $this
            ->actingAs(\create(User::class))
            ->get(\route('games.create', $this->playlist->getId()));

        $response
            ->assertStatus(200)
            ->assertSessionHas('current_playlist', $this->playlist->getId());
    }

    /** @test */
    public function it_will_return_not_found_error_when_playlist_doesnt_exists()
    {
        $this->actingAs(\factory(User::class)->create());

        $response = $this
            ->get(\route('games.create', 'not-a-valid-playlist-id'));

        $response->assertStatus(404);
    }

    /** @test */
    public function a_user_can_start_a_game()
    {
        $now = $this->setCarbonTest();

        $user = \create(User::class);

        $response = $this
            ->actingAs($user)
            ->withoutExceptionHandling()
            ->withSession([
                'current_playlist' => $this->playlist->getId(),
                'recently_played_tracks' => [],
            ])
            ->post(\route('games.store', $this->playlist->getId()));

        $response
            ->assertStatus(200)
            ->assertSessionHas('answer')
            ->assertSessionHas('current_playlist', $this->playlist->getId())
            ->assertSessionHas('last_game_answer_time', $now->timestamp)
            ->assertSessionHas('recently_played_tracks')
            ->assertJsonStructure([
                'tracks',
                'current_song_url',
            ]);

        $this->assertDatabaseHas('games', [
            'score' => 0,
            'user_id' => $user->id,
            'playlist_id' => $this->playlist->getId(),
        ]);
    }

    /** @test */
    public function starting_a_game_with_different_playlist_than_the_one_in_session_will_fail()
    {
        $this->actingAs(\create(User::class));

        $response = $this->withoutExceptionHandling()->withSession([
            'current_playlist' => $this->playlist->getId(),
        ])->post(\route('games.store', 'wrong-playlist-id'));

        $response->assertStatus(404);
    }
}

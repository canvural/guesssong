<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamesTest extends TestCase
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
    public function guests_can_not_play_a_game()
    {
        $response = $this->get(\route('games.index', 'rock-hard'));

        $response
            ->assertRedirect('/login')
            ->assertSessionMissing('current_playlist');
    }

    /** @test */
    public function logged_in_users_can_play_game()
    {
        $response = $this
            ->actingAs(\create(User::class))
            ->withPlaylistCache($this->playlist)
            ->get(\route('games.index', 'rock-hard'));

        $response
            ->assertStatus(200)
            ->assertSessionHas('current_playlist', $this->playlist['id']);
    }

    /** @test */
    public function not_cached_playlists_will_return_not_found_error()
    {
        $this->actingAs(\factory(User::class)->create());

        $response = $this->get(\route('games.index', 'rock-hard'));

        $response->assertStatus(404);
    }

    /** @test */
    public function current_game_playlist_is_stored_in_session()
    {
        $this->actingAs(\create(User::class));

        $response = $this
            ->withPlaylistCache($this->playlist)
            ->get(\route('games.index', 'rock-hard'));

        $response->assertSessionHas('current_playlist', $this->playlist['id']);
    }

    /** @test */
    public function a_user_can_start_a_game()
    {
        $now = $this->setCarbonTest();

        $user = \create(User::class);

        $this->actingAs($user);

        $response = $this
            ->withoutExceptionHandling()
            ->withSession([
                'current_playlist' => $this->playlist['id'],
                'recently_played_tracks' => [],
            ])
            ->withPlaylistCache($this->playlist)
            ->withPlaylistCacheExistence($this->playlist)
            ->withPlaylistTracksCacheRemember($this->tracks)
            ->post(\route('games.store', 'rock-hard'), [
                'playlist' => $this->playlist['id'],
            ]);

        $response
            ->assertStatus(200)
            ->assertSessionHas('answer')
            ->assertSessionHas('current_playlist', $this->playlist['id'])
            ->assertSessionHas('last_game_answer_time', $now->timestamp)
            ->assertSessionHas('recently_played_tracks')
            ->assertJsonStructure([
                'tracks',
                'current_song_url',
            ]);

        $this->assertDatabaseHas('games', [
            'score' => 0,
            'user_id' => $user->id,
            'playlist_id' => $this->playlist['id'],
        ]);
    }

    /** @test */
    public function starting_a_game_with_different_playlist_than_the_one_in_session_will_fail()
    {
        $this->actingAs(\create(User::class));

        $response = $this->withoutExceptionHandling()->withSession([
            'current_playlist' => $this->playlist['id'],
        ])->post(\route('games.store', 'rock-hard'), [
            'playlist' => 'wrong-playlist-id',
        ]);

        $response->assertStatus(404);
    }
}

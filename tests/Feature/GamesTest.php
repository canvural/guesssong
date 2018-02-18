<?php

namespace Tests\Feature;

use App\Services\MusicService;
use App\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\Fakes\SpotifyFake;
use Tests\TestCase;

class GamesTest extends TestCase
{
    use RefreshDatabase;
    
    private $playlist;
    private $tracks;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app->bind(MusicService::class, SpotifyFake::class);
    
        $this->playlist = \get_playlist('rock-hard');
        $this->tracks = \get_fake_data($this->playlist['id'] . '_tracks.json');
    }
    
    /** @test */
    function guests_can_not_play_a_game()
    {
        $response = $this->get(\route('games.index', 'rock-hard'));
    
        $response->assertRedirect('/login');
    }
    
    /** @test */
    function logged_in_users_can_play_game()
    {
        $this->actingAs(\create(User::class));
        
        Cache::shouldReceive('get')
            ->once()
            ->with('playlist_rock-hard')
            ->andReturn($this->playlist);
    
        $response = $this->get(\route('games.index', 'rock-hard'));
        
        $response->assertStatus(200);
    }
    
    /** @test */
    function not_cached_playlists_will_return_not_found_error()
    {
        $this->actingAs(\factory(User::class)->create());
        
        $response = $this->get(\route('games.index', 'rock-hard'));
        
        $response->assertStatus(404);
    }
    
    /** @test */
    function current_game_playlist_is_stored_in_session()
    {
        $this->actingAs(\create(User::class));
    
        Cache::shouldReceive('get')
            ->once()
            ->with('playlist_rock-hard')
            ->andReturn($this->playlist);
    
        $response = $this->get(\route('games.index', 'rock-hard'));
    
        $response->assertSessionHas('current_playlist', $this->playlist['id']);
    }
    
    /** @test */
    function a_user_can_start_a_game()
    {
        // Setup Carbon for testing
        $now = Carbon::create(2018, 1, 1);
        Carbon::setTestNow($now);
        
        $user = \create(User::class);
        
        $this->actingAs($user);
    
        Cache::shouldReceive('get')
            ->once()
            ->with('playlist_rock-hard')
            ->andReturn($this->playlist);
        
        Cache::shouldReceive('has')
            ->once()
            ->with('playlist_rock-hard')
            ->andReturnTrue();
        
        Cache::shouldReceive('remember')
            ->once()
            ->withAnyArgs()
            ->andReturn($this->tracks);
        
        $response = $this->withoutExceptionHandling()->withSession([
            'current_playlist' => $this->playlist['id'],
            'recently_played_tracks' => []
        ])->post(\route('games.store', 'rock-hard'), [
            'playlist' => $this->playlist['id']
        ]);
        
        $response
            ->assertStatus(200)
            ->assertSessionHas('answer')
            ->assertSessionHas('current_playlist', $this->playlist['id'])
            ->assertSessionHas('last_game_answer_time', $now->timestamp)
            ->assertSessionHas('recently_played_tracks')
            ->assertJsonStructure([
                'tracks',
                'current_song_url'
            ]);
    
        $this->assertDatabaseHas('scores', [
            'score' => 0,
            'user_id' => $user->id,
            'playlist_id' => $this->playlist['id']
        ]);
    }
    
    /** @test */
    function starting_a_game_with_different_playlist_than_the_one_in_session_will_fail()
    {
        $this->actingAs(\create(User::class));
    
        $response = $this->withoutExceptionHandling()->withSession([
            'current_playlist' => $this->playlist['id']
        ])->post(\route('games.store', 'rock-hard'), [
            'playlist' => 'wrong-playlist-id'
        ]);
        
        $response->assertStatus(404);
    }
}

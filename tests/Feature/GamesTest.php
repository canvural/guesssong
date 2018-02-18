<?php

namespace Tests\Feature;

use App\Services\MusicService;
use App\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\Fakes\SpotifyFake;
use Tests\TestCase;

class GamesTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app->bind(MusicService::class, SpotifyFake::class);
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
        
        $playlist = \get_playlist('rock-hard');
        
        Cache::shouldReceive('get')
            ->once()
            ->with('playlist_rock-hard')
            ->andReturn($playlist);
    
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
    
        $playlist = \get_playlist('rock-hard');
    
        Cache::shouldReceive('get')
            ->once()
            ->with('playlist_rock-hard')
            ->andReturn($playlist);
    
        $response = $this->get(\route('games.index', 'rock-hard'));
    
        $response->assertSessionHas('current_playlist', $playlist['id']);
    }
}

<?php

namespace Tests\Feature;

use App\Services\MusicService;
use Tests\Fakes\SpotifyFake;
use Tests\TestCase;

class PlaylistsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $this->app->bind(MusicService::class, SpotifyFake::class);
    }
    
    /** @test */
    function it_can_list_playlists_for_a_category()
    {
        $playlists = \get_fake_data('rock_playlists.json');
        
        $response = $this->withExceptionHandling()->get(\route('playlists.show', 'rock'));
        
        \collect($playlists)->each(function ($playlist) use ($response) {
            $response->assertSee($playlist['name']);
        });
    }
}

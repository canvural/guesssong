<?php

namespace Tests\Feature;

use Tests\TestCase;

class PlaylistsTest extends TestCase
{
    /** @test */
    public function it_can_list_playlists_for_a_category()
    {
        $playlists = \get_fake_data('rock_playlists.json');

        $response = $this->withExceptionHandling()->get(\route('playlists.index', 'rock'));

        \collect($playlists)->each(function ($playlist) use ($response) {
            $response->assertSee($playlist['name']);
        });
    }
}

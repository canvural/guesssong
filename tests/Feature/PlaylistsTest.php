<?php

namespace Tests\Feature;

use App\Playlist;
use Tests\TestCase;

class PlaylistsTest extends TestCase
{
    /** @test */
    public function it_can_list_playlists_for_a_category()
    {
        $playlists = collect(get_fake_data('rock_playlists.json'))
            ->map(function ($playlist) {
                return Playlist::createFromSpotifyData($playlist);
            });

        $response = $this->withExceptionHandling()->get(route('playlists.index', 'rock'));

        $playlists->each(function (Playlist $playlist) use ($response) {
            $response->assertSee($playlist->getName());
        });
    }
}

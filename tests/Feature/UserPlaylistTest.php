<?php

namespace Tests\Feature;

use App\SocialLogin;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPlaylistTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function users_logged_in_with_spotify_can_see_their_own_playlists()
    {
        $user = \create(User::class);
        \create(SocialLogin::class, [
            'user_id' => $user->id,
        ]);

        $response = $this
            ->withoutExceptionHandling()
            ->actingAs($user)
            ->get(\route('userplaylists.index'));

        $response->assertStatus(200);
        $this->assertEquals(
            \get_fake_data('user_playlists.json'),
            $response->original->getData()['playlists']->toArray()
        );
    }
}

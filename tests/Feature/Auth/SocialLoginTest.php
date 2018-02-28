<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Factory as Socialite;
use SocialiteProviders\Spotify\Provider as SpotifyProvider;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @dataProvider socialLoginAndRedirectUrlProvider
     *
     * @param $socialLogin
     * @param $redirectUrl
     */
    public function it_redirects_to_correct_provider_url($socialLogin, $redirectUrl)
    {
        $response = $this->call('GET', "/login/$socialLogin");

        $this->assertContains($redirectUrl, $response->getTargetUrl());
    }

    /** @test
     * @throws \ReflectionException
     */
    public function it_retrieves_spotify_request_and_creates_a_new_user()
    {
        // Mock the Facade and return a User Object with the email 'foo@bar.com'
        $this->mockSocialiteFacade($this->dummyUser([
            'email' => 'foo@bar.com',
            'token' => 'example-token',
            'refreshToken' => 'example-refresh-token',
        ]), SpotifyProvider::class);

        $this->get('/login/spotify/callback')->assertRedirect('/categories');

        $this->assertDatabaseHas('users', [
            'email' => 'foo@bar.com',
        ]);

        \tap(User::first(), function ($user) {
            $this->assertEquals('example-token', $user->socialLogin->spotify_token);
            $this->assertEquals('example-refresh-token', $user->socialLogin->spotify_refresh_token);
        });
    }

    /** @test
     * @throws \ReflectionException
     */
    public function it_retrieves_spotify_request_and_login_existing_user()
    {
        $user = factory(User::class)->create();

        $this->mockSocialiteFacade($this->dummyUser([
            'id' => $user->id,
            'email' => $user->email,
        ]), SpotifyProvider::class);

        $this
            ->get('/login/spotify/callback')
            ->assertRedirect('/categories');
    }

    public static function socialLoginAndRedirectUrlProvider()
    {
        return [
            ['spotify', 'https://accounts.spotify.com/authorize'],
            ['facebook', 'https://www.facebook.com/v2.10/dialog/oauth'],
        ];
    }

    /**
     * Mock the Socialite Factory, so we can hijack the OAuth Request.
     *
     * @param array  $user
     * @param string $providerClass
     *
     * @throws \ReflectionException
     */
    private function mockSocialiteFacade(array $user, string $providerClass)
    {
        $socialiteUser = $this->createMock(\Laravel\Socialite\Two\User::class);
        $socialiteUser->id = $user['id'];
        $socialiteUser->token = $user['token'];
        $socialiteUser->email = $user['email'];
        $socialiteUser->name = $user['name'];
        $socialiteUser->refreshToken = $user['refreshToken'];

        $provider = $this->createMock($providerClass);
        $provider->expects($this->any())
            ->method('user')
            ->willReturn($socialiteUser);

        $stub = $this->createMock(Socialite::class);
        $stub->expects($this->any())
            ->method('driver')
            ->willReturn($provider);

        // Replace Socialite Instance with our mock
        $this->app->instance(Socialite::class, $stub);
    }

    private function dummyUser(array $overrides = [])
    {
        return array_merge([
            'id' => '1',
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'token' => 'fake-access-token',
            'refreshToken' => 'fake-refresh-token',
        ], $overrides);
    }
}

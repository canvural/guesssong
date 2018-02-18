<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_register_an_account()
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);
        $response->assertRedirect('/categories');
        $this->assertTrue(Auth::check());
        $this->assertCount(1, User::all());
        tap(User::first(), function ($user) {
            $this->assertEquals('John Doe', $user->name);
            $this->assertEquals('johndoe@example.com', $user->email);
            $this->assertTrue(Hash::check('secret', $user->password));
        });
    }

    /** @test */
    public function email_is_required()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));
        $response = $this->post(route('register'), $this->validParams([
            'email' => '',
        ]));
        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /** @test */
    public function email_is_valid()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));
        $response = $this->post(route('register'), $this->validParams([
            'email' => 'not-an-email-address',
        ]));
        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /** @test */
    public function email_cannot_exceed_255_chars()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));
        $response = $this->post(route('register'), $this->validParams([
            'email' => substr(str_repeat('a', 256).'@example.com', -256),
        ]));
        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /** @test */
    public function email_is_unique()
    {
        \factory(User::class)->create(['email' => 'johndoe@example.com']);

        $this->withExceptionHandling();
        $this->from(route('register'));
        $response = $this->post(route('register'), $this->validParams([
            'email' => 'johndoe@example.com',
        ]));
        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
        $this->assertCount(1, User::all());
    }

    /** @test */
    public function password_is_required()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));
        $response = $this->post(route('register'), $this->validParams([
            'password' => '',
        ]));
        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));
        $response = $this->post(route('register'), $this->validParams([
            'password' => 'foo',
            'password_confirmation' => 'bar',
        ]));
        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /** @test */
    public function password_must_be_6_chars()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));
        $response = $this->post(route('register'), $this->validParams([
            'password' => 'foo',
            'password_confirmation' => 'foo',
        ]));
        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ], $overrides);
    }
}

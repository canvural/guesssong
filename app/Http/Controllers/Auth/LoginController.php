<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\SocialiteScopes;
use App\SocialLogin;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/categories';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     * @param $provider
     *
     * @return RedirectResponse
     */
    public function redirectToProvider($provider): RedirectResponse
    {
        return \Socialite::driver($provider)->scopes(SocialiteScopes::$provider())->redirect();
    }

    /**
     * Obtain the user information from provider.
     *
     * @param $provider
     *
     * @return RedirectResponse
     */
    public function handleProviderCallback($provider): RedirectResponse
    {
        $user = \Socialite::driver($provider)->user();

        $authUser = $this->findOrCreateUser($user, $provider);

        \Auth::login($authUser, true);

        return redirect($this->redirectTo);
    }

    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     *
     * @param  $user Socialite user object
     * @param $provider string Social auth provider
     *
     * @return User
     */
    public function findOrCreateUser($user, $provider): User
    {
        $authUser = User::firstOrCreate(
            ['email' => $user->email],
            ['name' => $user->name]
        );

        $socialProfile = $authUser->socialLogin ?: new SocialLogin();

        $providerField = "{$provider}_id";
        $providerTokenField = "{$provider}_token";
        $providerRefreshTokenField = "{$provider}_refresh_token";

        $socialProfile->{$providerField} = $user->id;
        $socialProfile->{$providerTokenField} = $user->token;
        $socialProfile->{$providerRefreshTokenField} = $user->refreshToken ?? '';

        $authUser->socialLogin()->save($socialProfile);

        return $authUser;
    }
}

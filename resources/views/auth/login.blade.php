@extends('layouts.app')

@section('body')
    <div class="container mx-auto flex justify-center mt-8">
        <div class="w-full max-w-xs">
            <form method="POST" action="{{ route('login') }}" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-grey-darker text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mb-3 {{ $errors->has('email') ? 'border-red' : '' }}" id="email" name="email" type="email" placeholder="Email" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <p class="text-red text-xs italic">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="mb-6">
                    <label class="block text-grey-darker text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker mb-3 {{ $errors->has('password') ? 'border-red' : '' }}" id="password" name="password" type="password" placeholder="******">
                    @if ($errors->has('password'))
                        <p class="text-red text-xs italic">{{ $errors->first('password') }}</p>
                    @endif

                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                    </label>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4 rounded" type="submit">
                        Sign In
                    </button>
                    <a class="inline-block align-baseline font-bold text-sm text-blue hover:text-blue-darker" href="{{ route('password.request') }}">
                        Forgot Password?
                    </a>
                </div>

                <div class="border-t mt-6 text-center pt-4">
                    <a href="/login/spotify" class="no-underline bg-spotify text-white font-bold py-2 px-4 rounded inline-flex items-center">@svg('spotify') Log in with Spotify</a>
                </div>
            </form>
            <div class="text-center">
                <p class="text-grey-dark text-sm">Don't have an account? <a href="{{ route('register') }}" class="no-underline text-blue font-bold">Create an Account</a>.</p>
            </div>
        </div>
    </div>
@endsection

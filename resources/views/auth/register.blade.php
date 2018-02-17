@extends('layouts.app')

@section('body')
    <div class="container mx-auto flex items-center h-screen w-full bg-grey-lighter">
        <div class="w-full bg-white rounded shadow-lg p-8 m-4 md:max-w-sm md:mx-auto">
            <h1 class="block w-full text-center text-grey-darkest mb-6">Sign Up</h1>
            <form class="mb-4 md:flex md:flex-wrap md:justify-between" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="flex flex-col mb-4 md:w-full">
                    <label class="mb-2 uppercase tracking-wide font-bold text-lg text-grey-darkest" for="name">Name</label>
                    <input class="border py-2 px-3 text-grey-darkest {{ $errors->has('name') ? ' border-red' : '' }}" value="{{ old('name') }}" type="text" name="name" id="name" required autofocus>
                    @if ($errors->has('name'))
                        <p class="text-red text-xs italic">{{ $errors->first('name') }}</p>
                    @endif
                </div>
                <div class="flex flex-col mb-4 md:w-full">
                    <label class="mb-2 uppercase font-bold text-lg text-grey-darkest" for="email">Email</label>
                    <input class="border py-2 px-3 text-grey-darkest {{ $errors->has('email') ? 'border-red' : '' }}" value="{{ old('email') }}" type="email" name="email" id="email" required>
                    @if ($errors->has('email'))
                        <p class="text-red text-xs italic">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="flex flex-col mb-6 md:w-full">
                    <label class="mb-2 uppercase font-bold text-lg text-grey-darkest" for="password">Password</label>
                    <input class="border py-2 px-3 text-grey-darkest {{ $errors->has('password') ? 'border-red' : '' }}" type="password" name="password" id="password" required>
                    @if ($errors->has('password'))
                        <p class="text-red text-xs italic">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <div class="flex flex-col mb-6 md:w-full">
                    <label class="mb-2 uppercase font-bold text-lg text-grey-darkest" for="password-confirm">Confirm Password</label>
                    <input class="border py-2 px-3 text-grey-darkest" type="password" name="password_confirmation" id="password-confirm" required>
                </div>

                <button class="block bg-teal hover:bg-teal-dark text-white uppercase text-lg mx-auto p-4 rounded" type="submit">Create Account</button>

            </form>
            <a class="block w-full text-center no-underline text-sm text-grey-dark hover:text-grey-darker" href="{{ route('login') }}">Already have an account?</a>
        </div>
    </div>
@endsection

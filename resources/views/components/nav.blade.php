<nav class="font-sans flex flex-col text-center sm:flex-row sm:text-left sm:justify-between border-t-8 border-blue-light py-4 px-6 bg-white shadow sm:items-baseline w-full">
    <div class="mb-2 sm:mb-0">
        <a href="{{ route('categories.index') }}" class="text-lg no-underline text-grey-darkest hover:text-blue-dark mr-3">Categories</a>
        <a href="{{ route('scoreboard.index') }}" class="text-lg no-underline text-grey-darkest hover:text-blue-dark mr-3">Scoreboard</a>
        @auth
        @if (Auth::user()->hasSpotify())
            <a href="{{ route('userplaylists.index') }}" class="text-lg no-underline text-grey-darkest hover:text-blue-dark mr-3">
                My Playlists
            </a>
        @endif
        @endauth
    </div>
    <div>
        @auth
            <a href="{{ route('logout') }}" class="text-lg no-underline text-grey-darkest hover:text-blue-dark" onclick="event.preventDefault(); document.getElementById('form-logout').submit();">
                Logout
            </a>
            <form id="form-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @else
            <a href="{{ route('login') }}" class="text-lg no-underline text-grey-darkest hover:text-blue-dark">
                Login
            </a>
        @endauth
    </div>
</nav>
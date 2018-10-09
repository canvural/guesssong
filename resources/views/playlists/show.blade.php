@extends('layouts.app')

@section('body')
    <div class="mt-8 flex flex-wrap justify-center">
        @foreach($playlists as $playlist)
            <div class="max-w-sm rounded overflow-hidden shadow-lg lg:w-1/5 sm:w-full m-3">
                <a href="{{ route($route, ['playlistId' => $playlist->getId(), 'playlistSlug' => str_slug($playlist->getName())]) }}" class="no-underline text-black">
                    <img class="w-full" src="{{ $playlist->getImageUrl() }}" alt="{{ $playlist->getName() }}">
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2 text-center">{{ $playlist->getName() }}</div>
                        <div class="text-xs mb-2 text-center">
                            You played this playlist {{ array_get($playlistCounts, $playlist->getId(), 0) }} times
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection

@extends('layouts.app')

@section('body')
    <div class="mt-8 flex flex-wrap justify-center">
        @foreach($playlists as $playlist)
            <div class="max-w-sm rounded overflow-hidden shadow-lg lg:w-1/5 sm:w-full m-3">
                <a href="{{ route($route, ['playlistId' => $playlist['id'], 'playlistSlug' => str_slug($playlist['name']), 'u' => $playlist['owner']['id']]) }}" class="no-underline text-black">
                    <img class="w-full" src="{{ $playlist['images'][0]['url'] }}" alt="{{ $playlist['name'] }}">
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2 text-center">{{ $playlist['name'] }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection

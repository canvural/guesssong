@extends('layouts.app')

@section('body')
    <div class="mt-8 flex flex-wrap justify-center">
        @foreach($categories as $category)
            <div class="max-w-sm rounded overflow-hidden shadow-lg lg:w-1/5 sm:w-full m-3">
                <a href="{{ route('playlists.show', ['category' => $category['id']]) }}" class="no-underline text-black">
                    <img class="w-full" src="{{ $category['icons'][0]['url'] }}" alt="{{ $category['name'] }}">
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2 text-center">{{ $category['name'] }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection

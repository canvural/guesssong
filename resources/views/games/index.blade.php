@extends('layouts.app')

@section('body')
    <div class="mt-8 flex justify-center">
        <game
            playlist_image="{{ $playlistImage }}"
            playlist_id="{{ $playlistId }}"
        ></game>
    </div>
@endsection

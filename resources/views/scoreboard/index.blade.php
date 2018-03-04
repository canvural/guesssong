@extends('layouts.app')

@section('body')
    <div class="px-8 mt-8 flex justify-center">
        <table class="text-lg w-full max-w-full mb-4 bg-transparent border-collapse">
            <thead>
                <tr>
                    <th class="align-bottom border-b-2 border-grey-dark p-3">User name</th>
                    <th class="align-bottom border-b-2 border-grey-dark p-3">Games Played</th>
                    <th class="align-bottom border-b-2 border-grey-dark p-3">Total Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($games as $game)
                    <tr>
                        <td class="align-top border-t border-grey-dark p-3">{{ $game->user->name }}</td>
                        <td class="align-top border-t border-grey-dark p-3">{{ $game->gamesPlayed }}</td>
                        <td class="align-top border-t border-grey-dark p-3">{{ $game->totalScore }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
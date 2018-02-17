<?php

function get_fake_data(string $fileName): array
{
    return \json_decode(\file_get_contents(base_path("tests/data/{$fileName}")), TRUE);
}

function get_playlist(string $name)
{
    $playlists = get_fake_data('rock_playlists.json');
    
    return collect($playlists)->filter(function($playlist) use ($name) {
        return \str_slug($playlist['name']) === \str_slug($name);
    })->first();
}
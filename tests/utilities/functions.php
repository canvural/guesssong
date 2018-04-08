<?php

function get_fake_data(string $fileName): array
{
    return \json_decode(\file_get_contents(base_path("tests/data/{$fileName}")), true);
}

function get_playlist(string $name)
{
    $playlists = get_fake_data('rock_playlists.json');

    return collect($playlists)->first(function ($playlist) use ($name) {
        return \str_slug($playlist['name']) === \str_slug($name);
    });
}

function create($class, array $attributes = [], $times = null)
{
    return factory($class, $times)->create($attributes);
}

function make($class, array $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}

<?php

namespace App;

class SocialiteScopes
{
    public static function spotify(): array
    {
        return [
            'user-read-email',
            'playlist-read-private',
            'user-library-read',
            'playlist-read-collaborative',
        ];
    }

    public static function facebook(): array
    {
        return [
            'email',
            'read_custom_friendlists',
        ];
    }
}

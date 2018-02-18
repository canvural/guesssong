<?php

use Faker\Generator as Faker;

$factory->define(App\Game::class, function (Faker $faker) {
    return [
        'score' => $faker->randomNumber(),
        'playlist_id' => '37i9dQZF1DWWJOmJ7nRx0C',
        'user_id' => function () {
            return factory(\App\User::class)->create()->id;
        }
    ];
});

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware(['guest'])->group(function () {
    Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider');
    Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');
});

Route::group(['prefix' => 'me', 'middleware' => 'auth'], function () {
    Route::get('playlists', 'UserPlaylistController@index')->name('userplaylists.index');
});

Route::middleware(['auth', 'spotify.id'])->group(function () {
    Route::get('game/{playlistId}/{playlistSlug?}', 'GameController@create')->name('games.create');
    Route::get('me/game/{playlistId}/{playlistSlug?}', 'GameController@create')->name('usergames.create');

    Route::post('game/{playlistId}/{playlistSlug?}', 'GameController@store')->name('games.store');
    Route::post('me/game/{playlistId}/{playlistSlug?}', 'GameController@store')->name('usergames.store');

    Route::post('game/{playlistId}/{playlistSlug?}/answer', 'GameAnswerController@create')->name('gameAnswers.create');
    Route::post('me/game/{playlistId}/{playlistSlug?}/answer', 'GameAnswerController@create')->name('usergameAnswers.create');
});

Route::get('/categories', 'CategoryController@index')->name('categories.index');
Route::get('playlists/{category}', 'PlaylistController@index')->name('playlists.index');
Route::get('scoreboard', 'ScoreboardController@index')->name('scoreboard.index');

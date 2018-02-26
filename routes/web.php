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

Route::get('/', function () {
    dd(Socialite::driver('spotify'));
});

Route::middleware(['guest'])->group(function () {
    Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider');
    Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');
});

Route::group(['prefix' => 'me', 'middleware' => 'auth'], function () {
    Route::get('playlists', 'UserPlaylistController@index')->name('userplaylists.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('game/{playlistName}', 'GameController@index')->name('games.index');
    Route::get('me/game/{playlistName}', 'GameController@index')->name('usergames.index');

    Route::post('game/{playlistName}', 'GameController@store')->name('games.store');
    Route::post('me/game/{playlistName}', 'GameController@store')->name('usergames.store');

    Route::post('game/{playlistName}/answer', 'GameAnswerController@create')->name('gameAnswers.create');
    Route::post('me/game/{playlistName}/answer', 'GameAnswerController@create')->name('usergameAnswers.create');
});

Route::get('/categories', 'CategoryController@index')->name('categories.index');
Route::get('playlists/{category}', 'PlaylistController@index')->name('playlists.index');

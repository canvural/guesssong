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

Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/categories', 'CategoryController@index')->name('categories.index');

Route::get('playlists/{category}', 'PlaylistController@show')->name('playlists.show');

Route::middleware(['auth'])->group(function () {
    Route::get('game/{playlistName}', 'GameController@index')->name('games.index');
    Route::post('game/{playlistName}', 'GameController@store')->name('games.store');
    Route::post('game/{playlistName}/answer', 'GameAnswerController@create')->name('gameAnswers.create');
});

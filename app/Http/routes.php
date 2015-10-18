<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['middleware' => 'guest', function () {
    return view('home');
}]);

Route::get('/dashboard', ['middleware' => 'auth', function () {
	return view('dashboard');
}]);

Route::get('/movies', 'MovieController@index');
Route::post('/movies', 'MovieController@store');
Route::put('/movies/{movie}/vote', 'MovieController@vote');
Route::delete('/movies/{movie}', 'MovieController@destroy');

Route::get('/auth', 'Auth\AuthController@authenticate');
Route::get('/auth/callback', 'Auth\AuthController@handleGitHubCallback');
Route::get('/auth/logout', 'Auth\AuthController@logout');

<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/login',		'LoginController@get_login');
Route::post('/login',		'LoginController@post_login');
Route::get('/login/logout',	'LoginController@get_logout');

//protect routes below
Route::group(array('before'=>'avalon_auth', 'prefix'=>'login'), function()
{
	Route::resource('/objects', 'ObjectController');
	Route::resource('/users',	'UserController');
	
	Route::get('/objects/{id}/create',		'InstanceController@get_create');
});

Route::filter('avalon_auth', function()
{
    if (!Session::has('avalon_id')) return Redirect::to('/login', 303);
});

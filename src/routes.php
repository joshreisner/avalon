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

//temp

Route::get('/' . Config::get('avalon::prefix'),		'LoginController@get_login');
Route::post('/' . Config::get('avalon::prefix'),	'LoginController@post_login');

//protect routes below
Route::group(array('before'=>'avalon_auth', 'prefix'=>Config::get('avalon::prefix')), function()
{
	Route::get('logout',				'LoginController@logout');

	Route::resource('objects',			'ObjectController');
	Route::resource('users',			'UserController');
	
	Route::get('/objects/{id}/create',	'InstanceController@create');
	Route::post('/objects/{id}',		'InstanceController@store');
});

Route::filter('avalon_auth', function()
{
    if (!Session::has('avalon_id')) return Redirect::to('/' . Config::get('avalon::prefix'), 303);
});

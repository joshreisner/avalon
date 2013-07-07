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

//unprotected routes
Route::get('/' . Config::get('avalon::prefix'),		'LoginController@getIndex');
Route::post('/' . Config::get('avalon::prefix'),	'LoginController@postIndex');

//protected routes
Route::group(array('before'=>'avalon_auth', 'prefix'=>Config::get('avalon::prefix')), function()
{
	Route::get('logout',					'LoginController@getLogout');
	Route::resource('objects',				'ObjectController');
	Route::resource('objects.fields',		'FieldController');
	Route::resource('objects.instances',	'InstanceController');
	Route::resource('users',				'UserController');
});

Route::filter('avalon_auth', function()
{
    if (!Session::has('avalon_id')) return Redirect::action('LoginController@getIndex');
});

<?php

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
	Route::get('/users/{user_id}/activate',								'UserController@getActivate');
	Route::post('/objects/{object_id}/fields/reorder', 					'FieldController@postReorder');
	Route::post('/objects/{object_id}/instances/reorder',				'InstanceController@postReorder');
	Route::get('/objects/{object_id}/instances/{instance_id}/activate',	'InstanceController@getActivate');
});

//filters
Route::filter('avalon_auth', function()
{
    if (!Session::has('avalon_id')) return Redirect::action('LoginController@getIndex');
});
<?php

//unprotected routes
Route::get('/' . Config::get('avalon::prefix'),		'LoginController@getIndex');
Route::post('/' . Config::get('avalon::prefix'),	'LoginController@postIndex');

//protected routes
Route::group(array('before'=>'avalon_auth', 'prefix'=>Config::get('avalon::prefix')), function()
{
	Route::get('logout',					'LoginController@getLogout');

	Route::get('/settings', 'AccountController@edit');
	Route::post('/settings', 'AccountController@update');
	
	Route::resource('objects',				'ObjectController');
	Route::resource('objects.fields',		'FieldController');
	Route::resource('objects.instances',	'InstanceController');
	Route::resource('users',				'UserController');
	
	Route::get('/users/{user_id}/delete',								'UserController@delete');
	Route::post('/objects/{object_id}/fields/reorder', 					'FieldController@reorder');
	Route::post('/objects/{object_id}/instances/reorder',				'InstanceController@reorder');
	Route::get('/objects/{object_id}/instances/{instance_id}/delete',	'InstanceController@delete');

	//Route::any('/upload/file/to/s3', 'InstanceController@redactor_s3');

	Route::post('/objects/{object_id}/instances/{instance_id}/upload/image', 'InstanceController@upload_image');

});

//filters
Route::filter('avalon_auth', function()
{
	Session::flash('pre_login_url', URL::current());
    if (!Session::has('avalon_id')) return View::make('avalon::login.index'); //Redirect::action('LoginController@getIndex');
});
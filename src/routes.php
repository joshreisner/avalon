<?php

//unprotected routes
Route::get('/'  . Config::get('avalon::route_prefix'),				'LoginController@getIndex');
Route::post('/' . Config::get('avalon::route_prefix'), 				'LoginController@postIndex');
Route::get('/'  . Config::get('avalon::route_prefix') . '/reset',	'LoginController@getReset');
Route::post('/' . Config::get('avalon::route_prefix') . '/reset',	'LoginController@postReset');
Route::get('/'  . Config::get('avalon::route_prefix') . '/change/{email}/{token}', 'LoginController@getChange');
Route::post('/' . Config::get('avalon::route_prefix') . '/change',	'LoginController@postChange');

//protected routes
Route::group(array('before'=>'auth', 'prefix'=>Config::get('avalon::route_prefix')), function(){
	
	//all users
	Route::get('logout', 'LoginController@getLogout');
	Route::resource('objects.instances', 'InstanceController');
	Route::post('/objects/{object_id}/instances/reorder', 'InstanceController@reorder');
	Route::get('/objects/{object_id}/instances/{instance_id}/delete', 'InstanceController@delete');
	Route::resource('objects', 'ObjectController'); //need to unprotect only index

	//only admins
	Route::group(array('before'=>'admin'), function(){
		Route::resource('users', 'UserController');
		Route::get('/users/{user_id}/delete', 'UserController@delete');
	});

	//only programmers
	Route::group(array('before'=>'programmer'), function(){
		Route::resource('objects.fields', 'FieldController');
		Route::post('/objects/{object_id}/fields/reorder', 'FieldController@reorder');
	});

	//under construction: uploads
	//Route::any('/upload/file/to/s3', 'InstanceController@redactor_s3');
	//Route::post('/objects/{object_id}/instances/{instance_id}/upload/image', 'InstanceController@upload_image');
	Route::post('/upload/image', 'UploadController@image');

});

Route::filter('admin', function(){
	if (Auth::user()->role > 2) return Redirect::action('ObjectController@index');
});

Route::filter('programmer', function(){
	if (Auth::user()->role > 1) return Redirect::action('ObjectController@index');
});
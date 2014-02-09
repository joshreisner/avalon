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
	
	//all authenticated users
	Route::get('logout', 'LoginController@getLogout');
	Route::resource('objects.instances', 'InstanceController');
	Route::post('/objects/{object_id}/instances/reorder', 'InstanceController@reorder');
	Route::get('/objects/{object_id}/instances/{instance_id}/delete', 'InstanceController@delete');
	Route::get('/objects', 'ObjectController@index'); 
	Route::post('/upload/image', 'FileController@image');

	//only admins
	Route::group(array('before'=>'admin'), function(){
		Route::resource('users', 'UserController');
		Route::get('/users/{user_id}/delete', 'UserController@delete');
	});

	//only programmers
	Route::group(array('before'=>'programmer'), function(){

		//these would fit neatly in a resource controller except that index can be accessed by any user
		Route::get('/objects/create', 'ObjectController@create'); 
		Route::post('/objects', 'ObjectController@store'); 
		Route::get('/objects/{id}/edit', 'ObjectController@edit'); 
		Route::put('/objects/{id}', 'ObjectController@update'); 
		Route::delete('/objects/{id}', 'ObjectController@destroy'); 

		Route::resource('objects.fields', 'FieldController');
		Route::post('/objects/{object_id}/fields/reorder', 'FieldController@reorder');
	});

});

Route::filter('admin', function(){
	if (Auth::user()->role > 2) return Redirect::action('ObjectController@index');
});

Route::filter('programmer', function(){
	if (Auth::user()->role > 1) return Redirect::action('ObjectController@index');
});
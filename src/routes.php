<?php

Route::group(array('prefix'=>Config::get('avalon::route_prefix')), function(){

	if (Auth::guest() || empty(Auth::user()->role)) {
	
		# Unprotected login routes
		Route::get('/',							array('as'=>'home', 'uses'=>'LoginController@getIndex'));
		Route::post('/', 						'LoginController@postIndex');
		Route::get('/reset',					'LoginController@getReset');
		Route::post('/reset',					'LoginController@postReset');
		Route::get('/change/{email}/{token}',	'LoginController@getChange');
		Route::post('/change',					'LoginController@postChange');

	} else {

		# Admins only
		Route::group(array('before'=>'admin'), function(){
			Route::resource('users', 'UserController');
			Route::get('/users/{user_id}/delete', 'UserController@delete');
			Route::get('/users/{user_id}/resend-welcome', 'UserController@resendWelcome');
		});

		# Programmers only
		Route::group(array('before'=>'programmer'), function(){

			# Edit table
			Route::get('/create', 'ObjectController@create'); 
			Route::post('/', 'ObjectController@store'); 
			Route::get('/{object_name}/edit', 'ObjectController@edit'); 
			Route::put('/{object_name}', 'ObjectController@update'); 
			Route::delete('/{object_name}', 'ObjectController@destroy'); 

			# Edit fields
			Route::get('/{object_name}/fields', 'FieldController@index');
			Route::get('/{object_name}/fields/create', 'FieldController@create');
			Route::post('/{object_name}/fields', 'FieldController@store');
			Route::get('/{object_name}/fields/{field_id}/edit', 'FieldController@edit');
			Route::put('/{object_name}/fields/{field_id}', 'FieldController@update');
			Route::delete('/{object_name}/fields/{field_id}', 'FieldController@destroy');
			Route::post('/{object_name}/fields/reorder', 'FieldController@reorder');
		
			# Test routes
			Route::group(array('prefix'=>'test'), function(){
				Route::get('tables', 'ObjectController@tables');
			});

		});

		# All authenticated users
		Route::group(array('before'=>'user'), function(){
			Route::get('/', array('as'=>'home', 'uses'=>'ObjectController@index')); 
			Route::get('/logout', 'LoginController@getLogout');
			Route::post('/upload/image', 'FileController@image');

			# Todo delete
			Route::get('/image/test', 'FileController@test');
			
			# Complex instance routing, optionally with linked_id for related objects
			Route::get('/{object_name}', 'InstanceController@index');
			Route::get('/{object_name}/create/{linked_id?}', 'InstanceController@create');
			Route::post('/{object_name}/reorder', 'InstanceController@reorder');
			Route::post('/{object_name}/{linked_id?}', 'InstanceController@store');
			Route::get('/{object_name}/edit/{instance_id}/{linked_id?}', 'InstanceController@edit');
			Route::put('/{object_name}/{instance_id}/{linked_id?}', 'InstanceController@update');
			Route::delete('/{object_name}/{instance_id}', 'InstanceController@destroy');
			Route::get('/{object_name}/{instance_id}/delete', 'InstanceController@delete');
		});

	}
	
});

Route::filter('user', function(){
	if (empty(Auth::user()->role) || Auth::user()->role > 3) return Redirect::action('ObjectController@index');
});

Route::filter('admin', function(){
	if (empty(Auth::user()->role) || Auth::user()->role > 2) return Redirect::action('ObjectController@index');
});

Route::filter('programmer', function(){
	if (empty(Auth::user()->role) || Auth::user()->role > 1) return Redirect::action('ObjectController@index');
});
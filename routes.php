<?php

Route::any('(:bundle)', array('as'=>'login', 'uses'=>'avalon::login@form'));
Route::any('(:bundle)/logout', array('as'=>'logout', 'uses'=>'avalon::login@logout'));

Route::group(array('before' => 'auth'), function() {
	Route::any('(:bundle)/instances/(:num)/fields',		array('as'=>'fields',			'uses'=>'avalon::fields@list'));
	Route::any('(:bundle)/instances/(:num)/fields/add',	array('as'=>'fields_add',		'uses'=>'avalon::fields@add'));
	Route::any('(:bundle)/instances/(:num)',			array('as'=>'instances',		'uses'=>'avalon::instances@list'));
	Route::any('(:bundle)/instances/(:num)/add',		array('as'=>'instances_add',	'uses'=>'avalon::instances@add'));
	Route::any('(:bundle)/instances/(:num)/edit',		array('as'=>'instances_edit',	'uses'=>'avalon::fields@list'));
	Route::get('(:bundle)/objects',						array('as'=>'objects',			'uses'=>'avalon::objects@list'));
	Route::any('(:bundle)/objects/add', 				array('as'=>'objects_add',		'uses'=>'avalon::objects@add'));
	Route::any('(:bundle)/objects/(:num)/edit',			array('as'=>'objects_edit',		'uses'=>'avalon::objects@edit'));
	Route::any('(:bundle)/settings',					array('as'=>'settings',			'uses'=>'avalon::settings@form'));
	Route::get('(:bundle)/users',						array('as'=>'users',			'uses'=>'avalon::users@list'));
	Route::any('(:bundle)/users/add',					array('as'=>'users_add',		'uses'=>'avalon::users@add'));
	Route::any('(:bundle)/users/(:num)',				array('as'=>'users_edit',		'uses'=>'avalon::users@edit'));
});

Route::filter('auth', function() {
	if (Auth::guest()) return Redirect::to('login');
});
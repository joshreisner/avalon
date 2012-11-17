<?php

Route::any('(:bundle)', array('as'=>'login', 'uses'=>'avalon::login@form'));
Route::any('(:bundle)/logout', array('as'=>'logout', 'uses'=>'avalon::login@logout'));

Route::group(array('before' => 'auth'), function() {
	Asset::container('avalon')->add('avalon_page_css', 'css/page.css');
	Asset::container('avalon')->add('avalon_page_js', 'js/page.js');
	Route::get('(:bundle)/objects',			array('as'=>'objects',		'uses'=>'avalon::objects@list'));
	Route::get('(:bundle)/objects/add', 	array('as'=>'objects_add',	'uses'=>'avalon::objects@add'));
	Route::get('(:bundle)/settings',		array('as'=>'settings',		'uses'=>'avalon::settings@form'));
	Route::get('(:bundle)/users',			array('as'=>'users',		'uses'=>'avalon::users@list'));
	Route::any('(:bundle)/users/add',		array('as'=>'users_add',	'uses'=>'avalon::users@add'));
	Route::any('(:bundle)/users/(:num)',	array('as'=>'users_edit',	'uses'=>'avalon::users@edit'));
});

Route::filter('auth', function() {
	if (Auth::guest()) {
		return Redirect::to('login');
	}
});
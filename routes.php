<?php

Route::any('(:bundle)', array('as'=>'login', 'uses'=>'avalon::login@form'));
Route::any('(:bundle)/logout', array('as'=>'logout', 'uses'=>'avalon::login@logout'));

Route::group(array('before' => 'auth'), function() {
	Asset::container('avalon')->add('avalon_page_css', 'css/page.css');
	Route::get('(:bundle)/objects',		array('as'=>'objects',		'uses'=>'avalon::objects@list'));
	Route::get('(:bundle)/objects/new', array('as'=>'objects_new',	'uses'=>'avalon::objects@new'));
	Route::get('(:bundle)/settings',	array('as'=>'settings',		'uses'=>'avalon::settings@form'));
	Route::get('(:bundle)/users',		array('as'=>'users',		'uses'=>'avalon::users@list'));
});

Route::filter('auth', function() {
	if (Auth::guest()) {
		return Redirect::to('login');
	}
});
<?php

Route::any('(:bundle)', array('as'=>'login', 'uses'=>'avalon::login@form'));

Route::group(array('before' => 'auth'), function() {
	Route::get('(:bundle)/objects',		array('as'=>'objects', 'uses'=>'avalon::objects@list'));
	Route::get('(:bundle)/objects/new', 'avalon::objects@new');
	Route::get('(:bundle)/settings',	'avalon::settings@form');
	Route::get('(:bundle)/users',		'avalon::users@list');
});

Route::filter('auth', function() {
	if (Auth::guest()) {
		die('not logged in');
		return Redirect::to('login');
	}
});
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
	Route::post('/objects/{object_id}/fields/reorder', 					'FieldController@postReorder');
	Route::post('/objects/{object_id}/instances/reorder',				'InstanceController@postReorder');
	Route::get('/objects/{object_id}/instances/{instance_id}/activate',	'InstanceController@getActivate');
});

Route::filter('avalon_auth', function()
{
    if (!Session::has('avalon_id')) return Redirect::action('LoginController@getIndex');
});

//todo move this to an appropriate place
class Breadcrumbs {

	public static function leave($breadcrumbs) {
		$return = array();
		
		//prepend home
		$breadcrumbs = array_merge(array('/'=>'<i class="icon-home"></i>'), $breadcrumbs);
		
		//build breadcrumbs
		foreach ($breadcrumbs as $link=>$text) {
			$return[] = (is_string($link)) ? '<a href="' . $link . '">' . $text . '</a>' : $text;
		}
		
		return '<h1>' . implode(Config::get('avalon::breadcrumbs_separator'), $return) . '</h1>';
	}
	
}
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

Route::get('/login',					'LoginController@get_login');
Route::post('/login',					'LoginController@post_login');
Route::get('/login/logout',				'LoginController@get_logout');

//todo protect routes below
Route::get('/login/objects',			'ObjectController@get_index');
Route::get('/login/objects/create',		'ObjectController@get_create');
Route::post('/login/objects/create',	'ObjectController@post_store');

Route::get('/login/objects/{id}',		'InstanceController@get_index');
Route::get('/login/objects/{id}/create','InstanceController@get_create');

<?php

class LoginController extends \BaseController {

	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| Handle logins to Avalon.  Avoids the built-in Laravel Auth model so as not to 
	| conflict with any potential applications.
	|
	*/

	//show login page if not logged in
	public function getIndex()
	{
		//show install form
		if (!DB::table('avalon')->count()) return View::make('avalon::login.install');

		//already logged in
		if (Session::has('avalon_id')) return Redirect::action('ObjectController@index');
		
		//not logged in
		return View::make('avalon::login.index');
	}

	//handle a post to the login or install form
	public function postIndex()
	{
		//regular login
		if (DB::table('avalon')->count()) {
			//attempt auth
			if ($user = DB::table('avalon_users')->where('email', Input::get('email'))->select('id', 'password')->first()) {
				if (Hash::check(Input::get('password'), $user->password)) {
					//log in with supplied credentials
					Session::put('avalon_id', $user->id);
					DB::table('avalon_users')->where('id', $user->id)->update(array('last_login' => new DateTime));
					return Redirect::action('ObjectController@index');
				}
			}
			return Redirect::action('LoginController@getIndex');
		} 
		
		//make user
		$user_id = DB::table('avalon_users')->insertGetId(array(
			'firstname'		=> Input::get('firstname'),
			'lastname'		=> Input::get('lastname'),
			'email'			=> Input::get('email'),
			'password'		=> Hash::make(Input::get('password')),
			'role'			=> 1,
			'last_login'	=> new DateTime,
			'updated_at'	=> new DateTime,
		));
		
		//show that user created self
		DB::table('avalon_users')->where('id', $user_id)->update(array('updated_by'=>$user_id));
		
		//make avalon row
		DB::table('avalon')->insert(array(
			'updated_at'	=> new DateTime,
			'updated_by'	=> $user_id,
		));
		
		Session::put('avalon_id', $user_id);
		
		return Redirect::action('ObjectController@index');

	}
	
	public function getLogout()
	{
		Session::forget('avalon_id');
		return Redirect::action('LoginController@getIndex');
	}

}
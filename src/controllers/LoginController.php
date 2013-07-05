<?php

class LoginController extends \BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function get_login()
	{
		if (DB::table('avalon')->count()) {
			if (!empty($_SESSION['avalon_id'])) {
				//is logged in
				return Redirect::to('/login/objects', 303);
			} elseif ($token = Cookie::get('avalon_token')) {
				//has logged in
				if ($user_id = DB::table('avalon_users')->where('token', $token)->pluck('id')) {
					//log in with cookie
					$_SESSION['avalon_id'] = $user_id;
					DB::table('avalon_users')->where('id', $user_id)->update(array('last_login' => new DateTime));
				} else {
					//unset bad cookie
					Cookie::forget('avalon_token');
				}
				return Redirect::to('/login', 303);
			} else {
				//not logged in
				return View::make('avalon::login.index');
			}
		} else {
			//install system
			return View::make('avalon::login.install');			
		}
	}

	public function post_login()
	{
		if (DB::table('avalon')->count()) {
			//attempt auth
			if ($user = DB::table('avalon_users')->where('email', Input::get('email'))->select('id', 'password')->first()) {
				if (Hash::check(Input::get('password'), $user->password)) {
					//log in with supplied credentials
					$_SESSION['avalon_id'] = $user->id;
					DB::table('avalon_users')->where('id', $user->id)->update(array('last_login' => new DateTime));
				}
			}
			return Redirect::to('/login', 303);
		} else {
		
			$token = Str::random();
			
			//make user
			$user_id = DB::table('avalon_users')->insertGetId(array(
				'firstname'		=> Input::get('firstname'),
				'lastname'		=> Input::get('lastname'),
				'email'			=> Input::get('email'),
				'password'		=> Hash::make(Input::get('password')),
				'token'			=> $token,
				'role'			=> 1,
				'last_login'	=> new DateTime,
				'updated_at'	=> new DateTime,
				'updated_by'	=> 1,
			));
			
			//make avalon row
			DB::table('avalon')->insert(array(
				'updated_at'	=> new DateTime,
				'updated_by'	=> 1,
			));
			
			$_SESSION['avalon_id'] = $user_id;
			
			return Redirect::to('/login', 303)->withCookie(Cookie::forever('avalon_token', $token));

		}
	}
	
	public function get_logout()
	{
		if (isset($_SESSION['avalon_id'])) unset($_SESSION['avalon_id']);
		Cookie::forget('avalon_token');
		return Redirect::to('/login', 303);
	}

}
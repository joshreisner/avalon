<?php

class LoginController extends \BaseController {

	//show login page if not logged in
	public function getIndex() {
		//show install form
		if (!DB::table('avalon')->count()) return View::make('avalon::login.install');

		//already logged in
		if (Session::has('avalon_id')) return Redirect::action('ObjectController@index');
		
		//not logged in
		return View::make('avalon::login.index');
	}

	//handle a post to the login or install form
	public function postIndex() {
		//regular login
		if (DB::table('avalon')->count()) {
			//attempt auth
			if ($user = DB::table('avalon_users')->where('active', 1)->where('email', Input::get('email'))->select('id', 'password')->first()) {
				if (Hash::check(Input::get('password'), $user->password)) {
					//log in with supplied credentials
					Session::put('avalon_id', $user->id);
					DB::table('avalon_users')->where('id', $user->id)->update(array(
						'last_login'=>new DateTime
					));

					//redirect::intended does not seem to be working
					if (Session::has('pre_login_url')) return Redirect::to(Session::get('pre_login_url'));
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
			'updated'		=> new DateTime,
		));
		
		//show that user created self
		DB::table('avalon_users')->where('id', $user_id)->update(array('updater'=>$user_id));
		
		//make avalon row
		DB::table('avalon')->insert(array(
			'updated'	=> new DateTime,
			'updater'	=> $user_id,
		));
		
		Session::put('avalon_id', $user_id);
		
		return Redirect::action('ObjectController@index');

	}
	
	//logout
	public function getLogout() {
		Session::forget('avalon_id');
		return Redirect::action('LoginController@getIndex');
	}

}
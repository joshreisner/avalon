<?php

class LoginController extends \BaseController {

	//show login page if not logged in
	public function getIndex() {
		//show install form
		if (!DB::table(DB_USERS)->count()) return View::make('avalon::login.install');

		return View::make('avalon::login.index');
	}

	//handle a post to the login or install form
	public function postIndex() {
		//regular login
		if (DB::table(DB_USERS)->count()) {
			//attempt auth
			if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')), true)) {

				DB::table(DB_USERS)->where('id', Auth::user()->id)->update(array(
					'last_login'=>new DateTime
				));

				return Redirect::intended(URL::route('home'));
			}
			return Redirect::route('home')->with('error', trans('avalon::messages.site_login_invalid'));
		} 
		
		//make user
		$user_id = DB::table(DB_USERS)->insertGetId(array(
			'name'			=> Input::get('name'),
			'email'			=> Input::get('email'),
			'password'		=> Hash::make(Input::get('password')),
			'role'			=> 1,
			'last_login'	=> new DateTime,
			'created_at'	=> new DateTime,
			'updated_at'	=> new DateTime,
		));
		
		//show that user created self
		DB::table(DB_USERS)->where('id', $user_id)->update(array('updated_by'=>$user_id));
		
		Auth::loginUsingId($user_id);
		
		return Redirect::route('home');
	}
	
	//logout
	public function getLogout() {
		Auth::logout();
		return Redirect::route('home');
	}

	//reset password form
	public function getReset() {
		return View::make('avalon::login.reset');
	}

	//send reset email
	public function postReset() {

		//get user
		if (!$user = DB::table(DB_USERS)->where('role', '<', 4)->whereNull('deleted_at')->where('email', Input::get('email'))->first()) {
			return Redirect::action('LoginController@getReset')->with(array(
				'error'=>trans('avalon::messages.users_password_reset_error')
			));
		}

		//set new token every time
		$token = Str::random();
		DB::table(DB_USERS)->where('id', $user->id)->update(array('token'=>$token));

		//reset link
		$link = URL::action('LoginController@getChange', array('token'=>$token, 'email'=>$user->email));

		//send reminder email
		Mail::send('avalon::emails.password', array('link'=>$link), function($message) use ($user)
		{
			$message->to($user->email)->subject(trans('avalon::messages.users_password_reset'));
		});

		return Redirect::action('LoginController@getReset')->with(array('message'=>trans('avalon::messages.users_password_reset_sent')));
	}

	//reset password form
	public function getChange($email, $token) {
		//todo check email / token combo
		if (!$user = DB::table(DB_USERS)->whereNull('deleted_at')->where('email', $email)->where('token', $token)->first()) {
			return Redirect::action('LoginController@getReset')->with(array(
				'error'=>trans('avalon::messages.users_password_change_error')
			));
		}

		return View::make('avalon::login.change', array(
			'email'=>$email,
			'token'=>$token,
		));
	}

	//send reset email
	public function postChange() {
		if (!$user = DB::table(DB_USERS)->whereNull('deleted_at')->where('email', Input::get('email'))->where('token', Input::get('token'))->first()) {
			return Redirect::action('LoginController@getReset')->with(array(
				'error'=>trans('avalon::messages.users_password_change_error')
			));
		}

		//successfully used reset token, time for it to die
		DB::table(DB_USERS)->where('id', $user->id)->update(array(
			'token'=>null,
			'password'=>Hash::make(Input::get('password')),
			'last_login'=>new DateTime,
		));

		//log you in
		Session::put('avalon_id', $user->id);
		return Redirect::route('home');
	}

}
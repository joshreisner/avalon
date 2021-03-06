<?php

class UserController extends BaseController {

	private static $roles = array(
		1=>'Programmer',
		2=>'Admin',
		3=>'User'
	);
	
	//show a list of users
	public function index() {
		$users = DB::table(DB_USERS)->whereIn('role', array_keys(self::$roles))->orderBy('name')->get();
		
		foreach ($users as &$user) {
			$user->role = self::$roles[$user->role];
			$user->link = URL::action('UserController@edit', $user->id);
			$user->delete = URL::action('UserController@delete', $user->id);
		}

		return View::make('avalon::users.index', array(
			'users'=>$users,
		));
	}
	
	//show the create new user form
	public function create() {
		$objects = DB::table(DB_OBJECTS)->get();
		return View::make('avalon::users.create', array(
			'roles'=>self::$roles,
			'objects'=>$objects,
		));
	}
	
	//save new user, email them autogenerated password
	public function store() {
		$email = Input::get('email');
		$password = Str::random(12);

		$user_id = DB::table(DB_USERS)->insertGetId(array(
			'name'=>Input::get('name'),
			'email'=>$email,
			'password'=>Hash::make($password),
			'role'=>Input::get('role'),
			'updated_by'=>Auth::user()->id,
			'updated_at'=>new DateTime,
		));

		self::sendWelcome($email, $password);		
		
		return Redirect::action('UserController@index')->with('user_id', $user_id);
	}

	//show edit screen
	public function edit($user_id) {
		$objects = DB::table(DB_OBJECTS)->get();
		$user = DB::table(DB_USERS)->where('id', $user_id)->first();
		return View::make('avalon::users.edit', array(
			'user'=>$user,
			'roles'=>self::$roles,
			'objects'=>$objects,
		));
	}

	//save edit to database
	public function update($user_id) {
		DB::table(DB_USERS)->where('id', $user_id)->update(array(
			'name'=>Input::get('name'),
			'email'=>Input::get('email'),
			'role'=>Input::get('role'),
			'updated_by'=>Auth::user()->id,
			'updated_at'=>new DateTime,
		));
		return Redirect::action('UserController@index')->with('user_id', $user_id);
	}

	//toggle active flag
	public function delete($user_id) {
		$deleted_at = (Input::get('active') == 1) ? null : new DateTime;
		DB::table(DB_USERS)->where('id', $user_id)->update(array(
			'updated_by'=>Auth::user()->id,
			'updated_at'=>new DateTime,
			'deleted_at'=>$deleted_at,
		));
		$updated = DB::table(DB_USERS)->where('id', $user_id)->pluck('updated_at');
		return Dates::relative($updated);
	}

	//destroy a never-logged-in user
	public function destroy($user_id) {
		DB::table(DB_USERS)->whereNull('last_login')->where('id', $user_id)->delete();
		return Redirect::action('UserController@index');
	}

	/**
	 * Re-send welcome email
	 * Have to reset user's hashed password as well
	 * Not sure this is a great idea
	 */
	public function resendWelcome($user_id) {
		$password = Str::random(12);
		$email = DB::table(DB_USERS)->where('id', $user_id)->pluck('email');

		DB::table(DB_USERS)->where('id', $user_id)->update(array(
			'password'=>Hash::make($password),
		));

		self::sendWelcome($email, $password);		
		
		return Redirect::action('UserController@index')->with('user_id', $user_id);
	}

	/**
	 * Send a welcome email to a user
	 */
	private function sendWelcome($email, $password) {
		//send notification email
		return Mail::send('avalon::emails.welcome', array(
			'email'=>$email,
			'password'=>$password,
			'link'=>URL::route('home'),
			), function($message) use ($email) 
		{
			$message->to($email)->subject(trans('avalon::messages.users_welcome_subject'));
		});		
	}
}
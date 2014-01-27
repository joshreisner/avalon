<?php

class UserController extends \BaseController {

	private static $roles = array(
		1=>'Programmer',
		2=>'Admin',
		3=>'User'
	);
	
	//show a list of users
	public function index() {
		$users = DB::table('avalon_users')->orderBy('lastname')->get();
		
		foreach ($users as &$user) {
			$user->name = $user->firstname . ' ' . $user->lastname;
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
		$objects = DB::table('avalon_objects')->get();
		return View::make('avalon::users.create', array(
			'roles'=>self::$roles,
			'objects'=>$objects,
		));
	}
	
	//save new user, email them autogenerated password
	public function store() {
		$password = Str::random();
		DB::table('avalon_users')->insert(array(
			'firstname'=>Input::get('firstname'),
			'lastname'=>Input::get('lastname'),
			'email'=>Input::get('email'),
			'password'=>Hash::make($password),
			'role'=>Input::get('role'),
			'updated_by'=>Session::get('avalon_id'),
			'updated_at'=>new DateTime,
		));
		
		//todo email notification
		
		return Redirect::action('UserController@index');
	}

	//show edit screen
	public function edit($user_id) {
		$objects = DB::table('avalon_objects')->get();
		$user = DB::table('avalon_users')->where('id', $user_id)->first();
		return View::make('avalon::users.edit', array(
			'user'=>$user,
			'roles'=>self::$roles,
			'objects'=>$objects,
		));
	}

	//save edit to database
	public function update($user_id) {
		DB::table('avalon_users')->where('id', $user_id)->update(array(
			'firstname'=>Input::get('firstname'),
			'lastname'=>Input::get('lastname'),
			'email'=>Input::get('email'),
			'role'=>Input::get('role'),
			'updated_by'=>Session::get('avalon_id'),
			'updated_at'=>new DateTime,
		));
		return Redirect::action('UserController@index');
	}

	//toggle active flag
	public function delete($user_id) {
		$deleted_at = (Input::get('active') == 1) ? null : new DateTime;
		DB::table('avalon_users')->where('id', $user_id)->update(array(
			'updated_by'=>Session::get('avalon_id'),
			'updated_at'=>new DateTime,
			'deleted_at'=>$deleted_at,
		));
		$updated = DB::table('avalon_users')->where('id', $user_id)->pluck('updated_at');
		return Dates::relative($updated);
	}
}
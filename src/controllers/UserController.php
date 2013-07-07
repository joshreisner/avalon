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
			$user->role = self::$roles[$user->role];
			if (!empty($user->last_login)) $user->last_login = \Carbon\Carbon::createFromTimeStamp(strtotime($user->last_login))->diffForHumans();
		}
		
		return View::make('avalon::users.index', array(
			'users'=>$users
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
	
	public function edit($user_id) {
		return 'edit screen for user ' . $user_id;
	}

}
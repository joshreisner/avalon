<?php

class UserController extends \BaseController {

	private static $roles = array(
		1=>'Programmer',
		2=>'Admin',
		3=>'User'
	);
	
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
	
	public function create() {
		$objects = DB::table('avalon_objects')->get();
		return View::make('avalon::users.create', array(
			'roles'=>self::$roles,
			'objects'=>$objects,
		));
	}
	
	public function edit($user_id) {
		return 'edit screen for user ' . $user_id;
	}

}
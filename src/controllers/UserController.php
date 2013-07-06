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

}
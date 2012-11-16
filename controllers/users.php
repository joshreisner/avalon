<?php
class Avalon_Users_Controller extends Controller {
	
	public $restful = true;
	
	public function get_list() {
		$users = \Avalon\User::where('active', '=', 1)->get(array('firstname', 'lastname', 'role'));
		return View::make('avalon::users')->with('users', $users);
	}

}
<?php
class Avalon_Users_Controller extends Controller {
	
	public $restful = true;
	
	private $roles = array(1=>'Programmer', 2=>'Admin', 3=>'User');
	
	public function get_list() {
		$users = \Avalon\User::where('active', '=', 1)->order_by('lastname')->get(array('id', 'firstname', 'lastname', 'role', 'last_login'));
		
		//set roles to their verbal description
		foreach ($users as $u) $u->role = $this->roles[$u->role];
		
		return View::make('avalon::users')->with('users', $users);
	}
	
	public function get_edit($id) {
		$user = \Avalon\User::find($id);
		return View::make('avalon::users_edit')->with('user', $user)->with('roles', $this->roles);
	}

	public function put_edit($id) {
		$user = \Avalon\User::find($id);
		$user->firstname = Input::get('firstname');
		$user->lastname = Input::get('lastname');
		$user->email = Input::get('email');
		$user->role = Input::get('role');
		$user->save();
		return Redirect::to_route('users');
	}

	public function get_add() {
		return 'hi';
	}
	
	public function post_add() {
		
	}

}
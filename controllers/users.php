<?php
class Avalon_Users_Controller extends Controller {
	
	public $restful = true;
	
	private $roles = array(1=>'Programmer', 2=>'Admin', 3=>'User');

	public function get_add() {
		$objects = \Avalon\Object::where('active', '=', 1)->order_by('title')->get(array('id', 'title'));
		return View::make('Avalon::users.add', array(
			'roles'=>$this->roles,
			'objects'=>$objects,
			'title'=>'Add User'
		));
	}
	
	public function get_edit($id) {
		$user = \Avalon\User::find($id);
		
		$permissions = array();
		foreach ($user->objects as $object) $permissions[] = $object->id;
		
		$objects = \Avalon\Object::where('active', '=', 1)->order_by('title')->get(array('id', 'title'));

		return View::make('Avalon::users.edit', array(
			'user'=>$user,
			'roles'=>$this->roles,
			'objects'=>$objects,
			'permissions'=>$permissions,
			'title'=>'Edit User'
		));
	}

	public function get_list() {
		$users = \Avalon\User::where('active', '=', 1)->order_by('lastname')->order_by('firstname')->get(array('id', 'firstname', 'lastname', 'role', 'last_login'));
		
		$user = Auth::user();
		
		//set roles to their verbal description
		foreach ($users as $u) {
			$u->role = $this->roles[$u->role];
			$u->link = URL::to_route('users_edit', $u->id);
			if (!empty($u->last_login)) $u->last_login = \Avalon\Date::format($u->last_login);
		}
		
		return View::make('Avalon::users.list', array(
			'user'=>$user,
			'users'=>$users,
			'title'=>'Users'
		));
	}
	
	public function delete_edit($id) {
		$user = \Avalon\User::find($id);
		$user->active = 0;
		$user->save();
		return Redirect::to_route('users');
	}

	public function put_edit($id) {
		$user = \Avalon\User::find($id);
		$user->firstname = Input::get('firstname');
		$user->lastname = Input::get('lastname');
		$user->email = Input::get('email');
		$user->role = Input::get('role');

		//perhaps there's an easier way of saving checkbox values?
		$permissions = array();
		$objects = \Avalon\Object::where('active', '=', 1)->order_by('title')->get(array('id'));
		foreach ($objects as $object) {
			if (Input::get('permissions_' . $object->id) == 'on') $permissions[] = $object->id;
		}
		$user->objects()->sync($permissions);
		
		$user->save();
		return Redirect::to_route('users');
	}
	
	public function post_add() {
		$user = new \Avalon\User;
		$user->firstname = Input::get('firstname');
		$user->lastname = Input::get('lastname');
		$user->email = Input::get('email');
		$user->role = Input::get('role');
		$user->active = 1;
		$user->save();
		return Redirect::to_route('users');
	}

}
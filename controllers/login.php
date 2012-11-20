<?php
class Avalon_Login_Controller extends Controller {

	public $restful = true;

	public function get_form() {
		if (!Auth::guest()) return Redirect::to_route('objects');
		Asset::container('avalon')->add('avalon_login_css', 'css/login.css');
		return View::make('avalon::login')->with('count', \Avalon\User::count());
	}
	
	public function post_form() {
		if (\Avalon\User::count() == 0) {
			//this is the very first user
			$user = new \Avalon\User;
			$user->email      = Input::get('email');
			$user->password   = Hash::make(Input::get('password'));
			$user->firstname  = Input::get('firstname');
			$user->lastname   = Input::get('lastname');
			$user->role       = 1;
			$user->active     = 1;
			$user->last_login = date('Y-m-d H:i:s');
			$user->save();
			
			Auth::login($user->id, true);
			return Redirect::to_route('objects');

        } else {
            //check login credentials
            if ($user = \Avalon\User::where('email', '=', Input::get('email'))->first()) {
                if (Hash::check(Input::get('password'), $user->password)) {
					Auth::login($user->id);
					$user->last_login = date('Y-m-d H:i:s');
					\Avalon\User::$timestamps = false;
					$user->save();
                }
            }

            return Redirect::to_route('objects');
        }
	}
	
	public function get_logout() {
		Auth::logout();
		return Redirect::to_route('login');
	}
}
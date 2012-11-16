<?php
class Avalon_Login_Controller extends Controller {

	public $restful = true;

	public function get_form() {
		Asset::container('avalon')->add('avalon_login_css', 'css/login.css');
		if (0 == \Avalon\User::count()) {
            //this is the very first user
            return View::make('avalon::install');
        } else {
        	if (Auth::check()) return Redirect::to_route('objects');
            return View::make('avalon::login');
        }
	}
	
	public function post_form() {
        if (0 == \Avalon\User::count()) {
            //this is the very first user
            $user = new \Avalon\Avalon_User;
            $user->email      = Input::get('email');
            $user->password   = Hash::make(Input::get('password'));
            $user->firstname  = Input::get('firstname');
            $user->lastname   = Input::get('lastname');
            $user->role       = 1;
            $user->active     = 1;
            //$user->last_login = 
            $user->save();

            Auth::login($user->id);
            return Redirect::to_route('objects');

        } else {
            //check login credentials
            if ($user = \Avalon\User::where('email', '=', Input::get('email'))->first()) {
                if (Hash::check(Input::get('password'), $user->password)) {
                    Auth::login($user->id);
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
<?php
class Avalon_Login_Controller extends Controller {

	public $restful = true;

	public function get_form() {
        if (0 == \Avalon\User::count()) {
            //this is the very first user
            return View::make('avalon::install');
        } else {
            return View::make('avalon::login');
        }
	}
	
	public function post_form() {
		die('...and that\'s as far as i got.');
	}
	
}
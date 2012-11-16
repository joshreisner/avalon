<?php
class Avalon_Objects_Controller extends Controller {
	
	public $restful = true;
	
	public function get_list() {
		$user = Auth::user();
		$objects = array();
		return View::make('avalon::objects')->with('user', $user)->with('objects', $objects);
	}

	public function get_new() {
		echo 'hi';
	}
}
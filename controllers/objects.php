<?php
class Avalon_Objects_Controller extends Controller {
	
	public $restful = true;
	
	public function get_list() {
		Asset::container('avalon')->add('avalon_page_css', 'css/page.css');
		$user = Auth::user();
		$objects = array();
		return View::make('avalon::objects')->with('user', $user)->with('objects', $objects);
	}

}
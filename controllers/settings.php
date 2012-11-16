<?php
class Avalon_Settings_Controller extends Controller {
	
	public $restful = true;
	
	public function get_form() {
		return View::make('avalon::settings');
	}

}
<?php
class Avalon_Settings_Controller extends Controller {
	
	public $restful = true;
	
	public $languages = array('es'=>'Español', 'fr'=>'Français', 'it'=>'Italiano', 'pt'=>'Portuguès', 'ru'=>'Русский', 'uk'=>'Українська');
	
	public function get_form() {
		$settings = \Avalon\Settings::find(1);
		return View::make('avalon::settings.edit', array(
			'settings'=>$settings,
			'languages'=>$this->languages,
			'title'=>'Site Settings',
			'link_color'=>$settings->link_color,
		));
	}
	
	public function put_form() {
		$settings = \Avalon\Settings::find(1);
		$settings->link_color = Input::get('link_color');
		$settings->save();
		return Redirect::to_route('objects');
	}

}
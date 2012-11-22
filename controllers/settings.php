<?php
class Avalon_Settings_Controller extends Controller {
	
	public $restful = true;
	
	public $languages = array('es'=>'Español', 'fr'=>'Français', 'it'=>'Italiano', 'pt'=>'Portuguès', 'ru'=>'Русский', 'uk'=>'Українська');
	
	public function get_form() {
		if (0 == \Avalon\Settings::count()) {
			$settings = new \Avalon\Settings;
			$settings->link_color = '#0088CC';
			$settings->save();
		} else {
			$settings = \Avalon\Settings::find(1);
		}
		return View::make('avalon::settings.edit', array(
			'settings'=>$settings,
			'languages'=>$this->languages,
			'title'=>'Site Settings'
		));
	}
	
	public function put_form() {
		$settings = \Avalon\Settings::find(1);
		$settings->link_color = Input::get('link_color');
		$settings->save();
		return Redirect::to_route('objects');
	}

}
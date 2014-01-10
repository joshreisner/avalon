<?php

class AccountController extends \BaseController {

	public function edit() {
		return View::make('avalon::accounts.edit', array(
			'account'=>DB::table('avalon')->where('id', 1)->first()
		));
	}

	public function update() {
		DB::table('avalon')->where('id', 1)->update(array(
			'title'=>Input::get('title'),
			'image'=>Input::get('image'),
			'color'=>Input::get('color'),
			'updated'=>new DateTime,
			'updater'=>Session::get('avalon_id'),
		));

		return Redirect::action('ObjectController@index');
	}

}
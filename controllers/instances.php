<?php
class Avalon_Instances_Controller extends Controller {
	
	public $restful = true;
	
	public function get_list($id) {
		$object = \Avalon\Object::find($id);
		return View::make('avalon::instances.list')->with('object', $object);
	}
	
}
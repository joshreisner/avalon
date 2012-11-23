<?php
class Avalon_Instances_Controller extends Controller {
	
	public $restful = true;
	
	public function get_add($id) {
		return View::make('avalon::instances.add', array(
			'object'=>\Avalon\Object::find($id),
			'title'=>'Add New'
		));
	}

	public function get_list($id) {
		$object = \Avalon\Object::find($id);
		return View::make('avalon::instances.list', array(
			'object'=>$object,
			'title'=>$object->title
		));
	}

	public function post_add($id) {
		return 'not implemented yet';
	}
	
}
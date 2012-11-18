<?php
class Avalon_Fields_Controller extends Controller {
	
	public $restful = true;
	
	public function get_add($id) {
		$object = \Avalon\Object::find($id);
		return View::make('avalon::fields.add')->with('object', $object);
	}
	
	public function get_list($id) {
		$object = \Avalon\Object::find($id);
		$fields = \Avalon\Field::where('object_id', '=', $object->id);
		return View::make('avalon::fields.list')->with('object', $object)->with('fields', $fields);
	}
	
	public function post_add($id) {
		$object = \Avalon\Object::find($id);
		$field = new \Avalon\Field;
		$field->title = Input::get('title');
		$field->field_name = Str::slug(Input::get('title'));
		$field->type = Input::get('type');
		$field->visibility = Input::get('visibility');
		$field->required = (Input::get('title') == 'on') ? 1 : 0;
		$field->save();
		$object->objects()->insert($field);
		
		return Redirect::to_route('fields', $id);
	}
}
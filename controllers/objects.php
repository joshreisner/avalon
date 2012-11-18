<?php
class Avalon_Objects_Controller extends Controller {
	
	public $restful = true;
	
	public function get_add() {
		return View::make('avalon::objects.add');
	}
	
	public function get_edit($id) {
		$object = \Avalon\Object::find($id);
		return View::make('avalon::objects.edit')->with('object', $object);
	}
	
	public function get_list() {
		$user = Auth::user();
		$objects = \Avalon\Object::where('active','=',1)->order_by('list_grouping')->order_by('title')->get(array('id', 'title', 'list_grouping'));
		return View::make('avalon::objects.list')->with('user', $user)->with('objects', $objects);
	}

	public function post_add() {
		$object = new \Avalon\Object;
		$object->title = Input::get('title');
		$object->list_grouping = Input::get('list_grouping');
		$object->table_name = Str::slug($object->title, '_');
		$object->active = 1;
		$object->save();
		
		return Redirect::to_route('objects');
	}
	
	public function put_edit($id) {
		$object = \Avalon\Object::find($id);
		$object->title = Input::get('title');
		$object->list_grouping = Input::get('list_grouping');
		$object->save();
		return Redirect::to_route('instances', $object->id);
	}
}
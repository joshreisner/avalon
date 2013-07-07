<?php

class InstanceController extends \BaseController {

	//show create form for an object instance
	public function create($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		$fields = DB::table('avalon_fields')->where('object_id', $object_id)->orderBy('precedence')->get();
		return View::make('avalon::instances.create', array(
			'object'=>$object, 
			'fields'=>$fields
		));
	}

	//save a new object instance to the database
	public function store($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		$fields = DB::table('avalon_fields')->where('object_id', $object_id)->get();
		
		//metadata
		$inserts = array(
			'updated_at'=>new DateTime,
			'updated_by'=>Session::get('avalon_id'),
			'precedence'=>DB::table($object->name)->max('precedence') + 1
		);
		
		//add each field if present
		foreach ($fields as $field) {
			if ($value = Input::get($field->name)) {
				//do any per-field type processing here, eg dates
				$inserts[$field->name] = $value;
			}
		}
		
		DB::table($object->name)->insert($inserts);
		
		//update objects table with latest counts
		DB::table('avalon_objects')->where('id', $object_id)->update(array(
			'instance_count'=>DB::table($object->name)->count(),
			'instance_updated_at'=>new DateTime,
			'instance_updated_by'=>Session::get('avalon_id')
		));
		
		return Redirect::action('ObjectController@show', $object_id);
	}
	
	//show edit form
	public function edit($object_id, $instance_id) {
		
	}
	
	//save edits to database
	public function update($object_id, $instance_id) {
		
	}
	
	//deactivate instance
	public function destroy($object_id, $instance_id) {
		
	}
}
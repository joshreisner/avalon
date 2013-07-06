<?php

class InstanceController extends \BaseController {

	//show list of instances for an object
	public function get_index($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		$fields = DB::table('avalon_fields')->where('object_id', $object_id)->where('visibility', 'list')->get();
		$instances = DB::table($object->name)->get(); //todo select only $fields
		
		foreach ($instances as &$instance) {
			$instance->updated_at = \Carbon\Carbon::createFromTimeStamp(strtotime($instance->updated_at))->diffForHumans();
		}
		
		return View::make('avalon::instances.index', array(
			'object'=>$object, 
			'fields'=>$fields, 
			'instances'=>$instances
		));
	}

	//show create form for an object instance
	public function get_create($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		$fields = DB::table('avalon_fields')->where('object_id', $object_id)->get();
		return View::make('avalon::instances.create', array(
			'object'=>$object, 
			'fields'=>$fields
		));
	}

	//save a new object to the database
	public function post_store($object_id) {
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
		
		return Redirect::to('/login/objects/' . $object_id, 303);
	}
}
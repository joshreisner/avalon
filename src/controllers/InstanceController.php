<?php

class InstanceController extends \BaseController {

	public function get_index($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->select('id', 'title', 'table_name', 'order_by', 'direction', 'list_help')->first();
		$fields = DB::table('avalon_fields')->where('object_id', $object_id)->where('visibility', 'list')->select('title', 'field_name', 'precedence')->get();
		$instances = DB::table($object->table_name)->get(); //todo select only $fields
		return View::make('avalon::instances.index')->with(array('object'=>$object, 'fields'=>$fields, 'instances'=>$instances));
	}

	public function get_create($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->select('id', 'title', 'table_name', 'order_by', 'direction', 'list_help')->first();
		$fields = DB::table('avalon_fields')->where('object_id', $object_id)->whereIn('visibility', array('list', 'normal'))->select('title', 'field_name')->get();
		return View::make('avalon::instances.create')->with(array('object'=>$object, 'fields'=>$fields));
	}

}
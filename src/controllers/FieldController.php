<?php

class FieldController extends \BaseController {

	private static $types = array('string'=>'String');
	private static $visibility = array(
		'normal'=>'Normal',
		'list'=>'Show in List',
		'hidden'=>'Hidden',
	);

	//show a list of an object's fields
	public function index($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		$fields = DB::table('avalon_fields')->where('object_id', $object_id)->orderBy('precedence')->get();
		foreach ($fields as &$field) {
			if (!empty($field->updated_at)) $field->updated_at = \Carbon\Carbon::createFromTimeStamp(strtotime($field->updated_at))->diffForHumans();
		}
		return View::make('avalon::fields.index', array(
			'object'=>$object,
			'fields'=>$fields
		));
	}
	
	//show create form
	public function create($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		return View::make('avalon::fields.create', array(
			'object'=>$object,
			'types'=>self::$types,
			'visibility'=>self::$visibility,
		));
	}
	
	//save form data to fields, add new column to object
	public function store($object_id) {
		$table_name = DB::table('avalon_objects')->where('id', $object_id)->pluck('name');
		$field_name = Str::slug(Input::get('title'));
		$type = Input::get('type');
		$required = Input::get('required');
		
		Schema::table($table_name, function($table) use ($type, $field_name, $required) {
			switch ($type) {
				case 'string':
					$table->string($field_name);
					break;
			}
		});

		DB::table('avalon_fields')->insert(array(
			'title'=>Input::get('title'),
			'name'=>$field_name,
			'type'=>$type,
			'object_id'=>$object_id,
			'visibility'=>Input::get('visibility'),
			'required'=>$required,
			'updated_by'=>Session::get('avalon_id'),
			'updated_at'=>new DateTime,
		));
		
		return Redirect::action('FieldController@index', $object_id);
	}
	
}
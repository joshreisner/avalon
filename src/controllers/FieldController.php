<?php

class FieldController extends \BaseController {

	private static $types = array(
		'string'=>'String',
		'text'=>'Text',
	);
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
		$field_name = Str::slug(Input::get('title'), '_');
		$type		= Input::get('type');
		$required	= Input::has('required') ? 1 : 0;
		
		Schema::table($table_name, function($table) use ($type, $field_name, $required) {
			switch ($type) {
				case 'string':
					if ($required) {
						$table->string($field_name);
					} else {
						$table->string($field_name)->nullable();
					}
					break;
				case 'text':
					if ($required) {
						$table->text($field_name);
					} else {
						$table->text($field_name)->nullable();
					}
					break;
			}
		});

		DB::table('avalon_fields')->insert(array(
			'title'		=>Input::get('title'),
			'name'		=>$field_name,
			'type'		=>$type,
			'object_id'	=>$object_id,
			'visibility'=>Input::get('visibility'),
			'required'	=>$required,
			'precedence'=>DB::table('avalon_fields')->where('object_id', $object_id)->max('precedence') + 1,
			'updated_by'=>Session::get('avalon_id'),
			'updated_at'=>new DateTime,
		));
		
		return Redirect::action('FieldController@index', $object_id);
	}
	
	//show edit form
	public function edit($object_id, $field_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		$field = DB::table('avalon_fields')->where('id', $field_id)->first();
		return View::make('avalon::fields.edit', array(
			'object'=>$object,
			'field'=>$field,
			'visibility'=>self::$visibility,
			'types'=>self::$types,
		));
	}
	
	//save edits to database
	public function update($object_id, $field_id) {
		
	}
	
	//delete field & remove from database
	public function destroy($object_id, $field_id) {
		$table_name = DB::table('avalon_objects')->where('id', $object_id)->pluck('name');
		$field_name = DB::table('avalon_fields')->where('id', $field_id)->pluck('name');
		Schema::table($table_name, function($table) use ($field_name) {
			$table->dropColumn($field_name);
		});
		DB::table('avalon_fields')->where('id', $field_id)->delete();
		return Redirect::action('FieldController@index', $object_id);
	}
	
	//reorder fields by drag-and-drop
	public function postReorder($object_id) {
		$fields = explode('&', Input::get('order'));
		$precedence = 1;
		foreach ($fields as $field) {
			list($garbage, $id) = explode('=', $field);
			if (!empty($id)) {
				DB::table('avalon_fields')->where('id', $id)->update(array('precedence'=>$precedence));
				$precedence++;
			}
		}
	}
}
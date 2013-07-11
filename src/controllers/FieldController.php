<?php

class FieldController extends \BaseController {

	private static $types = array(
		'checkboxes'	=>'Checkboxes',
		'date'			=>'Date',
		'datetime'		=>'Date + Time',
		'html'			=>'HTML',
		'select'		=>'Select',
		'slug'			=>'Slug',
		'string'		=>'String',
		'text'			=>'Text',
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
		return View::make('avalon::fields.index', array(
			'object'=>$object,
			'fields'=>$fields,
			'types'=>self::$types,
		));
	}
	
	//show create form
	public function create($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();

		$related_fields = DB::table('avalon_fields')
				->where('object_id', $object->id)
				->where('type', 'string')
				->orderBy('precedence')
				->get();

		$related_objects = DB::table('avalon_objects')
				->where('id', '<>', $object_id)
				->orderBy('title')
				->get();

		return View::make('avalon::fields.create', array(
			'object'=>$object,
			'types'=>self::$types,
			'related_fields'=>$related_fields,
			'related_objects'=>$related_objects,
			'visibility'=>self::$visibility,
		));
	}
	
	//save form data to fields, add new column to object
	public function store($object_id) {
		$type		= Input::get('type');
		$required	= Input::has('required') ? 1 : 0;
		
		$table_name = DB::table('avalon_objects')->where('id', $object_id)->pluck('name');

		if ($type == 'checkboxes') {
			//use field_name to store joining table
			$columns = array(
				Str::singular(DB::table('avalon_objects')->where('id', Input::get('related_object_id'))->pluck('name')), 
				Str::singular($table_name)
			);
			sort($columns);
			$field_name = implode('_', $columns);

			//create joining table
			Schema::create($field_name, function ($table) use ($columns) {
				foreach ($columns as $column) {
					$table->integer($column . '_id');
				}
			});
		} else {
			$field_name = Str::slug(Input::get('title'), '_');
			if ($type == 'select' && !Str::endsWith($field_name, '_id')) $field_name .= '_id';

			//add new column
			Schema::table($table_name, function($table) use ($type, $field_name, $required) {
				switch ($type) {

					case 'date':
						if ($required) {
							$table->date($field_name);
						} else {
							$table->date($field_name)->nullable();
						}
						break;
					
					case 'datetime':
						if ($required) {
							$table->dateTime($field_name);
						} else {
							$table->dateTime($field_name)->nullable();
						}
						break;
					
						break;

					case 'select':
						if ($required) {
							$table->integer($field_name);
						} else {
							$table->integer($field_name)->nullable();
						}
						break;

					case 'slug':
					case 'string':
						if ($required) {
							$table->string($field_name);
						} else {
							$table->string($field_name)->nullable();
						}
						break;
					
					case 'html':
					case 'text':
						if ($required) {
							$table->text($field_name);
						} else {
							$table->text($field_name)->nullable();
						}
				}
			});

			//set existing default values for required dates to today, better than 0000-00-00
			if (in_array($type, array('date', 'datetime')) && $required) {
				DB::table($table_name)->update(array($field_name=>new DateTime)); 
			}
		}

		//related field and object
		//laravel doesn't seem to have a conventient way to handle nullable incoming integers
		$related_field_id = Input::get('related_field_id');
		if (empty($related_field_id)) $related_field_id = null;

		$related_object_id = Input::get('related_object_id');
		if (empty($related_object_id)) $related_object_id = null;

		//save field info to fields table
		DB::table('avalon_fields')->insert(array(
			'title'				=>Input::get('title'),
			'name'				=>$field_name,
			'type'				=>$type,
			'object_id'			=>$object_id,
			'visibility'		=>Input::get('visibility'),
			'related_field_id'	=>$related_field_id,
			'related_object_id'	=>$related_object_id,
			'required'			=>$required,
			'precedence'		=>DB::table('avalon_fields')->where('object_id', $object_id)->max('precedence') + 1,
			'updated_by'		=>Session::get('avalon_id'),
			'updated_at'		=>new DateTime,
		));
		
		return Redirect::action('FieldController@index', $object_id);
	}
	
	//show edit form
	public function edit($object_id, $field_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		$field = DB::table('avalon_fields')->where('id', $field_id)->first();

		$related_fields = DB::table('avalon_fields')
				->where('object_id', $field->object_id)
				->where('id', '<>', $field->id)
				->where('type', 'string')
				->orderBy('precedence')
				->get();

		$related_objects = DB::table('avalon_objects')
				->where('id', '<>', $object_id)
				->orderBy('title')
				->get();

		return View::make('avalon::fields.edit', array(
			'object'=>$object,
			'field'=>$field,
			'related_fields'=>$related_fields,
			'related_objects'=>$related_objects,
			'visibility'=>self::$visibility,
			'types'=>self::$types,
		));
	}
	
	//save edits to database
	public function update($object_id, $field_id) {
		$table_name = DB::table('avalon_objects')->where('id', $object_id)->pluck('name');
		$new_field_name = Str::slug(Input::get('name'), '_');
		$old_field_name = DB::table('avalon_fields')->where('id', $field_id)->pluck('name');
		$required	= Input::has('required') ? 1 : 0;

		//rename column if necessary		
		if ($old_field_name != $new_field_name) {
			Schema::table($table_name, function($table) use ($old_field_name, $new_field_name) {
				//todo check in a bit to see if this is working -- mysterious error "Call to undefined method"
				//$table->renameColumn($old_field_name, $new_field_name);
			});
		}

		//related field and object
		//todo be sure laravel doesn't do this automatically
		$related_field_id = Input::get('related_field_id');
		if (empty($related_field_id)) $related_field_id = null;

		$related_object_id = Input::get('related_object_id');
		if (empty($related_object_id)) $related_object_id = null;

		DB::table('avalon_fields')->where('id', $field_id)->update(array(
			'title'				=>Input::get('title'),
			//'name'			=>$new_field_name,
			'visibility'		=>Input::get('visibility'),
			'related_field_id'	=>$related_field_id,
			'related_object_id'	=>$related_object_id,
			'required'			=>$required,
			'updated_by'		=>Session::get('avalon_id'),
			'updated_at'		=>new DateTime,
		));
		
		return Redirect::action('FieldController@index', $object_id);
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
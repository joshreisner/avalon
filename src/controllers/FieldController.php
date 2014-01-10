<?php

class FieldController extends \BaseController {

	private static $types = array(
		//'checkboxes'	=>'Checkboxes',
		'color'			=>'Color',
		'date'			=>'Date',
		'datetime'		=>'Date + Time',
		'html'			=>'HTML',
		//'images'		=>'Images',
		'integer'		=>'Integer',
		'select'		=>'Select',
		'slug'			=>'Slug',
		'string'		=>'String',
		'text'			=>'Text',
		'time'			=>'Time',
		'url'			=>'URL',
	);
	
	private static $visibility = array(
		'list'=>'Show in List',
		'normal'=>'Normal',
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
				->lists('title', 'id');

		$related_objects = DB::table('avalon_objects')
				->where('id', '<>', $object_id)
				->orderBy('title')
				->lists('title', 'id');

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

					case 'color':
						if ($required) {
							$table->string($field_name, 6);
						} else {
							$table->string($field_name, 6)->nullable();
						}
						break;
					
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

					case 'integer':
					case 'select':
						if ($required) {
							$table->integer($field_name);
						} else {
							$table->integer($field_name)->nullable();
						}
						break;

					case 'slug':
					case 'string':
					case 'url':
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

					case 'time':
						if ($required) {
							$table->time($field_name);
						} else {
							$table->time($field_name)->nullable();
						}
				}
			});

			//set existing default values for required dates to today, better than 0000-00-00
			if (in_array($type, array('date', 'datetime')) && $required) {
				DB::table($table_name)->update(array($field_name=>new DateTime)); 
			}
		}

		//save field info to fields table
		DB::table('avalon_fields')->insert(array(
			'title'				=>Input::get('title'),
			'name'				=>$field_name,
			'type'				=>$type,
			'object_id'			=>$object_id,
			'visibility'		=>Input::get('visibility'),
			'width'				=>Input::has('width') ? Input::get('width') : null,
			'height'			=>Input::has('height') ? Input::get('height') : null,
			'related_field_id'	=>Input::has('related_field_id') ? Input::get('related_field_id') : null,
			'related_object_id'	=>Input::has('related_object_id') ? Input::get('related_object_id') : null,
			'required'			=>$required,
			'precedence'		=>DB::table('avalon_fields')->where('object_id', $object_id)->max('precedence') + 1,
			'updater'		=>Session::get('avalon_id'),
			'updated'		=>new DateTime,
		));
		
		return Redirect::action('FieldController@index', $object_id);
	}
	
	//show edit form
	public function edit($object_id, $field_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		$field = DB::table('avalon_fields')->where('id', $field_id)->first();

		$related_fields = array(''=>'') + DB::table('avalon_fields')
				->where('object_id', $field->object_id)
				->where('id', '<>', $field->id)
				->where('type', 'string')
				->orderBy('precedence')
				->lists('title', 'id');

		$related_objects = array(''=>'') + DB::table('avalon_objects')
				->where('id', '<>', $object_id)
				->orderBy('title')
				->lists('title', 'id');

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
		$field_name = Str::slug(Input::get('name'), '_');
		$required	= Input::has('required') ? 1 : 0;
		$field = DB::table('avalon_fields')->where('id', $field_id)->first();

		//rename column if necessary		
		if ($field->name != $field_name) {
			Schema::table($table_name, function($table) use ($field, $field_name) {
				//todo check in a bit to see if this is working -- mysterious error "Call to undefined method"
				$table->renameColumn($field->name, $field_name);
			});
		}

		//change nullability if necessary
		if ($field->required != $required) {
			//can't decide whether to attempt with DB::statement() or to use schema builder to 
			//make a new column and then copy. schema seems more appropriate but would require
			//a refactor of the column-adding above to avoid too much repetition
		}

		//related field and object
		DB::table('avalon_fields')->where('id', $field_id)->update(array(
			'title'				=>Input::get('title'),
			'name'				=>$field_name,
			'visibility'		=>Input::get('visibility'),
			'width'				=>Input::has('width') ? Input::get('width') : null,
			'height'			=>Input::has('height') ? Input::get('height') : null,
			'related_field_id'	=>Input::has('related_field_id') ? Input::get('related_field_id') : null,
			'related_object_id'	=>Input::has('related_object_id') ? Input::get('related_object_id') : null,
			'required'			=>$required,
			'updater'			=>Session::get('avalon_id'),
			'updated'			=>new DateTime,
		));
		
		return Redirect::action('FieldController@index', $object_id);
	}
	
	//delete field & remove from database
	public function destroy($object_id, $field_id) {
		$table = DB::table('avalon_objects')->where('id', $object_id)->first();
		$field = DB::table('avalon_fields')->where('id', $field_id)->first();

		if ($field->type == 'checkboxes') {
			Schema::dropIfExists($field->name);
		} else {
			Schema::table($table->name, function($table) use ($field) {
				$table->dropColumn($field->name);
			});
		}
		
		DB::table('avalon_fields')->where('id', $field_id)->delete();
		return Redirect::action('FieldController@index', $object_id);
	}
	
	//reorder fields by drag-and-drop
	public function reorder($object_id) {
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
<?php

class FieldController extends \BaseController {

	//this could probably be a variable no?
	private static function types() {

		return array(
			trans('avalon::messages.fields_types_cat_dates')=> array(
				'date'			=>trans('avalon::messages.fields_types_date'),
				'datetime'		=>trans('avalon::messages.fields_types_datetime'),
				'time'			=>trans('avalon::messages.fields_types_time'),
			),
			trans('avalon::messages.fields_types_cat_files')=>array(
				'image'			=>trans('avalon::messages.fields_types_image'),
				'images'		=>trans('avalon::messages.fields_types_images'),
				//'file'		=>'File',
				//'files'		=>'Files',
			),
			trans('avalon::messages.fields_types_cat_strings')=> array(
				'html'			=>trans('avalon::messages.fields_types_html'),
				'slug'			=>trans('avalon::messages.fields_types_slug'),
				'string'		=>trans('avalon::messages.fields_types_string'),
				'text'			=>trans('avalon::messages.fields_types_text'),
				'url'			=>trans('avalon::messages.fields_types_url'),
			),
			trans('avalon::messages.fields_types_cat_numbers')=> array(
				'integer'		=>trans('avalon::messages.fields_types_integer'),
				//'money'		=>'Money',
				//'decimal'		=>'Decimal',
			),
			trans('avalon::messages.fields_types_cat_relationships')=>array(
				'checkboxes'	=>trans('avalon::messages.fields_types_checkboxes'),
				'select'		=>trans('avalon::messages.fields_types_select'),
			),
			trans('avalon::messages.fields_types_cat_misc')=>array(
				'color'			=>trans('avalon::messages.fields_types_color'),
			),
		);
	}
	
	//also probably could be a variable
	private static function visibility() {
		return array(
			'list'	=>trans('avalon::messages.fields_visibility_list'),
			'normal'=>trans('avalon::messages.fields_visibility_normal'),
			'hidden'=>trans('avalon::messages.fields_visibility_hidden'),
		);
	}

	//show a list of an object's fields
	public function index($object_name) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object->id)->orderBy('precedence')->get();
		foreach ($fields as &$field) {
			$field->link = URL::action('FieldController@edit', array($object->name, $field->id));
			$field->type = trans('avalon::messages.fields_types_' . $field->type);
		}
		return View::make('avalon::fields.index', array(
			'object'=>$object,
			'fields'=>$fields,
		));
	}
	
	//show create form
	public function create($object_name) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();

		$related_fields = DB::table(DB_FIELDS)
				->where('object_id', $object->id)
				->where('type', 'string')
				->orderBy('precedence')
				->lists('title', 'id');

		$related_objects = DB::table(DB_OBJECTS)
				->orderBy('title')
				->lists('title', 'id');

		return View::make('avalon::fields.create', array(
			'object'			=>$object,
			'related_fields'	=>array(''=>'') + $related_fields,
			'related_objects'	=>array(''=>'') + $related_objects,
			'visibility'		=>self::visibility(),
			'types'				=>self::types(),
		));
	}
	
	//save form data to fields, add new column to object
	public function store($object_name) {
		$type		= Input::get('type');
		$required	= Input::has('required') ? 1 : 0;
		
		$table_name = DB::table(DB_OBJECTS)->where('name', $object_name)->pluck('name');

		if ($type == 'checkboxes') {
			//use field_name to store joining table
			$columns = array(
				Str::singular(DB::table(DB_OBJECTS)->where('id', Input::get('related_object_id'))->pluck('name')), 
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

			//add _id suffix to foreign key columns (convention, also relationship eg hasOne() conflict)
			if (in_array($type, array('select', 'image')) && !Str::endsWith($field_name, '_id')) $field_name .= '_id';

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

					case 'image':
					case 'integer':
					case 'select':
						if ($required) {
							$table->integer($field_name);
						} else {
							$table->integer($field_name)->nullable();
						}
						break;

					case 'images':
					//no column here? was considering an int count?
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
						break;

					case 'time':
						if ($required) {
							$table->time($field_name);
						} else {
							$table->time($field_name)->nullable();
						}
						break;
				}
			});

			//set existing default values for required dates to today, better than 0000-00-00
			if (in_array($type, array('date', 'datetime')) && $required) {
				DB::table($table_name)->update(array($field_name=>new DateTime)); 
			}
		}

		//save field info to fields table
		$object_id = DB::table(DB_OBJECTS)->where('name', $object_name)->pluck('id');
		$field_id = DB::table(DB_FIELDS)->insertGetId(array(
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
			'precedence'		=>DB::table(DB_FIELDS)->where('object_id', $object_id)->max('precedence') + 1,
			'updated_by'		=>Auth::user()->id,
			'updated_at'		=>new DateTime,
		));

		self::organizeTable($object_id);
		
		return Redirect::action('FieldController@index', $object_name)->with('field_id', $field_id);
	}
	
	//show edit form
	public function edit($object_name, $field_id) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$field = DB::table(DB_FIELDS)->where('id', $field_id)->first();

		$related_fields = array(''=>'') + DB::table(DB_FIELDS)
				->where('object_id', $field->object_id)
				->where('id', '<>', $field->id)
				->where('type', 'string')
				->orderBy('precedence')
				->lists('title', 'id');

		$related_objects = array(''=>'') + DB::table(DB_OBJECTS)
				->orderBy('title')
				->lists('title', 'id');

		return View::make('avalon::fields.edit', array(
			'object'			=>$object,
			'field'				=>$field,
			'related_fields'	=>$related_fields,
			'related_objects'	=>$related_objects,
			'visibility'		=>self::visibility(),
			'types'				=>self::types(),
		));
	}
	
	//save edits to database
	public function update($object_name, $field_id) {
		$table_name = DB::table(DB_OBJECTS)->where('name', $object_name)->pluck('name');
		$field_name = Str::slug(Input::get('name'), '_');
		$required	= Input::has('required') ? 1 : 0;
		$field = DB::table(DB_FIELDS)->where('id', $field_id)->first();

		//rename column if necessary		
		if ($field->name != $field_name) {
			Schema::table($table_name, function($table) use ($field, $field_name) {
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
		DB::table(DB_FIELDS)->where('id', $field_id)->update(array(
			'title'				=>Input::get('title'),
			'name'				=>$field_name,
			'visibility'		=>Input::get('visibility'),
			'width'				=>Input::has('width') ? Input::get('width') : null,
			'height'			=>Input::has('height') ? Input::get('height') : null,
			'related_field_id'	=>Input::has('related_field_id') ? Input::get('related_field_id') : null,
			'related_object_id'	=>Input::has('related_object_id') ? Input::get('related_object_id') : null,
			'required'			=>$required,
			'updated_by'		=>Auth::user()->id,
			'updated_at'		=>new DateTime,
		));
		
		return Redirect::action('FieldController@index', $object_name)->with('field_id', $field_id);
	}
	
	//delete field & remove from database
	public function destroy($object_name, $field_id) {
		$table = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$field = DB::table(DB_FIELDS)->where('id', $field_id)->first();

		if ($field->type == 'checkboxes') {
			Schema::dropIfExists($field->name);
		} elseif (Schema::hasColumn($table->name, $field->name)) {
			Schema::table($table->name, function($table) use ($field) {
				$table->dropColumn($field->name);
			});
		}
		
		DB::table(DB_FIELDS)->where('id', $field_id)->delete();
		return Redirect::action('FieldController@index', $object_name);
	}
	
	//reorder fields by drag-and-drop
	public function reorder($object_id) {
		$fields = explode('&', Input::get('order'));
		$precedence = 1;
		foreach ($fields as $field) {
			list($garbage, $id) = explode('=', $field);
			if (!empty($id)) {
				DB::table(DB_FIELDS)->where('id', $id)->update(array('precedence'=>$precedence));
				$precedence++;
			}
		}

		self::organizeTable($object_id);
	}

	private static function organizeTable($object_id) {
		//reorder actual table fields
		$object = DB::table(DB_OBJECTS)->where('id', $object_id)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object_id)->whereNotIn('type', array('checkboxes', 'images'))->orderBy('precedence')->get();
		$system = array('created_at', 'updated_at', 'updated_by', 'deleted_at', 'precedence');

		db::unprepared('ALTER TABLE ' . $object->name . ' MODIFY COLUMN id INT NOT NULL AUTO_INCREMENT FIRST');
		$last = 'id';
		foreach ($fields as $field) {
			db::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN `' . $field->name . '` ' . self::type($field->type) . ' AFTER ' . $last);
			$last = $field->name;
		}

		//if there are non-system columns, reorder
		db::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN created_at DATETIME NOT NULL AFTER ' . $last);
		db::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN updated_at DATETIME NOT NULL AFTER created_at');
		db::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN updated_by INT 			   AFTER updated_at');
		db::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN deleted_at DATETIME 		   AFTER updated_by');
		db::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN precedence INT 	  NOT NULL AFTER deleted_at');
	}

	//format field type
	private static function type($type) {
		switch ($type) {

			case 'color':
				return 'VARCHAR(7)';
			
			case 'date':
				return 'DATE';

			case 'datetime':
				return 'DATETIME';

			case 'image':
			case 'integer':
			case 'select':
				return 'INTEGER';

			case 'slug':
			case 'string':
			case 'url':
				return 'VARCHAR(255)';
			
			case 'html':
			case 'text':
				return 'TEXT';

			case 'time':
				return 'TIME';
		}	
	}
}
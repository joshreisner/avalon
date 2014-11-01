<?php

class FieldController extends \BaseController {

	//this could probably be a variable no?
	private static function types() {

		return [
			trans('avalon::messages.fields_types_cat_dates')=> [
				'date'			=>trans('avalon::messages.fields_types_date'),
				'datetime'		=>trans('avalon::messages.fields_types_datetime'),
				'time'			=>trans('avalon::messages.fields_types_time'),
			],
			trans('avalon::messages.fields_types_cat_files')=>[
				'image'			=>trans('avalon::messages.fields_types_image'),
				'images'		=>trans('avalon::messages.fields_types_images'),
				//'file'		=>'File',
				//'files'		=>'Files',
			],
			trans('avalon::messages.fields_types_cat_strings')=>[
				'html'			=>trans('avalon::messages.fields_types_html'),
				'slug'			=>trans('avalon::messages.fields_types_slug'),
				'string'		=>trans('avalon::messages.fields_types_string'),
				'text'			=>trans('avalon::messages.fields_types_text'),
				'url'			=>trans('avalon::messages.fields_types_url'),
			],
			trans('avalon::messages.fields_types_cat_numbers')=>[
				//'decimal'		=>'Decimal',
				'integer'		=>trans('avalon::messages.fields_types_integer'),
				'money'			=>trans('avalon::messages.fields_types_money'),
			],
			trans('avalon::messages.fields_types_cat_relationships')=>[
				'checkboxes'	=>trans('avalon::messages.fields_types_checkboxes'),
				'select'		=>trans('avalon::messages.fields_types_select'),
			],
			trans('avalon::messages.fields_types_cat_misc')=>[
				'checkbox'		=>trans('avalon::messages.fields_types_checkbox'),
				'color'			=>trans('avalon::messages.fields_types_color'),
				'user'			=>trans('avalon::messages.fields_types_user'),
			],
		];
	}
	
	//also probably could be a variable
	private static function visibility() {
		return [
			'list'	=>trans('avalon::messages.fields_visibility_list'),
			'normal'=>trans('avalon::messages.fields_visibility_normal'),
			'hidden'=>trans('avalon::messages.fields_visibility_hidden'),
		];
	}

	//show a list of an object's fields
	public function index($object_name) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object->id)->orderBy('precedence')->get();
		foreach ($fields as &$field) {
			$field->link = URL::action('FieldController@edit', array($object->name, $field->id));
			$field->type = trans('avalon::messages.fields_types_' . $field->type);
		}
		return View::make('avalon::fields.index', [
			'object'=>$object,
			'fields'=>$fields,
		]);
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

		return View::make('avalon::fields.create', [
			'object'			=>$object,
			'related_fields'	=>array(''=>'') + $related_fields,
			'related_objects'	=>array(''=>'') + $related_objects,
			'visibility'		=>self::visibility(),
			'types'				=>self::types(),
		]);
	}
	
	//save form data to fields, add new column to object
	public function store($object_name) {
		$type		= Input::get('type');
		$required	= Input::has('required') ? 1 : 0;
		
		if ($type == 'checkboxes') {
			//use field_name to store joining table
			$columns = [
				Str::singular(DB::table(DB_OBJECTS)->where('id', Input::get('related_object_id'))->pluck('name')), 
				Str::singular($object_name)
			];
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
			if (in_array($type, ['select', 'image', 'user']) && !Str::endsWith($field_name, '_id')) $field_name .= '_id';

			//checkboxes can't be 'required' (or it's always required)
			if ($type == 'checkbox') $required = false;

			//add new column
			Schema::table($object_name, function($table) use ($type, $field_name, $required) {
				switch ($type) {

					case 'checkbox':
						$table->boolean($field_name)->default(false);
						break;
					
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

					case 'html':
					case 'text':
						if ($required) {
							$table->text($field_name);
						} else {
							$table->text($field_name)->nullable();
						}
						break;

					case 'image':
					case 'integer':
					case 'select':
					case 'user':
						if ($required) {
							$table->integer($field_name);
						} else {
							$table->integer($field_name)->nullable();
						}
						break;

					case 'money':
						if ($required) {
							$table->decimal($field_name, 10, 2);
						} else {
							$table->integer($field_name, 10, 2)->nullable();
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
			if (in_array($type, ['date', 'datetime']) && $required) {
				DB::table($object_name)->update([$field_name=>new DateTime]);
			}
		}

		//save field info to fields table
		$object_id = DB::table(DB_OBJECTS)->where('name', $object_name)->pluck('id');
		$field_id = DB::table(DB_FIELDS)->insertGetId([
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
		]);

		self::organizeTable($object_name);
		
		return Redirect::action('FieldController@index', $object_name)->with('field_id', $field_id);
	}
	
	//show edit form
	public function edit($object_name, $field_id) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$field = DB::table(DB_FIELDS)->where('id', $field_id)->first();

		$related_fields = [''=>''] + DB::table(DB_FIELDS)
			->where('object_id', $field->object_id)
			->where('id', '<>', $field->id)
			->where('type', 'string')
			->orderBy('precedence')
			->lists('title', 'id');

		$related_objects = [''=>''] + DB::table(DB_OBJECTS)
			->orderBy('title')
			->lists('title', 'id');

		return View::make('avalon::fields.edit', [
			'object'			=>$object,
			'field'				=>$field,
			'related_fields'	=>$related_fields,
			'related_objects'	=>$related_objects,
			'visibility'		=>self::visibility(),
			'types'				=>self::types(),
		]);
	}
	
	//save edits to database
	public function update($object_name, $field_id) {
		$field = DB::table(DB_FIELDS)->where('id', $field_id)->first();
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$field_name = Str::slug(Input::get('name'), '_');
		$required	= Input::has('required') ? 1 : 0;

		//rename column if necessary		
		if ($field->name != $field_name) {
			Schema::table($object_name, function($table) use ($field, $field_name) {
				$table->renameColumn($field->name, $field_name);
			});

			//update object sort order if necessary
			if ($object->order_by == $field->name) {
				DB::table(DB_OBJECTS)->where('name', $object_name)->update(array(
					'order_by' => $field_name
				));
			}
		}

		//change nullability if necessary
		if ($field->required != $required) {
			//can't decide whether to attempt with DB::statement() or to use schema builder to 
			//make a new column and then copy. schema seems more appropriate but would require
			//a refactor of the column-adding above to avoid too much repetition
		}

		//related field and object
		DB::table(DB_FIELDS)->where('id', $field_id)->update([
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
		]);
		
		return Redirect::action('FieldController@index', $object_name)->with('field_id', $field_id);
	}
	
	//delete field & remove from database
	public function destroy($object_name, $field_id) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$field = DB::table(DB_FIELDS)->where('id', $field_id)->first();

		if ($field->type == 'checkboxes') {
			Schema::dropIfExists($field->name);
		} elseif (Schema::hasColumn($object->name, $field->name)) {
			Schema::table($object->name, function($table) use ($field) {
				$table->dropColumn($field->name);
			});
		}

		//we're deleting the object's order_by field, so reset to default
		if ($object->order_by == $field->name) {
			DB::table(DB_OBJECTS)->where('name', $object_name)->update([
				'order_by' => 'precedence'
			]);
		}

		DB::table(DB_FIELDS)->where('id', $field_id)->delete();
		return Redirect::action('FieldController@index', $object_name);
	}
	
	//reorder fields by drag-and-drop
	public function reorder($object_name) {
		$fields = explode('&', Input::get('order'));
		$precedence = 1;
		foreach ($fields as $field) {
			list($garbage, $id) = explode('=', $field);
			if (!empty($id)) {
				DB::table(DB_FIELDS)->where('id', $id)->update(['precedence'=>$precedence]);
				$precedence++;
			}
		}

		self::organizeTable($object_name);
	}

	private static function organizeTable($object_name) {
		//reorder actual table fields
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object->id)->whereNotIn('type', ['checkboxes', 'images'])->orderBy('precedence')->get();
		$system = ['created_at', 'updated_at', 'updated_by', 'deleted_at', 'precedence'];

		DB::unprepared('ALTER TABLE ' . $object->name . ' MODIFY COLUMN id INT NOT NULL AUTO_INCREMENT FIRST');
		$last = 'id';
		foreach ($fields as $field) {
			DB::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN `' . $field->name . '` ' . self::type($field->type) . ' AFTER ' . $last);
			$last = $field->name;
		}

		//if there are non-system columns, reorder
		DB::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN created_at DATETIME NOT NULL AFTER `' . $last . '`');
		DB::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN updated_at DATETIME NOT NULL AFTER created_at');
		DB::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN updated_by INT 			   AFTER updated_at');
		DB::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN deleted_at DATETIME 		   AFTER updated_by');
		DB::unprepared('ALTER TABLE `' . $object->name . '` MODIFY COLUMN precedence INT 	  NOT NULL AFTER deleted_at');
	}

	//format field type
	private static function type($type) {
		switch ($type) {

			case 'checkbox':
				return 'TINYINT(1)';
			
			case 'color':
				return 'VARCHAR(7)';
			
			case 'date':
				return 'DATE';

			case 'datetime':
				return 'DATETIME';

			case 'image':
			case 'integer':
			case 'select':
			case 'user':
				return 'INTEGER';

			case 'money':
				return 'DECIMAL(10,2)';

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
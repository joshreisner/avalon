<?php

class ObjectController extends BaseController {

	private static $direction = [
		'asc'=>'Ascending',
		'desc'=>'Descending',
	];
	
	# Display list for home page
	public function index() {
		$objects = DB::table(DB_OBJECTS)
			->join(DB_USERS, DB_USERS . '.id', '=', DB_OBJECTS . '.updated_by')
			->select(DB_OBJECTS . '.*', DB_USERS . '.name AS updated_name')
			->orderBy('list_grouping')
			->orderBy('title')
			->get();
		foreach ($objects as &$object) {
			$object->link = URL::action('InstanceController@index', $object->name);
			$object->updated_by = $object->name;
			if ($object->count == 0) $object->instance_count = '';
		}
		return View::make('avalon::objects.index', ['objects'=>$objects]);
	}
	
	# Display create object form
	public function create() {
		$order_by = [trans('avalon::messages.fields_system')=>[
			'id'=>trans('avalon::messages.fields_id'),
			'precedence'=>trans('avalon::messages.fields_precedence'),
			'created_at'=>trans('avalon::messages.fields_created_at'),
			'updated_at'=>trans('avalon::messages.fields_updated_at'),
		]];

		return View::make('avalon::objects.create', [
			'order_by' =>$order_by,
			'direction'	=>self::$direction,
			'list_groupings' =>self::getGroupings(),
		]);
	}
	
	# Store create object form post data
	public function store() {

		//make plural, title case
		$title		= mb_convert_case(Str::plural(Input::get('title')), MB_CASE_TITLE, 'UTF-8');

		//determine table name, todo check if unique
		$name		= Str::slug($title, '_');

		//model name
		$model		= Str::singular(Str::studly($title));
		
		//enforce predence always ascending
		$order_by	= Input::get('order_by');
		$direction 	= Input::get('direction');
		if ($order_by == 'precedence') $direction = 'asc';

		//create entry in objects table for new object
		$object_id = DB::table(DB_OBJECTS)->insertGetId([
			'title'			=> $title,
			'name'			=> $name,
			'model'			=> $model,
			'order_by'		=> $order_by,
			'direction'		=> $direction,
			'list_grouping'	=> Input::get('list_grouping'),
			'updated_at'	=> new DateTime,
			'updated_by'	=> Auth::user()->id,
		]);
		
		//create title field for table by default
		DB::table(DB_FIELDS)->insert([
			'title'			=> 'Title',
			'name'			=> 'title',
			'type'			=> 'string',
			'visibility'	=> 'list',
			'required'		=> 1,
			'object_id'		=> $object_id,
			'updated_at'	=> new DateTime,
			'updated_by'	=> Auth::user()->id,
			'precedence'	=> 1
		]);

		self::addTable($name, true);

		self::saveSchema();
		
		return Redirect::action('InstanceController@index', $name);
	}
	
	# Edit object settings
	public function edit($object_name) {

		//get order by select data
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object->id)->orderBy('precedence')->get();
		$order_by = [];
		foreach ($fields as $field) $order_by[$field->name] = $field->title;
		$order_by = [
			trans('avalon::messages.fields_system')=>[
				'id'=>trans('avalon::messages.fields_id'),
				'slug'=>trans('avalon::messages.fields_slug'),
				'precedence'=>trans('avalon::messages.fields_precedence'),
				'created_at'=>trans('avalon::messages.fields_created_at'),
				'updated_at'=>trans('avalon::messages.fields_updated_at'),
			],
			trans('avalon::messages.fields_user')=>$order_by,
		];

		//related objects are different than dependencies; it's the subset of dependencies that are grouped by this object
		$related_objects = DB::table(DB_FIELDS)
			->join(DB_OBJECTS, DB_OBJECTS . '.group_by_field', '=', DB_FIELDS . '.id')
			->where(DB_FIELDS . '.related_object_id', $object->id)
			->orderBy(DB_OBJECTS . '.title')
			->select(DB_OBJECTS . '.*') //due to bug that leads to ambiguous column error
			->lists('title', 'id');

		//values for the related objects. could be combined with above
		$links = DB::table(DB_OBJECT_LINKS)->where('object_id', $object->id)->lists('linked_id');

		//return view
		return View::make('avalon::objects.edit', [
			'object'			=>$object, 
			'order_by'			=>$order_by,
			'direction'			=>self::$direction,
			'dependencies'		=>DB::table(DB_FIELDS)->where('related_object_id', $object->id)->count(),
			'group_by_field'	=>[''=>''] + DB::table(DB_FIELDS)->where('object_id', $object->id)->where('type', 'select')->lists('title', 'id'),
			'list_groupings'	=>self::getGroupings(),
			'related_objects'	=>$related_objects,
			'links'				=>$links,
		]);
	}
	
	//edit object settings
	public function update($object_name) {

		//rename table if necessary
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$new_name = Str::slug(Input::get('name'), '_');
		if ($object->name != $new_name) Schema::rename($object->name, $new_name);
		
		//enforce predence always ascending
		$order_by = Input::get('order_by');
		$direction = Input::get('direction');
		if ($order_by == 'precedence') $direction = 'asc';

		//not sure why it's necessary, doesn't like empty value all of a sudden
		$group_by_field = Input::has('group_by_field') ? Input::get('group_by_field') : null;

		//linked objects
		DB::table(DB_OBJECT_LINKS)->where('object_id', $object->id)->delete();
		if (Input::has('related_objects')) {
			foreach (Input::get('related_objects') as $linked_id) {
				DB::table(DB_OBJECT_LINKS)->insert([
					'object_id'=>$object->id,
					'linked_id'=>$linked_id,
				]);
			}
		}

		//update objects table
		DB::table(DB_OBJECTS)->where('id', $object->id)->update([
			'title'				=> Input::get('title'),
			'name'				=> $new_name,
			'model'				=> Input::get('model'),
			'url'				=> Input::get('url'),
			'order_by'			=> $order_by,
			'direction'			=> $direction,
			'singleton'			=> Input::has('singleton') ? 1 : 0,
			'can_see'			=> Input::has('can_see') ? 1 : 0,
			'can_create'		=> Input::has('can_create') ? 1 : 0,
			'can_edit'			=> Input::has('can_edit') ? 1 : 0,
			'list_grouping'		=> Input::get('list_grouping'),
			'group_by_field'	=> $group_by_field,
			'list_help'			=> trim(Input::get('list_help')),
			'form_help'			=> trim(Input::get('form_help')),
		]);

		self::saveSchema();
		
		return Redirect::action('InstanceController@index', $new_name);
	}
	
	//destroy object
	public function destroy($object_name) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		Schema::dropIfExists($object->name);
		DB::table(DB_OBJECTS)->where('id', $object->id)->delete();
		DB::table(DB_FIELDS)->where('object_id', $object->id)->delete();
		DB::table(DB_OBJECT_LINKS)->where('object_id', $object->id)->orWhere('linked_id', $object->id)->delete();
		self::saveSchema();
		return Redirect::route('home');
	}

	//for list_grouping typeaheads
	private static function getGroupings() {
		$groupings = DB::table(DB_OBJECTS)->where('list_grouping', '<>', '')->distinct()->orderBy('list_grouping')->lists('list_grouping');
		foreach ($groupings as &$grouping) $grouping = '"' . str_replace('"', '', $grouping) . '"';
		return '[' . implode(',', $groupings) . ']';
	}

	//to create unique table names, also for import controller
	public static function getTables() {
		$return = [];
		$pdo = DB::connection()->getPdo();
		$tables = $pdo->query('SHOW TABLES');
		foreach ($tables as $table) $return[] = array_shift($table);
		return $return;
	}

	//to create unique table names, also for import controller
	public static function getPaths() {
		return ['create', 'import', 'logout'];
	}
	
	//create table with boilerplate fields
	private static function addTable($name, $addTitle=true) {
		Schema::create($name, function($table) use($addTitle){
			$table->increments('id');
			if ($addTitle) $table->string('title');
			$table->string('slug');
			$table->timestamps();
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->softDeletes();
			$table->integer('precedence');
		});		
	}
	
	public static function saveSchema() {
		if (!App::environment('local')) return;
		$filename = storage_path() . '/avalon.schema.json';
		$schema = [
			'generated'=>new DateTime,
			'objects'=>DB::table(DB_OBJECTS)->get(),
			'fields'=>DB::table(DB_FIELDS)->get(),
		];
		file_put_contents($filename, json_encode($schema));
		return;
	}
	
	//load schema from file
	public static function loadSchema() {
		
		//run this first, because any orphaned fields will cause it to squawk
		FieldController::cleanup();
		
		//load schema from file and prepare
		$schema = json_decode(file_get_contents(storage_path() . '/avalon.schema.json'));

		//load current database into $objects and $fields variables
		$objects = $fields = [];
		$db_fields = DB::table(DB_FIELDS)
			->join(DB_OBJECTS, DB_OBJECTS . '.id', '=', DB_FIELDS . '.object_id')
			->select(
				DB_FIELDS . '.object_id',
				DB_FIELDS . '.id AS field_id', 
				DB_OBJECTS . '.name AS table', 
				DB_FIELDS . '.name AS column'
			)->get();
		foreach ($db_fields as $field) {
			if (!array_key_exists($field->object_id, $objects)) $objects[$field->object_id] = $field->table;
			$fields[$field->field_id] = ['table'=>$field->table, 'column'=>$field->column];
		}

		//loop through new object schema and update
		foreach ($schema->objects as $object) {

			$values = [
				'id'=>$object->id,
				'title'=>$object->title,
				'name'=>$object->name,
				'model'=>$object->model,
				'order_by'=>$object->order_by,
				'direction'=>$object->direction,
				'group_by_field'=>$object->group_by_field,
				'list_help'=>$object->list_help,
				'form_help'=>$object->form_help,
				'list_grouping'=>$object->list_grouping,
				'can_create'=>$object->can_create,
				'can_edit'=>$object->can_edit,
				'can_see'=>$object->can_see,
				'url'=>$object->url,
				'singleton'=>$object->singleton,
			];

			if (array_key_exists($object->id, $objects)) {
				DB::table(DB_OBJECTS)->where('id', $object->id)->update($values);
			} else {
				DB::table(DB_OBJECTS)->insert($values);
				self::addTable($object->name);
			}
			
			if (isset($objects[$object->id])) unset($objects[$object->id]);
		}
		
		foreach ($objects as $id=>$table) {
			DB::table(DB_OBJECTS)->where('id', $id)->delete();
			DB::table(DB_FIELDS)->where('object_id', $id)->delete();
			Schema::dropIfExists($table);
		}
		
		foreach ($schema->fields as $field) {

			$values = [
				'id'=>$field->id,
				'object_id'=>$field->object_id,
				'type'=>$field->type,
				'title'=>$field->title,
				'name'=>$field->name,
				'visibility'=>$field->visibility,
				'required'=>$field->required,
				'related_field_id'=>$field->related_field_id,
				'related_object_id'=>$field->related_object_id,
				'width'=>$field->width,
				'height'=>$field->height,
				'help'=>$field->help,
				'updated_at'=>$field->updated_at,
				'updated_by'=>$field->updated_by,
				'precedence'=>$field->precedence,
			];
			
			if ($field->id == 62) {
				dd($field);
			}
			
			if (array_key_exists($field->id, $fields)) {
				DB::table(DB_FIELDS)->where('id', $field->id)->update($values);
			} else {
				DB::table(DB_FIELDS)->insert($values);
				if ($field->type == 'checkboxes') {
					FieldController::addJoiningTable($fields[$field->id]['table'], $field->related_object_id);
				} else {
					FieldController::addColumn($fields[$field->id]['table'], $field->name, $field->type, $field->required);			
				}
			}
			
			if (isset($fields[$field->id])) unset($fields[$field->id]);
		}
		
		foreach ($fields as $id=>$props) {
			extract($props);
			DB::table(DB_FIELDS)->where('id', $id)->delete();
			Schema::dropIfExists($table, $column);
		}

	}
	
}
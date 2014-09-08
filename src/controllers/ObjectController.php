<?php

class ObjectController extends \BaseController {

	private static $direction = array(
		'asc'=>'Ascending',
		'desc'=>'Descending',
	);
	
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
		return View::make('avalon::objects.index', array('objects'=>$objects));
	}
	
	# Display create object form
	public function create() {
		$order_by = array(trans('avalon::messages.fields_system')=>array(
			'id'=>trans('avalon::messages.fields_id'),
			'precedence'=>trans('avalon::messages.fields_precedence'),
			'created_at'=>trans('avalon::messages.fields_created_at'),
			'updated_at'=>trans('avalon::messages.fields_updated_at'),
		));

		return View::make('avalon::objects.create', array(
			'order_by'	=>$order_by,
			'direction'	=>self::$direction,
			'list_groupings' =>self::getGroupings(),
		));
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
		$object_id = DB::table(DB_OBJECTS)->insertGetId(array(
			'title'			=> $title,
			'name'			=> $name,
			'model'			=> $model,
			'order_by'		=> $order_by,
			'direction'		=> $direction,
			'list_grouping'	=> Input::get('list_grouping'),
			'updated_at'	=> new DateTime,
			'updated_by'	=> Auth::user()->id,
		));
		
		//create title field for table by default
		DB::table(DB_FIELDS)->insert(array(
			'title'			=> 'Title',
			'name'			=> 'title',
			'type'			=> 'string',
			'visibility'	=> 'list',
			'required'		=> 1,
			'object_id'		=> $object_id,
			'updated_at'	=> new DateTime,
			'updated_by'	=> Auth::user()->id,
			'precedence'	=> 1
		));
		
		//create table with boilerplate fields
		Schema::create($name, function($table){
			$table->increments('id');
			$table->string('title');
			$table->timestamps();
			$table->integer('updated_by')->nullable();
			$table->softDeletes();
			$table->integer('precedence');
		});
		
		return Redirect::action('InstanceController@index', $name);
	}
	
	# Edit object settings
	public function edit($object_name) {

		//get order by select data
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object->id)->orderBy('precedence')->get();
		$order_by = array();
		foreach ($fields as $field) $order_by[$field->name] = $field->title;
		$order_by = array(
			trans('avalon::messages.fields_system')=>array(
				'id'=>trans('avalon::messages.fields_id'),
				'precedence'=>trans('avalon::messages.fields_precedence'),
				'created_at'=>trans('avalon::messages.fields_updated_at'),
			),
			trans('avalon::messages.fields_user')=>$order_by,
		);

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
		return View::make('avalon::objects.edit', array(
			'object'			=>DB::table(DB_OBJECTS)->where('id', $object->id)->first(), 
			'order_by'			=>$order_by,
			'direction'			=>self::$direction,
			'dependencies'		=>DB::table(DB_FIELDS)->where('related_object_id', $object->id)->count(),
			'group_by_field'	=>array(''=>'') + DB::table(DB_FIELDS)->where('object_id', $object->id)->where('type', 'select')->lists('title', 'id'),
			'list_groupings'	=>self::getGroupings(),
			'related_objects'	=>$related_objects,
			'links'				=>$links,
		));
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

		//is a singleton instance?
		$singleton = Input::has('singleton') ? 1 : 0;

		//linked objects
		DB::table(DB_OBJECT_LINKS)->where('object_id', $object->id)->delete();
		if (Input::has('related_objects')) {
			foreach (Input::get('related_objects') as $linked_id) {
				DB::table(DB_OBJECT_LINKS)->insert(array(
					'object_id'=>$object->id,
					'linked_id'=>$linked_id,
				));
			}
		}

		//update objects table
		DB::table(DB_OBJECTS)->where('id', $object->id)->update(array(
			'title'				=>Input::get('title'),
			'name'				=>$new_name,
			'model'				=>Input::get('model'),
			'order_by'			=>$order_by,
			'direction'			=>$direction,
			'singleton'			=>$singleton,
			'list_grouping'		=>Input::get('list_grouping'),
			'group_by_field'	=>$group_by_field,
			'list_help'			=>trim(Input::get('list_help')),
			'form_help'			=>trim(Input::get('form_help')),
		));
		
		return Redirect::action('InstanceController@index', $new_name);
	}
	
	//destroy object
	public function destroy($object_name) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		Schema::dropIfExists($object->name);
		DB::table(DB_OBJECTS)->where('id', $object->id)->delete();
		DB::table(DB_FIELDS)->where('object_id', $object->id)->delete();
		DB::table(DB_OBJECT_LINKS)->where('object_id', $object->id)->orWhere('linked_id', $object->id)->delete();
		return Redirect::route('home');
	}

	//test
	public function tables() {
		
		//get all tables in database
		$tables = self::getTables();

		//remove the objects
		$tables = array_diff($tables, DB::table(DB_OBJECTS)->lists('name'));

		//remove the linking tables
		$tables = array_diff($tables, DB::table(DB_FIELDS)->where('type', 'checkboxes')->lists('name'));

		//remove the avalon system tables
		$tables = array_diff($tables, array(DB_FIELDS, DB_FILES, DB_OBJECT_LINKS, DB_OBJECT_USER, DB_OBJECTS, DB_USERS));

		//remove avalon paths
		$tables = array_diff($tables, array('create', 'logout'));
		
		//remove the laravel system tables
		$tables = array_diff($tables, array('migrations'));

		dd($tables);
	}

	//for list_grouping typeaheads
	private static function getGroupings() {
		$groupings = DB::table(DB_OBJECTS)->where('list_grouping', '<>', '')->distinct()->orderBy('list_grouping')->lists('list_grouping');
		foreach ($groupings as &$grouping) $grouping = '"' . str_replace('"', '', $grouping) . '"';
		return '[' . implode(',', $groupings) . ']';
	}

	//get array of tables in database
	private static function getTables() {
		$return = array();
		$pdo = DB::connection()->getPdo();
		$tables = $pdo->query('SHOW TABLES');
		foreach ($tables as $table) $return[] = $table[0];
		return $return;
	}

}
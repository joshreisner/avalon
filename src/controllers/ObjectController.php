<?php

class ObjectController extends \BaseController {

	private static $direction = array(
		'asc'=>'Ascending',
		'desc'=>'Descending',
	);
	
	//display list for home page
	public function index() {
		$objects = DB::table(Config::get('avalon::db_objects'))->orderBy('list_grouping')->orderBy('title')->get();
		foreach ($objects as &$object) {
			$object->link = URL::action('InstanceController@index', $object->id);
			if ($object->count == 0) $object->instance_count = '';
		}
		return View::make('avalon::objects.index', array('objects'=>$objects));
	}
	
	//display create object form
	public function create() {
		$order_by = array(Lang::get('avalon::messages.fields_system')=>array(
			'id'=>Lang::get('avalon::messages.fields_id'),
			'precedence'=>Lang::get('avalon::messages.fields_precedence'),
			'created_at'=>Lang::get('avalon::messages.fields_created_at'),
			'updated_at'=>Lang::get('avalon::messages.fields_updated_at'),
		));

		return View::make('avalon::objects.create', array(
			'order_by'	=>$order_by,
			'direction'	=>self::$direction,
			'list_groupings' =>self::getGroupings(),
		));
	}
	
	//store create object form post data
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
		$object_id = DB::table(Config::get('avalon::db_objects'))->insertGetId(array(
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
		DB::table(Config::get('avalon::db_fields'))->insert(array(
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
		
		return Redirect::action('InstanceController@index', $object_id);
	}
	
	# Edit object settings
	public function edit($object_id) {

		//get order by select data
		$fields = DB::table(Config::get('avalon::db_fields'))->where('object_id', $object_id)->orderBy('precedence')->get();
		$order_by = array();
		foreach ($fields as $field) $order_by[$field->name] = $field->title;
		$order_by = array(
			Lang::get('avalon::messages.fields_system')=>array(
				'id'=>Lang::get('avalon::messages.fields_id'),
				'precedence'=>Lang::get('avalon::messages.fields_precedence'),
				'created_at'=>Lang::get('avalon::messages.fields_updated_at'),
			),
			Lang::get('avalon::messages.fields_user')=>$order_by,
		);

		//related objects are different than dependencies; it's the subset of dependencies that are grouped by this object
		$related_objects = DB::table(Config::get('avalon::db_fields'))
			->join(Config::get('avalon::db_objects'), Config::get('avalon::db_objects') . '.group_by_field', '=', Config::get('avalon::db_fields') . '.id')
			->where(Config::get('avalon::db_fields') . '.related_object_id', $object_id)
			->orderBy(Config::get('avalon::db_objects') . '.title')
			->select(Config::get('avalon::db_objects') . '.*') //due to bug that leads to ambiguous column error
			->lists('title', 'id');

		//values for the related objects. could be combined with above
		$links = DB::table(Config::get('avalon::db_object_links'))->where('object_id', $object_id)->lists('linked_id');

		//return view
		return View::make('avalon::objects.edit', array(
			'object'			=>DB::table(Config::get('avalon::db_objects'))->where('id', $object_id)->first(), 
			'order_by'			=>$order_by,
			'direction'			=>self::$direction,
			'dependencies'		=>DB::table(Config::get('avalon::db_fields'))->where('related_object_id', $object_id)->count(),
			'group_by_field'	=>array(''=>'') + DB::table(Config::get('avalon::db_fields'))->where('object_id', $object_id)->where('type', 'select')->lists('title', 'id'),
			'list_groupings'	=>self::getGroupings(),
			'related_objects'	=>$related_objects,
			'links'				=>$links,
		));
	}
	
	//edit object settings
	public function update($object_id) {

		//rename table if necessary
		$object = DB::table(Config::get('avalon::db_objects'))->where('id', $object_id)->first();
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
		DB::table(Config::get('avalon::db_object_links'))->where('object_id', $object_id)->delete();
		if (Input::has('related_objects')) {
			foreach (Input::get('related_objects') as $linked_id) {
				DB::table(Config::get('avalon::db_object_links'))->insert(array(
					'object_id'=>$object_id,
					'linked_id'=>$linked_id,
				));
			}
		}

		//update objects table
		DB::table(Config::get('avalon::db_objects'))->where('id', $object_id)->update(array(
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
		
		return Redirect::action('InstanceController@index', $object_id);
	}
	
	//destroy object
	public function destroy($object_id) {
		Schema::dropIfExists(DB::table(Config::get('avalon::db_objects'))->where('id', $object_id)->pluck('name'));
		DB::table(Config::get('avalon::db_objects'))->where('id', $object_id)->delete();
		DB::table(Config::get('avalon::db_fields'))->where('object_id', $object_id)->delete();
		DB::table(Config::get('avalon::db_object_links'))->where('object_id', $object_id)->orWhere('linked_id', $object_id)->delete();
		return Redirect::action('ObjectController@index');
	}

	//for list_grouping typeaheads
	private static function getGroupings() {
		$groupings = DB::table(Config::get('avalon::db_objects'))->where('list_grouping', '<>', '')->distinct()->orderBy('list_grouping')->lists('list_grouping');
		foreach ($groupings as &$grouping) $grouping = '"' . str_replace('"', '', $grouping) . '"';
		return '[' . implode(',', $groupings) . ']';
	}

}
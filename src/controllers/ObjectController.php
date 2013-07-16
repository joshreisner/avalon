<?php

class ObjectController extends \BaseController {

	private static $direction = array(
		'asc'=>'Ascending',
		'desc'=>'Descending',
	);
	
	//display list for home page
	public function index() {
		$objects = DB::table('avalon_objects')->orderBy('list_grouping')->orderBy('title')->get();
		foreach ($objects as &$object) {
			$object->link = URL::action('InstanceController@index', $object->id);
			if ($object->instance_count == 0) $object->instance_count = '';
		}
		return View::make('avalon::objects.index', array('objects'=>$objects));
	}
	
	//display create object form
	public function create() {
		$order_by = array(
			'id'=>Lang::get('avalon::messages.fields_id'),
			'precedence'=>Lang::get('avalon::messages.fields_precedence'),
			'created_at'=>Lang::get('avalon::messages.fields_updated_at'),
		);

		//typehead
		$typeahead = DB::table('avalon_objects')->select('list_grouping')->distinct()->orderBy('list_grouping')->get();
		foreach ($typeahead as &$list_grouping) $list_grouping = '"' . $list_grouping->list_grouping . '"';
		$typeahead = '[' . implode(',', $typeahead) . ']';

		return View::make('avalon::objects.create', array(
			'order_by'	=>$order_by,
			'direction'	=>self::$direction,
			'typeahead'	=>$typeahead,
		));
	}
	
	//store create object form post data
	public function store() {

		//make plural, title case
		$title		= mb_convert_case(Str::plural(Input::get('title')), MB_CASE_TITLE, 'UTF-8');

		//determine table name, todo check if unique
		$name		= Str::slug($title, '_');
		
		//enforce predence always ascending
		$order_by	= Input::get('order_by');
		$direction 	= Input::get('direction');
		if ($order_by == 'precedence') $direction = 'asc';

		//create entry in objects table for new object
		$object_id = DB::table('avalon_objects')->insertGetId(array(
			'title'			=>$title,
			'name'			=>$name,
			'order_by'		=>$order_by,
			'direction'		=>$direction,
			'list_grouping'	=>Input::get('list_grouping'),
			'updated_by'	=>Session::get('avalon_id'),
			'updated_at'	=>new DateTime,
		));
		
		//create title field for table by default
		DB::table('avalon_fields')->insert(array(
			'title'			=>'Title',
			'name'			=>'title',
			'type'			=>'string',
			'visibility'	=>'list',
			'required'		=>1,
			'object_id'		=>$object_id,
			'updated_by'	=>Session::get('avalon_id'),
			'updated_at'	=>new DateTime,
			'precedence'	=>1
		));
		
		//create table with boilerplate fields
		Schema::create($name, function($table){
			$table->increments('id');
			$table->string('title');
			$table->integer('updated_by')->nullable();
			$table->dateTime('updated_at');
			$table->integer('precedence');
			$table->integer('subsequence')->nullable();
			$table->boolean('active')->default(1);
		});
		
		return Redirect::action('InstanceController@index', $object_id);
	}
	
	//edit object settings
	public function edit($object_id) {

		//get order by select data
		$fields = DB::table('avalon_fields')->where('object_id', $object_id)->orderBy('precedence')->get();
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

		//get typeahead info
		//$typeahead = '["foo","bar", "baz"]';
		$typeahead = DB::table('avalon_objects')->select('list_grouping')->distinct()->orderBy('list_grouping')->get();
		foreach ($typeahead as &$list_grouping) $list_grouping = '"' . $list_grouping->list_grouping . '"';
		$typeahead = '[' . implode(',', $typeahead) . ']';

		return View::make('avalon::objects.edit', array(
			'object'=>DB::table('avalon_objects')->where('id', $object_id)->first(), 
			'order_by'=>$order_by,
			'direction'=>self::$direction,
			'dependencies'=>DB::table('avalon_fields')->where('related_object_id', $object_id)->count(),
			'group_by_field'=>DB::table('avalon_fields')->where('object_id', $object_id)->where('type', 'select')->get(),
			'typeahead'=>$typeahead,
		));
	}
	
	//edit object settings
	public function update($object_id) {
		//make plural, title case
		$title		= mb_convert_case(Str::plural(Input::get('title')), MB_CASE_TITLE, 'UTF-8');

		//rename table if necessary
		$old_name = DB::table('avalon_objects')->where('id', $object_id)->pluck('name');
		$new_name = Str::slug(Input::get('name'), '_');
		if ($old_name != $new_name) Schema::rename($old_name, $new_name);
		
		//enforce predence always ascending
		$order_by = Input::get('order_by');
		$direction = Input::get('direction');
		if ($order_by == 'precedence') $direction = 'asc';

		//update objects table
		DB::table('avalon_objects')->where('id', $object_id)->update(array(
			'title'				=>$title,
			'name'				=>$new_name,
			'order_by'			=>$order_by,
			'direction'			=>$direction,
			'list_grouping'		=>Input::get('list_grouping'),
			'group_by_field'	=>Input::get('group_by_field'),
			'list_help'			=>trim(Input::get('list_help')),
			'form_help'			=>trim(Input::get('form_help')),
		));
		
		return Redirect::action('InstanceController@index', $object_id);
	}
	
	//destroy object
	public function destroy($object_id) {
		Schema::dropIfExists(DB::table('avalon_objects')->where('id', $object_id)->pluck('name'));
		DB::table('avalon_objects')->where('id', $object_id)->delete();
		DB::table('avalon_fields')->where('object_id', $object_id)->delete();
		DB::table('avalon_object_links')->where('object_id', $object_id)->orWhere('linked_id', $object_id)->delete();
		return Redirect::action('ObjectController@index');
	}
	
}
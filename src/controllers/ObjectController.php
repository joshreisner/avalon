<?php

class ObjectController extends \BaseController {

	//display list for home page
	public function index() {
		$objects = DB::table('avalon_objects')->orderby('title')->get();
		foreach ($objects as &$object) {
			if (!empty($object->instance_updated_at)) $object->instance_updated_at = \Carbon\Carbon::createFromTimeStamp(strtotime($object->instance_updated_at))->diffForHumans();
			if ($object->instance_count == 0) $object->instance_count = '';
		}
		return View::make('avalon::objects.index', array('objects'=>$objects));
	}
	
	//display create object form
	public function create() {
		return View::make('avalon::objects.create');
	}
	
	//store create object form post data
	public function store() {
		
		//determine table name, todo check if unique
		$name = Str::slug(Input::get('title'), '_');
		
		//create entry in objects table for new object
		$object_id = DB::table('avalon_objects')->insertGetId(array(
			'title'=>Input::get('title'),
			'name'=>$name,
			'updated_by'=>Session::get('avalon_id'),
			'updated_at'=>new DateTime,
		));
		
		//create title field for table by default
		DB::table('avalon_fields')->insert(array(
			'title'=>'Title',
			'name'=>'title',
			'type'=>'string',
			'visibility'=>'list',
			'required'=>1,
			'object_id'=>$object_id,
			'updated_by'=>Session::get('avalon_id'),
			'updated_at'=>new DateTime,
			'precedence'=>1
		));
		
		//create table with boilerplate fields
		Schema::create($name, function($table){
			$table->increments('id');
			$table->string('title');
			$table->integer('updated_by')->nullable();
			$table->dateTime('updated_at');
			//$table->boolean('published')->default(1);
			$table->integer('precedence');
			$table->integer('subsequence')->nullable();
			$table->boolean('active')->default(1);
		});
		
		return Redirect::action('ObjectController@show', $object_id);
	}

	//show list of instances for an object
	public function show($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		$fields = DB::table('avalon_fields')->where('object_id', $object_id)->where('visibility', 'list')->orderBy('precedence')->get();
		$instances = DB::table($object->name)->orderBy($object->order_by, $object->direction)->get(); //todo select only $fields
		
		foreach ($instances as &$instance) {
			$instance->updated_at = \Carbon\Carbon::createFromTimeStamp(strtotime($instance->updated_at))->diffForHumans();
		}
		
		return View::make('avalon::objects.show', array(
			'object'=>$object, 
			'fields'=>$fields, 
			'instances'=>$instances
		));
	}
	
	//edit object settings
	public function edit($object_id) {
		$object = DB::table('avalon_objects')->where('id', $object_id)->first();
		return View::make('avalon::objects.edit', array(
			'object'=>$object, 
		));
	}
	
	//edit object settings
	public function update($object_id) {
		//rename table if necessary
		$old_name = DB::table('avalon_objects')->where('id', $object_id)->pluck('name');
		$new_name = Str::slug(Input::get('name'), '_');
		if ($old_name != $new_name) Schema::rename($old_name, $new_name);
		
		//update objects table
		DB::table('avalon_objects')->where('id', $object_id)->update(array(
			'title'=>Input::get('title'),
			'name'=>$new_name,
			'list_help'=>trim(Input::get('list_help')),
			'form_help'=>trim(Input::get('form_help')),
		));
		
		return Redirect::action('ObjectController@show', $object_id);
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
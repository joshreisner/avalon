<?php

class ObjectController extends \BaseController {

	//display list for home page
	public function get_index() {
		$objects = DB::table('avalon_objects')->orderby('title')->get();
		foreach ($objects as &$object) {
			$object->instance_updated_at = \Carbon\Carbon::createFromTimeStamp(strtotime($object->instance_updated_at))->diffForHumans();
		}
		return View::make('avalon::objects.index', array('objects'=>$objects));
	}
	
	//display create object form
	public function get_create() {
		return View::make('avalon::objects.create');
	}
	
	//store create object form post data
	public function post_store() {
		
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
			$table->timestamp('updated_at');
			$table->boolean('active')->default(1);
			$table->boolean('published')->default(1);
			$table->integer('precedence');
			$table->integer('subsequence')->nullable();
		});
		
		return Redirect::to('/login/objects/' . $object_id, 303);
	}
	
}
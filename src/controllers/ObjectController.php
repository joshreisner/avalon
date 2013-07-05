<?php

class ObjectController extends \BaseController {

	public function get_index() {
		$objects = DB::table('avalon_objects')->select('id', 'title')->orderby('title')->get();
		return View::make('avalon::objects.index')->with('objects', $objects);
	}
	
	public function get_create() {
		return View::make('avalon::objects.create');
	}
	
	public function post_store() {
		
		//determine table name, todo check if unique
		$table_name = Str::slug(Input::get('title'), '_');
		
		//create entry in objects table for new object
		$object_id = DB::table('avalon_objects')->insertGetId(array(
			'title'=>Input::get('title'),
			'table_name'=>$table_name,
			'updated_by'=>$_SESSION['avalon_id'],
			'updated_at'=>new DateTime,
		));
		
		//create title field for table by default
		DB::table('avalon_fields')->insert(array(
			'title'=>'Title',
			'field_name'=>'title',
			'type'=>'string',
			'visibility'=>'list',
			'required'=>1,
			'object_id'=>$object_id,
			'updated_by'=>$_SESSION['avalon_id'],
			'updated_at'=>new DateTime,
			'precedence'=>1
		));
		
		//create table with boilerplate fields
		Schema::create($table_name, function($table){
			$table->increments('id');
			$table->string('title');
			$table->integer('updated_by')->nullable();
			$table->timestamp('updated_at');
			$table->boolean('active');
			$table->boolean('published');
			$table->integer('precedence');
			$table->integer('subsequence')->nullable();
		});
		
		return Redirect::to('/login/objects/' . $object_id, 303);
	}
	
}
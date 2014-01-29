<?php

use Illuminate\Database\Migrations\Migration;

class AvalonCreateTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('avalon', function($table){
			$table->increments('id');
			$table->string('title');
			$table->string('css')->nullable();
		});
		
		Schema::create('avalon_fields', function($table){
			$table->increments('id');
			$table->integer('object_id');
			$table->string('type');
			$table->string('title');
			$table->string('name');
			$table->string('visibility')->default('normal');
			$table->boolean('required')->default(0);
			$table->integer('related_field_id')->nullable();
			$table->integer('related_object_id')->nullable();
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
			$table->string('help')->nullable();
			$table->dateTime('updated_at');
			$table->integer('updated_by');
			$table->integer('precedence');
		});
		
		Schema::create('avalon_object_links', function($table){
			$table->integer('object_id');
			$table->integer('linked_id');
		});
		
		Schema::create('avalon_object_user', function($table){
			$table->integer('object_id');
			$table->integer('user_id');
		});

		Schema::create('avalon_objects', function($table){
			$table->increments('id');
			$table->string('title');
			$table->string('name');
			$table->string('model');
			$table->string('order_by');
			$table->string('direction');
			$table->integer('group_by_field')->nullable();
			$table->text('list_help')->nullable();
			$table->text('form_help')->nullable();
			$table->string('web_page')->nullable();
			$table->string('list_grouping')->nullable();
			$table->integer('count')->default(0);
			$table->dateTime('updated_at');
			$table->integer('updated_by');
		});
		
		Schema::create('avalon_users', function($table){
			$table->increments('id');
			$table->string('firstname');
			$table->string('lastname');
			$table->string('email')->unique();
			$table->string('password');
			$table->integer('role')->nullable();
			$table->string('token')->nullable(); //for password resets
			$table->dateTime('last_login')->nullable();
			$table->dateTime('updated_at');
			$table->integer('updated_by')->nullable(); //for first user
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//clear out user-defined object tables
		$objects = DB::table('avalon_objects')->get();
		foreach ($objects as $object) Schema::dropIfExists($object->name);
		
		Schema::dropIfExists('avalon');
		Schema::dropIfExists('avalon_fields');
		Schema::dropIfExists('avalon_object_links');
		Schema::dropIfExists('avalon_object_user');
		Schema::dropIfExists('avalon_objects');
		Schema::dropIfExists('avalon_users');
	}

}
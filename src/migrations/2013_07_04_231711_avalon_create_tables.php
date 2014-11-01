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
		Schema::create(DB_FIELDS, function($table){
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
		
		Schema::create(DB_FILES, function($table){
			$table->increments('id');
			$table->integer('field_id');
			$table->integer('instance_id')->nullable();
			$table->string('host')->nullable();
			$table->string('path')->nullable();
			$table->string('name');
			$table->string('extension', 8);
			$table->string('url');
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
			$table->integer('size');
			$table->boolean('writable')->default(0);
			$table->dateTime('updated_at');
			$table->integer('updated_by');
			$table->integer('precedence');
		});

		Schema::create(DB_OBJECT_LINKS, function($table){
			$table->integer('object_id');
			$table->integer('linked_id');
		});
		
		Schema::create(DB_OBJECT_USER, function($table){
			$table->integer('object_id');
			$table->integer('user_id');
		});

		Schema::create(DB_OBJECTS, function($table){
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
			$table->boolean('singleton')->default(0);
			$table->dateTime('updated_at');
			$table->integer('updated_by');
		});
		
		Schema::create(DB_USERS, function($table){
			$table->increments('id');
			$table->string('firstname');
			$table->string('lastname');
			$table->string('email')->unique();
			$table->string('password');
			$table->integer('role')->nullable();
			$table->string('token')->nullable(); //for password resets
			$table->string('remember_token', 100)->nullable();
			$table->dateTime('last_login')->nullable();
			$table->dateTime('created_at');
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
		Schema::dropIfExists(DB_FIELDS);
		Schema::dropIfExists(DB_FILES);
		Schema::dropIfExists(DB_OBJECT_LINKS);
		Schema::dropIfExists(DB_OBJECT_USER);
		Schema::dropIfExists(DB_OBJECTS);
		Schema::dropIfExists(DB_USERS);
	}

}
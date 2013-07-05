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
			$table->string('link_color')->default('#336699');
			$table->string('banner_image')->default('/packages/joshreisner/avalon/img/banner.png');
			$table->integer('updated_by');
			$table->timestamp('updated_at');
		});
		
		Schema::create('avalon_fields', function($table){
			$table->increments('id');
			$table->integer('object_id');
			$table->string('type');
			$table->string('title');
			$table->string('field_name');
			$table->integer('related_field_id')->nullable();
			$table->integer('related_object_id')->nullable();
			$table->string('visibility')->default('normal');
			$table->boolean('required')->default(0);
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
			$table->string('help')->nullable();
			$table->integer('updated_by');
			$table->integer('precedence');
			$table->timestamp('updated_at');
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
			$table->string('table_name');
			$table->string('order_by')->default('title');
			$table->string('direction')->default('ASC');
			$table->integer('group_by_field')->nullable();
			$table->text('list_help')->nullable();
			$table->text('form_help')->nullable();
			$table->boolean('show_published')->default(0);
			$table->string('web_page')->nullable();
			$table->string('list_grouping')->nullable();
			$table->integer('updated_by');
			$table->timestamp('updated_at');
		});
		
		Schema::create('avalon_users', function($table){
			$table->increments('id');
			$table->string('firstname');
			$table->string('lastname');
			$table->string('email')->unique();
			$table->string('password');
			$table->string('token')->unique();
			$table->integer('role')->default(3);
			$table->boolean('active')->default(1);
			$table->timestamp('last_login');
			$table->integer('updated_by');
			$table->timestamp('updated_at');
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('avalon');
		Schema::dropIfExists('avalon_fields');
		Schema::dropIfExists('avalon_object_links');
		Schema::dropIfExists('avalon_object_user');
		Schema::dropIfExists('avalon_objects');
		Schema::dropIfExists('avalon_users');
	}

}
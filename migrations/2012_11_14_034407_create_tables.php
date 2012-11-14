<?php

class Avalon_Create_Tables {

	public function up()
	{
		Schema::create('avalon', function($table)
		{
			$table->string('link_color', 7);
			$table->blob('banner_image');
			$table->integer('created_by')/*->references('id')->on('avalon_users')*/;
			$table->integer('updated_by')/*->references('id')->on('avalon_users')*/->nullable();
		    $table->timestamps();
		});

		Schema::create('avalon_fields', function($table)
		{
			$table->increments('id');
			$table->integer('object_id')/*->references('id')->on('avalon_objects')*/;
			$table->string('type');
			$table->string('title');
			$table->string('field_name');
			$table->string('visibility');
			$table->boolean('required');
			$table->boolean('translated');
			$table->integer('related_field_id')/*->references('id')->on('avalon_fields')*/->nullable();
			$table->integer('related_object_id')/*->references('id')->on('avalon_objects')*/->nullable();
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
			$table->text('additional')->nullable();
			$table->integer('created_by')/*->references('id')->on('avalon_users')*/;
			$table->integer('updated_by')/*->references('id')->on('avalon_users')*/->nullable();
			$table->boolean('active');
			$table->integer('precedence');
		    $table->timestamps();
		});

		Schema::create('avalon_languages', function($table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('code', 2);
			$table->boolean('checked');
		});

		Schema::create('avalon_objects', function($table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('table_name')->unique();
			$table->string('order_by')->default('created_at');
			$table->string('direction')->default('DESC');
			$table->integer('group_by_field')/*->references('id')->on('avalon_fields')*/;
			$table->text('list_help')->nullable();
			$table->text('form_help')->nullable();
			$table->boolean('show_published');
			$table->string('web_page');
			$table->string('list_grouping');
			$table->integer('created_by')/*->references('id')->on('avalon_users')*/;
			$table->integer('updated_by')/*->references('id')->on('avalon_users')*/->nullable();
			$table->boolean('active');
		    $table->timestamps();
		});

		Schema::create('avalon_objects_links', function($table)
		{
			$table->integer('object_id')/*->references('id')->on('avalon_objects')*/;
			$table->integer('linked_id')/*->references('id')->on('avalon_objects')*/;
		});

		Schema::create('avalon_users', function($table)
		{
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password');
			$table->string('firstname');
			$table->string('lastname');
			$table->integer('role');
			$table->timestamp('last_login')->nullable();
			$table->integer('created_by')/*->references('id')->on('avalon_users')*/;
			$table->integer('updated_by')/*->references('id')->on('avalon_users')*/->nullable();
			$table->boolean('active');
		    $table->timestamps();
		});

		Schema::create('avalon_users_to_objects', function($table)
		{
			$table->integer('user_id')/*->references('id')->on('avalon_users')*/;
			$table->integer('object_id')/*->references('id')->on('avalon_objects')*/;
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('avalon');
		Schema::drop('avalon_fields');
		Schema::drop('avalon_languages');
		Schema::drop('avalon_objects');
		Schema::drop('avalon_objects_links');
		Schema::drop('avalon_users');
		Schema::drop('avalon_users_to_objects');
	}

}
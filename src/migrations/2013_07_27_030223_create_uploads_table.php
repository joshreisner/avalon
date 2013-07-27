<?php

use Illuminate\Database\Migrations\Migration;

class CreateUploadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('avalon_uploads', function($table){
			$table->increments('id');
			$table->string('table');
			$table->integer('instance_id');
			$table->string('title');
			$table->string('url');
			$table->string('extension', 6);
			$table->bigInteger('size')->nullable();
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
			$table->integer('updated_by');
			$table->dateTime('updated_at');
			$table->integer('precedence');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('avalon_uploads');
	}

}
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ObjectPermissions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table(DB_OBJECTS, function($table){
			$table->boolean('can_create')->default(1);
			$table->boolean('can_edit')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table(DB_OBJECTS, function($table){
			$table->dropColumn('can_create');
			$table->dropColumn('can_edit');
		});
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ObjectsUrl extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table(DB_OBJECTS, function($table){
			$table->string('url');
		});

		DB::table(DB_FIELDS)->where('name', 'slug')->delete();

		//add slug column to all tables that don't have it
		$objects = DB::table(DB_OBJECTS)->get();
		foreach ($objects as $object) {

			//add slug column
			if (Schema::hasColumn($object->name, 'slug')) {
				DB::table($object->name)->whereNull('slug')->update(['slug'=>'']);
				DB::update('ALTER TABLE `' . $object->name . '` MODIFY `slug` VARCHAR(255) NOT NULL');
				Schema::table($object->name, function($table){
					//$table->unique('slug');
				});
			} else {
				Schema::table($object->name, function($table){
					$table->string('slug');//->unique();
				});

				//set slug values
				Slug::setForObject($object);
			}

			//add created_by column, not sure why this wasn't added earlier
			if (!Schema::hasColumn($object->name, 'created_by')) {
				Schema::table($object->name, function($table){
					$table->integer('created_by');
				});
			}
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table(DB_OBJECTS, function($table){
			$table->dropColumn('url');
		});

		//don't know how to restore slug to tables that once had it
		$objects = DB::table(DB_OBJECTS)->whereNotIn('name', ['about', 'courses', 'events', 'posts', 'publications'])->get();
		foreach ($objects as $object) {
			if (Schema::hasColumn($object->name, 'slug')) {
				Schema::table($object->name, function($table){
					$table->dropColumn('slug');
				});
			}
			if (Schema::hasColumn($object->name, 'created_by')) {
				Schema::table($object->name, function($table){
					$table->dropColumn('created_by');
				});
			}
		}
	}

}

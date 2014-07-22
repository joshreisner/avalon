<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserName extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table){
			$table->string('name');
		});

		$users = User::get();
		foreach ($users as $user) {
			$user->name = trim($user->firstname . ' ' . $user->lastname);
			$user->save();
		}

		Schema::table('users', function($table){
			$table->dropColumn('firstname');
			$table->dropColumn('lastname');
		});

	}

	/**
	 * Reverse the migrations.
	 * I'm not going to split the names and repopulate...
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table){
			$table->string('firstname');
			$table->string('lastname');
			$table->dropColumn('name');
		});

	}

}

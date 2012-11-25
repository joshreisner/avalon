<?php
class Avalon_Test_Controller extends Controller {
	
	public function action_index() {
		//run arbitrary code here

		Schema::table('foobar', function($table) {
		    $table->create();
		    $table->increments('id');
		    $table->string('username');
		    $table->string('email');
		    $table->string('phone')->nullable();
		    $table->text('about');
		    $table->timestamps();
		});
		echo 'foobar created!';


	}

	
}
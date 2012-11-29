<?php
class Avalon_Test_Controller extends Controller {
	
	public function action_index() {
		//run arbitrary code here

		echo 'date is ' . date(DATE_RFC822);
	}

	
}
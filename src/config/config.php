<?php

return array(
	
	//specify a local stylesheet
	//'css'					=> '/assets/css/avalon.css', 

	//prefix all table names
	'db_prefix'				=> 'avalon_',

	//specify image display size in the instance.create and .edit views
	'image_default_width'	=> 220,
	'image_default_height'	=> 100,
	'image_max_width'		=> 701,
	'image_max_height'		=> 240,
	'image_max_area'		=> 168240, //701 * 240 = 168240

	//specifies the path to find the cms at, eg http://example.com/login
	'route_prefix'			=> 'login',

);
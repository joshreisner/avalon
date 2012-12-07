<?php

Autoloader::map(array(
    'Avalon\\Field'		=> Bundle::path('avalon') . 'models/field.php',
    'Avalon\\Object'	=> Bundle::path('avalon') . 'models/object.php',
    'Avalon\\Settings'	=> Bundle::path('avalon') . 'models/settings.php',
    'Avalon\\User'		=> Bundle::path('avalon') . 'models/user.php',

    'Avalon\\Date'		=> Bundle::path('avalon') . 'libraries/date.php',
));

//assets
Asset::container('avalon')->bundle('avalon');
Asset::container('avalon')->add('jquery', 'vendor/jquery-1.8.3.min.js');
Asset::container('avalon')->add('jquery_valdate', 'vendor/jquery.validate.min.js');
Asset::container('avalon')->add('bootstrap_css', 'vendor/bootstrap/css/bootstrap.min.css');

if (Auth::check()) {
	//todo: only include assets on pages when they're needed?
	Asset::container('avalon')->add('avalon_page_css', 'css/page.css');
	Asset::container('avalon')->add('avalon_page_js', 'js/page.js');
	Asset::container('avalon')->add('color_js', 'vendor/jscolor/jscolor.js');
	Asset::container('avalon')->add('lorem_ipsum_js', 'vendor/lorem_ipsum.js');
	Asset::container('avalon')->add('table_dnd_js', 'vendor/jquery.table-multi-dnd.js');
	Asset::container('avalon')->add('fontawesome_css', 'vendor/fontawesome/css/font-awesome.css');
	Asset::container('avalon')->add('bootstrap_js', 'vendor/bootstrap/js/bootstrap.min.js');
	Asset::container('avalon')->add('redactor_css', 'vendor/redactor/redactor.css');
	Asset::container('avalon')->add('redactor_js', 'vendor/redactor/redactor.min.js');
} else {
	Asset::container('avalon')->add('avalon_login_css', 'css/login.css');
	Asset::container('avalon')->add('avalon_login_js', 'js/login.js');
}
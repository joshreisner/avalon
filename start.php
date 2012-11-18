<?php

Autoloader::namespaces(array(
    'Avalon' => Bundle::path('avalon') . 'models',
));

Auth::extend('auth', function() {

});

//assets
Asset::container('avalon')->bundle('avalon');
Asset::container('avalon')->add('jquery', 'vendor/jquery-1.8.3.min.js');
Asset::container('avalon')->add('jquery_valdate', 'vendor/jquery.validate.min.js');
Asset::container('avalon')->add('bootstrap_css', 'vendor/bootstrap/css/bootstrap.min.css');

if (Auth::check()) {
	Asset::container('avalon')->add('avalon_page_css', 'css/page.css');
	Asset::container('avalon')->add('avalon_page_js', 'js/page.js');
	Asset::container('avalon')->add('avalon_colorjs', 'vendor/jscolor/jscolor.js');
	Asset::container('avalon')->add('fontawesome_css', 'vendor/fontawesome/css/font-awesome.css');
	Asset::container('avalon')->add('bootstrap_js', 'vendor/bootstrap/js/bootstrap.min.js');
} else {
	Asset::container('avalon')->add('avalon_login_css', 'css/login.css');
	Asset::container('avalon')->add('avalon_login_js', 'js/login.js');
}



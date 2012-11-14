<?php

Autoloader::namespaces(array(
    'Avalon' => Bundle::path('avalon') . 'models',
));

Asset::container('avalon')->bundle('avalon');
Asset::container('avalon')->add('bootstrap_css', 'bootstrap/css/bootstrap.min.css');
Asset::container('avalon')->add('bootstrap_js', 'bootstrap/js/bootstrap.min.js');
Asset::container('avalon')->add('fontawesome_css', 'fontawesome/css/fontawesome.css');
Asset::container('avalon')->add('avalon_login_css', 'css/login.css');
//Asset::container('avalon')->add('avalon_js', 'js/avalon.js');

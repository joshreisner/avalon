Welcome to Avalon
======

This package is *pre-beta* meaning you should really wait to try it out unless you're feeling very very adventuresome.

Eventually this will become a Laravel 4 CMS gui package and will have a video and screenshots.

Install Instructions:

* add "joshreisner/avalon": "*" to composer.json
* add 'Joshreisner\Avalon\AvalonServiceProvider', to the $providers array in config/app.php
* make sure you have your database connected (only mysql tested)
* php artisan migrate --package=joshreisner/avalon
* php artisan asset:publish


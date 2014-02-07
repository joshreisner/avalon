Welcome to Avalon
======

This package is *pre-beta* meaning you should really wait to try it out unless you're feeling very very adventuresome.

Eventually this will become a Laravel 4 CMS gui package and will have a video and screenshots.

Install Instructions:

* make sure you have your database connected (only mysql tested)
* set up email (for new users and password resets)
* add 'Joshreisner\Avalon\AvalonServiceProvider', to the $providers array in config/app.php
$ $ composer require "joshreisner/avalon:*"
* $ php artisan config:publish joshreisner/avalon
* $ php artisan migrate --package=joshreisner/avalon
* $ php artisan asset:publish


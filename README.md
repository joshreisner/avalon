Welcome to Avalon
======

This package is *pre-beta* meaning you should really wait to try it out unless you're feeling very very adventuresome.

Eventually this will become a Laravel 4 CMS gui package and will have a video and screenshots.

Install Instructions:

* set up database (only mysql tested)
* set up email (for user invites and password resets)
* $ composer require joshreisner/avalon:dev-master
* add 'Joshreisner\Avalon\AvalonServiceProvider', to providers in config/app.php
* $ php artisan config:publish joshreisner/avalon
* $ php artisan migrate --package=joshreisner/avalon
* $ php artisan asset:publish


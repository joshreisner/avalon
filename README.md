Avalon
=============

Eventually, this will become a CMS bundle for Laravel similar to [login](https://github.com/joshreisner/login "login").  But right now
it's just a thicket of code.  So don't try to use it yet, is what I'm saying.

So far, the only configuration you have to do besides the usual bundle install/publish stuff is

* run the migration
* set your auth model setting to Avalon\User
* add the following line to your application/bundles.php
    'avalon' => array('handles' => 'login', 'auto' => true),
* install the following bundles
> 1. swiftmailer
<?php
namespace Avalon;

use \Eloquent;

class User extends Eloquent {

     public static $table		= 'avalon_users';
     public static $timestamps	= true;

     public function objects() {
          return $this->has_many_and_belongs_to('\Avalon\Object', 'avalon_users_to_objects');
     }
     
}


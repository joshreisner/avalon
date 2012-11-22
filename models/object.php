<?php
namespace Avalon;

use \Eloquent;

class Object extends Eloquent {

     public static $table		= 'avalon_objects';
     public static $timestamps	= true;

     public function fields() {
          return $this->has_many('Avalon\\Field');
     }
     
}


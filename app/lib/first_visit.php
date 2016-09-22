<?php

namespace indagare\cookies;

if ( ! class_exists( 'indagare\cookies\FirstVisit' ) ) {
	class FirstVisit {
    static public $value = null;
    
		static function isFirstVisit() {
      if( is_null( self::$value ) ) {
				$c = new \indagare\cookies\CookieDough( 'first_visit' );
        self::$value = $c->is_set();
        $c->set( "1", time() + 60*60*24*365*10, '/' );
      }

			return self::$value;
		}
	}
}
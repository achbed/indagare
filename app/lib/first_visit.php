<?php
/*
namespace indagare\cookies;

if ( ! class_exists( 'indagare\cookies\FirstVisit' ) ) {

	class FirstVisit {

		public static $value = null;

		public static $ttl = 315360000; // Ten years (about)

		public static $path = '/';

		static function isFirstVisit() {
			if ( is_null( self::$value ) ) {
				$c = new \indagare\cookies\CookieDough( 'first_visit' );
				$v = $c->get();
				self::$value = ( intval( $v ) == 1 );
				$c->set( "1", time() + self::$ttl, self::$path );
			}

			return self::$value;
		}
	}
}
*/
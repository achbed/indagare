<?php
/*
namespace indagare\cookies;

if ( ! class_exists( '\indagare\cookies\Counter' ) ) {

	class Counter extends CookieDough {

		public static $ttl = 86400;

		public static function updateCounter() {
			$i = self::get_instance( 'pagecount' );
			$x = 0;
			if ( ! empty( self::$ttl ) ) {
				$x = time() + $ttl;
				}
			$v = $i->inc( $x );
			return ( $v > 10 );
				}
	}
			}

*/
<?php

namespace indagare\cookies;

if ( ! class_exists( 'indagare\cookies\Counter' ) ) {

	class Counter {

		public static $value = null;

		public static $ttl = 86400;  // 24 hours

		public static $path = '/';

		static function updateCounter() {
			if ( is_null( self::$value ) ) {
				$c = new \indagare\cookies\CookieDough( 'pagecount' );

				self::$value = $c->get();
				if ( empty( self::$value ) ) {
					self::$value = 0;
				}

				if ( self::$value > 10 ) {
					return false;
				}

				self::$value++;
				$c->set( self::$value, time() + self::$ttl, self::$path );
			}

			return true;
		}
	}
}

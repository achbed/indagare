<?php

namespace indagare\cookies;

if ( ! class_exists( 'indagare\cookies\PageCountAll' ) ) {

	class PageCountAll {

		public static $value = null;

		public static $ttl = 604800; // 1 week

		public static $path = '/';

		static function getPageCountAll() {
			if ( is_null( self::$value ) ) {
				$c = new \indagare\cookies\CookieDough( 'pagecountall' );

				self::$value = $c->get();
				if ( empty( self::$value ) ) {
					self::$value = -1;
				}

				self::$value++;
				$c->set( self::$value, time() + self::$ttl, self::$path );
			}

			return self::$value;
		}
	}
}

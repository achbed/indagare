<?php

namespace indagare\cookies;

if ( ! class_exists( '\indagare\cookies\Counters' ) ) {

	class Counters {
		public static $ttl = 86400;

		private static $instances = array();

		public static function get_instance( $key ) {
			if ( ! array_key_exists( $key, self::$instances ) ) {
				self::$instances[$key] = new CookieDough( $key );
			}
			return self::$instances[$key];
		}

		/**
		 * Increment and return the page count.  Only increments once per PHP execution cycle (page load)
		 * @return mixed|number
		 */
		public static function getPageCountAll( $ttl = null ) {
			if ( is_null( $ttl ) ) {
				$ttl = self::$ttl;
			}

			$i = self::get_instance( 'pagecountall' );
			$v = $i->get();
			if ( ! empty( $i->counted ) ) {
				return $v;
			}
			$x = 0;
			if ( ! empty( $ttl ) ) {
				$x = time() + $ttl;
			}
			$v = $i->inc( $x );
			$i->counted = true;
			return $v;
		}

		/**
		 * Increment and return the page count.  Only increments once per PHP execution cycle (page load)
		 * @return mixed|number
		 */
		public static function getPageCountGroup( $g, $ttl = null ) {
			if ( is_null( $ttl ) ) {
				$ttl = self::$ttl;
				}

			$i = self::get_instance( 'pagecount_' . $g );
			$v = $i->get();
			if ( ! empty( $i->counted ) ) {
				return $v;
			}
			$x = 0;
			if ( ! empty( $ttl ) ) {
				$x = time() + $ttl;
			}
			$v = $i->inc( $x );
			$i->counted = true;
			return $v;
		}

	}
}

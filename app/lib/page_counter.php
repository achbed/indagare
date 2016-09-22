<?php

namespace indagare\cookies;

if ( ! class_exists( 'indagare\cookies\PageCountAll' ) ) {
	class PageCountAll {
		static public $instance = null;

		static public $counted = false;

		static function getPageCountAll() {
			if ( empty( self::$instance ) ) {
				self::$instance = new \indagare\cookies\CookieDough( 'pagecountall' );
			}

			$c = self::$instance->get();
			if ( empty( $c ) ) {
				$c = -1;
			}

			if ( ! self::$counted ) {
				self::$instance->set( $c++, time()+604800, '/' );
				self::$counted = true;
			}

			return $c;
		}
	}
}

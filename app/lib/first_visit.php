<?php

namespace indagare\cookies;

if ( ! class_exists( 'indagare\cookies\FirstVisit' ) ) {
	class FirstVisit {
		static public $instance = null;

		static function isFirstVisit() {
			if ( empty( self::$instance ) ) {
				self::$instance = new \indagare\cookies\CookieDough( 'first_visit' );
			}

			if ( self::$instance->is_set() ) {
				return false;
			}

			self::$instance->set( "1", time() + 60*60*24*365*10, '/' );
			return true;
		}
	}
}
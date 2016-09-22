<?php namespace indagare\cookies;

if ( ! class_exists( 'indagare\cookies\Counter' ) ) {
	class Counter {
		static public $instance = null;

		static function updateCounter() {
			if ( empty( self::$instance ) ) {
				self::$instance = new \indagare\cookies\CookieDough( 'pagecount' );
			}

			$c = self::$instance->get();
			if ( empty( $c ) ) {
				$c = 0;
			}

			if ($c > 10) {
				return false;
			}

			self::$instance->set( $c++, time()+86400, '/' );
			return true;
		}
	}
}

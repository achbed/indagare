<?php
namespace indagare\cookies;

if ( ! class_exists( '\indagare\cookies\CookieDough' ) ) {
	class CookieDough {

		/**
		 * The cookie key this class was initialized with
		 * @var string
		 */
		private $key = "";

		/**
		 * Whether the value awas already loaded from the cookie.
		 * @var boolean
		 */
		private $loaded = false;

		/**
		 * The cookie value
		 * @var mixed
		 */
		private $value = null;

		public function __construct( $key ) {
			$this->key = $key;
			if ( ! empty( $key ) ) {
				//if ( class_exists( '\Pantheon_Sessions' ) ) {
				//	if ( isset( $_SESSION['ind_cookies'][$this->key] ) ) {
				//		$this->value = $_SESSION['ind_cookies'][$this->key];
        //    $this->loaded = true;
				//	}
				//} else {
					if ( isset( $_COOKIE[$key] ) ) {
						$this->value = $_COOKIE[$key];
            $this->loaded = true;
					}
				//}
			}
		}

		public function is_set() {
			return $this->loaded;
		}

		public function get() {
			return $this->value;
		}

		public function set( $value, $expires = 0, $path ) {
			$this->value = $value;
      $this->loaded = true;
			//if ( class_exists( '\Pantheon_Sessions' ) ) {
			//	$_SESSION['ind_cookies'][$this->key] = $this->value;
			//} else {
				setcookie( $this->key, $this->value, $expires, $path, $_SERVER['HTTP_HOST'] );
			//}
		}
	}
}


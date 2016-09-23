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
			if ( ! empty( $this->key ) ) {
				if ( array_key_exists( $this->key, $_COOKIE ) ) {
					$this->value = $_COOKIE[$this->key];
					$this->loaded = true;
				}
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
			setcookie( $this->key, $this->value, $expires, $path, urlencode( $_SERVER['HTTP_HOST'] ) );
		}
	}
}

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
		}

		private function load() {
			if ( ! $this->loaded && ! empty( $this->key ) ) {
				if ( isset( $_COOKIE[$this->key] ) ) {
					$this->value = $_COOKIE[$this->key];
				}
				$this->loaded = true;
			}
		}

		public function is_set() {
			$this->load();
			return ( ! empty( $this->value ) );
		}

		public function get() {
			$this->load();
			return $this->value;
		}

		public function set( $value, $expires = 0, $path ) {
			$domain = $_SERVER['SERVER_NAME'];
			if(function_exists('get_blog_details')) {
				$blog = get_blog_details();
				$domain = $blog->domain;
			}

			$this->value = $value;
			setcookie( $this->key, $this->value, $expires, $path, $domain );
		}
	}
}


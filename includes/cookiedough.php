<?php

namespace indagare\cookies;

if ( ! class_exists( '\indagare\cookies\CookieDough' ) ) {

	class CookieDough {

		/**
		 * The store key this class was initialized with
		 *
		 * @var string
		 */
		private $key = "";

		/**
		 * Whether the value was already loaded from the backing store.
		 *
		 * @var boolean
		 */
		private $loaded = false;

		/**
		 * The cookie value
		 *
		 * @var mixed
		 */
		private $value = null;
		
		/**
		 * When the current value expires. Will never be longer than
		 * session expiration.
		 *
		 * @var integer
		 */
		private $expires = null;

		public function __construct( $key ) {
			$this->key = $key;
			$this->load( true );
		}

		/**
		 * Whether or not there's a value currently set for this object.
		 *
		 * @return boolean
		 */
		public function is_set() {
			// Clean up (make sure we don't have expired data)
			$this->clean();
				
			// Load the value if we haven't already
			$this->load( false );
			
			return $this->loaded;
		}

		/**
		 * Gets the current value
		 *
		 * @return mixed
		 */
		public function get() {
			// Clean up (make sure we don't have expired data)
			$this->clean();
			
			// Load the value if we haven't already
			$this->load( false );
			
			return $this->value;
		}

		/**
		 * Attempts to load the value from the backing store.
		 *
		 * @param boolean $reload Optional. Whether or not to force a reload from the backing store. Default is false.
		 */
		public function load( $reload = false ) {
			if ( $this->loaded && ! $reload ) {
				// We've already loaded a value, and we're not being asked to reload from store.
				return;
			}
			
			$this->loaded = false;
			$this->value = null;
			$this->expires = null;
			
			if ( ! empty( $this->key ) ) {
				if ( array_key_exists( $this->key, $_SESSION ) ) {
					$value = $_SESSION[$this->key];
					if ( is_array( $value ) && array_key_exists( 'expires', $value ) && array_key_exists( 'data', $value ) ) {
						if ( ( $value['expires'] !== 0 ) && ( $value['expires'] < time() ) ) {
							// Expired data!  Remove it.
							$this->destroy();
						} else {
							$this->value = $value['data'];
							$this->expires = $value['expires'];
							$this->loaded = true;
						}
					}
				}
			}
		}

		/**
		 * Sets the value with an optional expiration.
		 *
		 * @param mixed $value The value to store
		 * @param number $expires Optional. The timestamp when this data expires. Default is 0 (don't expire).
		 * @param mixed $depreciated
		 */
		public function set( $value, $expires = 0, $depreciated = null ) {
			$this->value = $value;
			$this->loaded = true;
			$this->expires = $expires;
			if ( ! empty( $this->key ) ) {
				$_SESSION[$this->key] = array( 'expires' => $expires, 'data' => $this->value );
				// setcookie( $this->key, $this->value, $expires, $path, urlencode( $_SERVER['HTTP_HOST'] ) );
			}
		}
		
		/**
		 * Remove this value from the backing store and reset to null values.
		 */
		public function destroy() {
			if ( ! empty( $this->key ) ) {
				unset( $_SESSION[$this->key] );
			}
			$this->loaded = false;
			$this->value = null;
			$this->expires = null;
		}

		/**
		 * Ensure that the value is not expired, and perform cleanup if needed.
		 */
		public function clean() {
			if ( empty( $this->key ) || ! $this->loaded ) {
				return;
			}
			
			if ( ( $this->expires !== 0 ) && ( $this->expires < time() ) ) {
				// Expired data!  Remove it.
				$this->destroy();
			}
		}
	}
}

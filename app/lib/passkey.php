<?php namespace indagare\users;

include_once 'db.php';
include_once 'passkeytype.php';

class Passkey {

	/**
	 * The type ID
	 * @var integer
	 */
	public $id;

	/**
	 * The passkey code as referenced in the URL and/or database
	 * @var string
	 */
	public $passkey;

	/**
	 *
	 * @var unknown
	 */
	public $user_id;

	/**
	 *
	 * @var unknown
	 */
	public $discount;

	/**
	 *
	 * @var unknown
	 */
	public $trials = 0;

	/**
	 * Whether or not the type is active
	 * @var integer
	 */
	public $active = 0;

	/**
	 * The type definition ID
	 * @var integer
	 */
	public $type = -1;

	/**
	 * The type defintion array
	 * @var array
	 */
	public $type_def = array();

	public function __construct( $id, $pk = null, $uid = null, $dis = null, $trials = null, $active = false ) {
		$this->type = PasskeyType::$UNKNOWN;

		if ( is_null( $pk ) ) {
			$v = \indagare\db\CrmDB::getPasskey( $id );
			if ( ( $v == 'false' ) || ( $v === false ) ) {
				return;
			}
			$this->cloneFrom( $v );
			return;
		}

		$this->setData( $id, $pk, $uid, $dis, $trials, $active );
	}

	/**
	 * Sets up the data for this object in one call.
	 *
	 * @param unknown $id
	 * @param unknown $pk
	 * @param unknown $uid
	 * @param unknown $dis
	 * @param unknown $trials
	 * @param unknown $active
	 */
	private function setData( $id, $pk, $uid, $dis, $trials, $active ) {
		$this->id = $id;
		$this->passkey = $pk;
		$this->user_id = $uid;
		$this->discount = $dis;
		$this->trials = $trials;
		$this->active = $active;
		$this->type_def = PasskeyType::get_type( $this->passkey );
		$this->type = $this->type['id'];
	}

	/**
	 * Clone the data from another Passkey object.  If input is not a
	 * Passkey object, then does nothing.
	 *
	 * @param Passkey $a
	 */
	private function cloneFrom( $a ) {
		if ( ! ( $a instanceof Passkey ) ){
			return;
		}

		$this->setData( $a->id, $a->passkey, $a->user_id, $a->discount, $a->trials, $a->active );
	}

	/**
	 * Returns a new Passkey object, or the string "false" on failure
	 *
	 * @param Passkey|string $key  The object or "false" string
	 */
	public static function getPasskey( $key ) {
		return new Passkey( $key );
	}

	/**
	 * Determines if a key is a valid passkey code.
	 *
	 * @param string $key "true" if valid, "false" if not.  Yes, these are strings, not booleans.
	 */
	public static function validatePasskey( $key ) {
		$pk = self::getPasskey( $key );

		if ( ( $pk == 'false' ) || ( $pk === false ) ) {
			return false;
		}

		/*
		if ( empty( $pk->active ) ) {
			return false;
		}

		if ( empty( $pk->type_def['valid'] ) ) {
			return false;
		}

		if ( $pk->trials <= 0 ) {
			return false;
		}
		*/

		return true;
	}

	/**
	 * Determines validity of the current passkey.
	 *
	 * @return boolean
	 */
	public function is_valid() {
		if ( empty( $this->active ) ) {
			return false;
		}

		if ( empty( $this->type_def['valid'] ) ) {
			return false;
		}

		if ( $this->trials <= 0 ) {
			return false;
		}

		return true;
	}
}


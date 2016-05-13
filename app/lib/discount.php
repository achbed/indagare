<?php namespace indagare\users;

include_once 'db.php';

class Discount {

	/**
	 * The discount ID
	 * @var integer
	 */
	public $id = null;

	/**
	 * The discount code as referenced in the URL and/or database
	 * @var string
	 */
	public $code = null;

	/**
	 * The human-readable name of this discount.
	 * @var string
	 */
	public $name = '';

	/**
	 * The human-readable description of this discount.
	 * @var string
	 */
	public $description = '';

	/**
	 * The discount percent to apply (if any).
	 * @var number
	 */
	public $percent = 0;

	/**
	 * The discount currency amount to apply (if any).
	 * @var number
	 */
	public $amount = 0;

	/**
	 * Whether or not the discount is active
	 * @var boolean
	 */
	public $active = false;

	/**
	 * Creates the Discount object.
	 *
	 * @param integer|Discount $id
	 * @param string $c Code
	 * @param string $n Name
	 * @param string $d Description
	 * @param number $pct Discount percentage
	 * @param number $amt Currency amount
	 * @param boolean $a Active?
	 */
	public function __construct( $id, $c = null, $n = null, $d = null, $pct = 0, $amt = 0, $a = false ) {
		if ( $id instanceof Discount ) {
			$this->cloneFrom( $id );
			return;
		}

		if ( is_null( $c ) ) {
			$v = \indagare\db\CrmDB::getDiscount( $id );
			if ( ( $v == 'false' ) || ( $v === false ) ) {
				return;
			}
			$this->cloneFrom( $v );
			return;
		}

		$this->setData( $id, $c, $n, $d, $pct, $amt, $a );
	}

	/**
	 * Sets up the data for this object in one call.
	 *
	 * @param integer $id
	 * @param string $c Code
	 * @param string $n Name
	 * @param string $d Description
	 * @param number $pct Discount percentage
	 * @param number $amt Currency amount
	 * @param boolean $a Active?
	 */
	private function setData( $id,  $c = null, $n = null, $d = null, $pct = 0, $amt = 0, $a = false ) {
		$this->id = $id;
		$this->code = $c;
		$this->name = $n;
		$this->description = $d;
		$this->percent = $pct;
		$this->amount = $amt;
		$this->active = $a;
	}

	/**
	 * Clone the data from another Discount object.  If input is not a
	 * Discount object, then does nothing.
	 *
	 * @param Discount $a
	 */
	private function cloneFrom( $a ) {
		if ( ! ( $a instanceof Discount ) ){
			return;
		}

		$this->setData( $a->id, $a->code, $a->name, $a->description, $a->percent, $a->amount, $a->active );
	}

	/**
	 * Returns a new Discount object, or the string "false" on failure
	 *
	 * @param Discount|string $key  The object or "false" string
	 */
	public static function getDiscount( $key ) {
		return new Discount( $key );
	}

	/**
	 * Finds the requested discount based on GET or POST input.  If no match,
	 * an active discount with 0 for both amount and percent is returned.
	 * @return Discount The matching discount object.
	 */
	public static function findDiscount() {
		global $DiscountArray;

		$discount_code = '';

		if ( isset( $_GET['dc'] ) && ! empty( $_GET['dc'] ) ) {
			$discount_code = $_GET['dc'];
		}

		if ( isset( $_POST['dc'] ) && ! empty( $_POST['dc'] ) ) {
			$discount_code = $_POST['dc'];
		}

		if ( ! empty( $discount_code ) ) {
			foreach( $DiscountArray as $d ) {
				if ( $d->code == $discount_code ) {
					return $d;
				}
			}
		}

		return new Discount( -1, '', 'No Discount', 'No Discount', 0, 0, true );
	}

	/**
	 * Determines validity of the current Discount.
	 *
	 * @return boolean
	 */
	public function is_valid() {
		if ( empty( $this->active ) ) {
			return false;
		}

		if ( empty( $this->code ) ) {
			return false;
		}

		if ( $this->percent < 0 ) {
			return false;
		}

		if ( $this->percent> 100 ) {
			return false;
		}

		if ( $this->amount < 0 ) {
			return false;
		}

		return true;
	}

}

global $DiscountArray;
/**
 * An array of valid Discount objects.  Storing this here for now.
 *
 *  @TODO: Should be a database table in CRM
 *
 * @var array
 */
$DiscountArray = array(
	new Discount( -1, 'mailinglist', 'Mailing List 20% Discount', '20% discount applied', 20, 0, true ),
);

<?php
namespace indagare\users;

class PasskeyType {
	/**
	 * Unknown Passkey Type
	 * @var integer
	 */
	public static $UNKNOWN = 3;

	/**
	 * Passcode Type A
	 * @var integer
	 */
	public static $TRIAL_A = 0;

	/**
	 * Passcode Type B
	 * @var integer
	 */
	public static $TRIAL_B = 1;

	/**
	 * Specially encoded Passcode
	 * @var integer
	 */
	public static $TRIAL_CODED = 2;

	/**
	 * Specially encoded Passcode
	 * @var integer
	 */
	public static $COMP_BASIC = 91;

	/**
	 * Specially encoded Passcode
	 * @var integer
	 */
	public static $COMP_ELITE = 92;

	/**
	 * Specially encoded Passcode
	 * @var integer
	 */
	public static $COMP_CONNO = 93;

	/**
	 * An array of trial type codes and their definitions
	 * @var array
	 */
	private static $types = array(
		array(
			'id' => 3,
			'code' => null,
			'name' => 'Unknown Trial Membership',
			'description' => 'Unknown membership.  Should never be used.',
			'expires' => 'today',
			'membership_level' => -999,
			'sort' => 9999,
			'valid' => false,
		),
		array(
			'id' => 0,
			'code' => 'trial-a',
			'name' => 'Trial Basic Membership, 30 days',
			'description' => '',
			'expires' => '+30 days',
			'membership_level' => 1,
			'sort' => 10,
			'valid' => true,
		),
		array(
			'id' => 1,
			'code' => 'trial-b',
			'name' => 'Complimentary Basic Membership',
			'description' => '',
			'expires' => '+1 year',
			'membership_level' => 1,
			'valid' => true,
		),
		array(
			'id' => 91,
			'code' => 'basic-',
			'name' => 'Complimentary Basic Membership',
			'description' => '',
			'expires' => '+1 year',
			'membership_level' => 1,
			'valid' => true,
		),
		array(
			'id' => 92,
			'code' => 'elite-',
			'name' => 'Complimentary Elite Membership',
			'description' => '',
			'expires' => '+1 year',
			'membership_level' => 2,
			'valid' => true,
		),
		array(
			'id' => 93,
			'code' => 'conn-',
			'name' => 'Complimentary Connoisseur Membership',
			'description' => '',
			'expires' => '+1 year',
			'membership_level' => 3,
			'valid' => true,
		),
		array(
			'id' => 4,
			'code' => 'trial30-',
			'name' => 'Trial Basic Membership, 30 days',
			'description' => '',
			'expires' => '+30 days',
			'membership_level' => 1,
			'valid' => true,
		),
		array(
			'id' => 5,
			'code' => 'trial90-',
			'name' => 'Trial Basic Membership, 90 days',
			'description' => '',
			'expires' => '+90 days',
			'membership_level' => 1,
			'valid' => true,
		),
	);

	/**
	 * Gets the array containing the type definition for a given type code
	 * string. If no match, returns false.
	 *
	 * @param string $typecode  The type code to match.
	 * @return false|array  False if no match, the type definition otherwise.
	 */
	public static function get_type( $type ) {
		$r = null;
		$t = strtolower( $type );

		foreach ( self::$types as $v ) {
			if ( $v['id'] == self::$UNKNOWN ) {
				$r = $v;
				continue;
			}

			if ( stripos( $t, $v['code'] ) === 0 ){
				return $v;
			}
		}

		return $r;

		/*
		 * Preserve cause it's intersting, but don't use at the moment.
		 */
		/*
		// Let's try the automated method of getting the level and length
		$a = explode( '-', $t, 3 );
		if(count($a) != 3) {
			return $r;
		}
		$level = $a[0];
		$len = $a[1];
		$code = $a[2];
		if(empty($level) || empty($len) || empty($code)) {
			return $r;
		}

		$level_item = null;
		foreach( self::$level_types as $v ) {
			if ( $level == $v['code'] ) {
				$level_item = $v;
				break;
			}
		}

		$len_span = substr( $len, -1 );
		$len_span_value = "";
		switch($len_span) {
			case "d":
				$len_span_value = "days";
				break;
			case "w":
				$len_span_value = "weeks";
				break;
			case "m":
				$len_span_value = "months";
				break;
			case "y":
				$len_span_value = "years";
				break;
		}
		$len_value = intval( substr( $len, 0, -1 ) );
		if($len_value == 1) {
			$len_span_value = substr($len_span_value, 0, -1);
		}
		$len = sprintf("%d %s", $len_value, $len_span_value);

		return array(
			'id' => self::$TRIAL_CODED,
			'code' => $t,
			'name' => 'Trial ' . $level_item['name'] . ', '.$len,
			'expires' => '+' . $len,
			'membership_level' => $level_item['membership_level'],
			'sort' => 5,
			'valid' => true,
		);
		*/
	}
}

<?php
namespace indagare\iajax;

require_once 'wpdef.php';
include_once 'user.php';
include_once 'db.php';
include_once 'mail.php';
include_once 'Mail.php';
include_once 'lphp.php';
include_once '../resources/emails/thank_you.php';
include_once '../resources/emails/apply.php';
//require_once('iajax_handler.php');

class AjaxHandler {
	/**
	 * A comma-separated list of email addresses to send an error email to.
	 * @var string
	 */
	private static $error_mail = 'dwallace@shr.global';

	/**
	 * A string to prepend to the email subject for error emails
	 * @var string
	 */
	private static $error_mail_prefix = 'ERROR: ';

	/**
	 * A comma-separated list of email addresses to send a debug email to.
	 * @var string
	 */
	private static $debug_mail = '';

	/**
	 * A string to prepend to the email subject for debug emails
	 * @var string
	 */
	private static $debug_mail_prefix = 'DEBUG: ';

	/**
	 * A comma-separated list of email addresses to send a debug email to.
	 * @var string
	 */
	private static $admin_mail = 'admin@indagare.com';

	/**
	 * A string to prepend to the email subject for debug emails
	 * @var string
	 */
	private static $admin_mail_prefix = 'NOTICE: ';

	/**
	 * Holds the instance of this plugin (once initialized)
	 * @var \indagare\iajax\AjaxHandler
	 */
	private static $instance = null;

	/**
	 * Sends an HTML email using the \indagare\util\IndagareMailer class.
	 *
	 * @param string $subject
	 * @param string $body
	 * @param string $recipients
	 */
	protected static function _email( $subject, $body, $recipients ) {
		if ( ! empty( $recipients ) ) {
			$m = new \indagare\util\IndagareMailer();
			return $m->sendHtml( $subject, $body, $recipients );
		}
	}

	/**
	 * Sends out an email to the admin list (if it exists).  Also
	 * prepends the admin mail prefix to the subject line.
	 *
	 * @param string $subject
	 * @param string $body
	 */
	protected static function _email_admin( $subject, $body) {
		return self::_email( self::$admin_mail_prefix . $subject, $body, self::$admin_mail );
	}

	/**
	 * Sends out an email to the debug list (if it exists).  Also
	 * prepends the debug mail prefix to the subject line.
	 *
	 * @param string $subject
	 * @param string $body
	 */
	protected static function _email_debug( $subject, $body) {
		return self::_email( self::$debug_mail_prefix . $subject, $body, self::$debug_mail );
	}

	/**
	 * Sends out an email to the error list (if it exists).  Also
	 * prepends the error mail prefix to the subject line.
	 *
	 * @param string $subject
	 * @param string $body
	 */
	protected static function _email_error( $subject, $body) {
		return self::_email( self::$error_mail_prefix . $subject, $body, self::$error_mail );
	}

/**
	 * Returns the main instance of the class, creating it if needed.
	 * @return \indagare\iajax\AjaxHandler
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Adds hooks and loads translations
	 */
	public function __construct() {
		if ( empty( $_REQUEST["task"] ) ) {
			// The request doesnt contain a task.  Die and ignore any other
			// handlers.
			die( "" );
		}

		$task = htmlspecialchars( $_REQUEST["task"], ENT_QUOTES, 'UTF-8' );

		if ( strpos( $task, '_' ) === 0 ) {
			// Don't even try to call functions starting with '_'.
			return;
		}

		if ( ! method_exists( __CLASS__, $task ) ) {
			return;
		}

		if ( substr( $task, -2 ) == '_j' ) {
			header('Content-Type: application/json');
		}

		return $this->{$task}();
	}

	/**
	 * Call the payment gateway and charge the card.
	 *
	 * @param array $order The parameters for the order
	 * 			name:  Cardholder name
	 */
	protected static function _charge_it( $order ) {
		// constants
		$acc = \indagare\users\AccountCreator::getAccountCreator( );

		$gateway["host"] = \indagare\config\Config::$pay_host;
		$gateway["port"] = \indagare\config\Config::$pay_port;
		$gateway["keyfile"] = \indagare\config\Config::$pay_key;
		$gateway["configfile"] = \indagare\config\Config::$pay_config;

		// form data
		$myorder["name"] = $_POST["cc_holder"];
		$myorder["cardnumber"] = $_POST["cc_num"];
		$myorder["cardexpmonth"] = $_POST["cc_m"];
		$myorder["cardexpyear"] = $_POST["cc_y"];
		$myorder["cvmindicator"] = "provided";
		$myorder["cvmvalue"] = $_POST["ccv"];
		$myorder["chargetotal"] = 0;
		$myorder["ordertype"] = "SALE";

		$args = array_merge( $myorder, $order, $gateway );

		if ( $args['chargetotal'] <= 0 ) {
			return array(
				'r_approved' => 'SKIPPED',
				'r_code' => '0',
				'r_error' => '0 or negative charge attempted.',
			);
		}

		$mylphp = new \lphp( );
		$result = $mylphp->curl_process( $args );  # use curl methods

		return $result;
	}

	/**
	 * DEPRECIATED: Handles processing input from the first tab of the
	 * signup form.
	 */
	public static function signup1() {
		global $acc;

		$acc = \indagare\users\AccountCreator::getAccountCreator( );

		$acc->user->prefix = $_POST["prefix"];
		$acc->user->first_name = $_POST["fn"];
		$acc->user->last_name = $_POST["ln"];
		$acc->user->middle_initial = $_POST["minitial"];
		$acc->user->email = $_POST["email"];
		$acc->user->membership_level = $_POST["l"];
		$acc->user->membership_years = $_POST["y"];
		$acc->user->passkey_id = $_POST["tgCode"];
		$acc->user->primary_street_address = $_POST['s_address1'];
		$acc->user->primary_street_address2 = $_POST['s_address2'];
		$acc->user->primary_city = $_POST['s_city'];
		$acc->user->primary_state = $_POST['s_state'];
		$acc->user->primary_postal = $_POST['s_zip'];
		$acc->user->primary_country = $_POST['s_country'];
		$acc->user->phone_home = $_POST['phone'];
	}

	/**
	 * DEPRECIATED: Validates the passkey and sets it for the current user
	 * if valid. Used by the second tab of the signup form.
	 */
	public static function signup21() {
		global $acc;

		if ( \indagare\users\Passkey::validatePasskey( $_REQUEST["rc"] ) ) {
			$acc->user->passkey_id = $_REQUEST["rc"];
			print "true";
			return;
		}

		print "false";
	}

	/**
	 * DEPRECIATED: Handles processing input from the non-passkey portion of
	 *  the second tab of the signup form.
	 */
	public static function signup22() {
		global $acc,$name,$email,$phone,$address;

		$acc = \indagare\users\AccountCreator::getAccountCreator( );
		//print_r($acc);
		$acc->user->question_1 = $_POST["top_destinations"];
		$acc->user->question_2 = $_POST["fav_hotels"];
		$acc->user->question_3 = $_POST["reason_travel"];
		$acc->user->question_4 = $_POST["next_destination"];
		//print_r($acc);
		$name = $acc->user->prefix . " " . $acc->user->first_name . " " . $acc->user->middle_initial . " " . $acc->user->last_name;
		$email = $acc->user->email;
		$phone = $acc->user->phone_home;
		$address = $acc->user->primary_street_address . ", " . $acc->user->primary_street_address2 . ", " . $acc->user->primary_postal . " " . $acc->user->primary_city . " " . $acc->user->primary_state . ", " . $acc->user->primary_country;
	}

	/**
	 * DEPRECIATED:  Validates that the login exists.
	 */
	public static function chkLogin() {
		$u = \indagare\users\User::checkLogin( $_POST["login"] );
		if ( $u ) {
			print "true";
		} else {
			print "false";
		}
	}

	/**
	 * Validates that the login exists.  Returns a JSON object.
	 */
	public static function chkLogin_j() {
		$response = array();
		try {
			if ( empty( $_POST['login'] ) ) {
				throw new \Exception( 'Empty input', 0 );
			}
			$response['login'] = $_POST['login'];

			$u = \indagare\users\User::checkLogin( $_POST["login"] );
			$response['exists'] = ! empty( $u );
		} catch ( \Exception $e ) {
			$response = array(
				'err' => $e->getMessage(),
			);

			$msg = "Response:\r\n" . print_r( $response, true ) . "\r\n\r\n";
			self::_email_debug('chkLogin_j: Processing Error', $msg );
			if ( $e->getCode() != 0 ) {
				self::_email_error('chkLogin_j: Processing Error', $msg );
			}
		}

		print json_encode( $response );
		exit();
	}

	/**
	 * Validates that a trial key exists, and returns various information
	 * about it.
	 */
	public static function chkTrialKey_j() {
		global $acc;

		$response = array(
				'valid' => false,
				'id' => 3,
				'name' => 'Invalid code',
				'length' => 'Invalid code',
		);

		try {
			if(!isset($_REQUEST["rc"]) || empty($_REQUEST["rc"])) {
				throw new \Exception( 'Empty input', 0 );

			}

			$acc = \indagare\users\AccountCreator::getAccountCreator( );
			$key = \indagare\db\CrmDB::getPasskey( $_REQUEST["rc"] );

			if ( ( $key == "false" ) || ( $key == false ) ) {
				throw new \Exception( 'Invalid code', 0 );
			}

			if ( $key->is_valid() ) {
				$acc->user->passkey_id = $_REQUEST["rc"];
			}

			$response = array(
				'valid' => $key->is_valid(),
				'id' => $key->type_def['id'],
				'name' => $key->type_def['name'],
				'length' => ucwords( substr( $key->type_def['expires'], 1 ) ),
			);
		} catch( \Exception $e ) {
			$response = array(
				'valid' => false,
				'id' => 3,
				'name' => $e->getMessage(),
				'length' => $e->getMessage(),
				'err' => $e->getMessage(),
			);

			$msg = "Response:\r\n" . print_r( $response, true ) . "\r\n\r\n";
			self::_email_debug('chkTrialKey_j: Processing Error', $msg );
			if ( $e->getCode() != 0 ) {
				self::_email_error('chkTrialKey_j: Processing Error', $msg );
			}
		}

		print json_encode( $response );
		exit();
	}

	/**
	 * Handles creation and setup of a new trial or complementary
	 * membership.  Returns the result as a JSON object.
	 */
	public static function newTrial_j() {
		global $acc;

		$response = array(
			'success' => false,
			'startdate' => '',
			'enddate' => '',
			'name' => '',
			'length' => '',
			'price' => '',
		);

		try {
			$acc = \indagare\users\AccountCreator::getAccountCreator( );

			if(empty($_POST["fn"])) throw new \Exception( 'Empty first name', 0 );
			if(empty($_POST["ln"])) throw new \Exception( 'Empty last name', 0 );
			if(empty($_POST["email"])) throw new \Exception( 'Empty email', 0 );
			if(empty($_POST["phone"])) throw new \Exception( 'Empty phone', 0 );
			if(empty($_POST["username"])) throw new \Exception( 'Empty username', 0 );
			if(empty($_POST["password"])) throw new \Exception( 'Empty password', 0 );
			if(empty($_POST["s_address1"])) throw new \Exception( 'Empty s_address1', 0 );
			if(empty($_POST["s_city"])) throw new \Exception( 'Empty s_city', 0 );
			if(empty($_POST["s_state"])) throw new \Exception( 'Empty s_state', 0 );
			if(empty($_POST["s_zip"])) throw new \Exception( 'Empty s_zip', 0 );
			if(empty($_POST["s_country"])) throw new \Exception( 'Empty s_country', 0 );
			if(empty($_POST["passKey"])) throw new \Exception( 'Empty passKey', 0 );

			$u = \indagare\users\User::checkLogin( $_POST["username"] );
			if(!empty($u)) throw new \Exception( 'Username exists', 0 );

			$acc->user->prefix = '';
			$acc->user->first_name = $_POST["fn"];
			$acc->user->last_name = $_POST["ln"];
			$acc->user->email = $_POST["email"];
			$acc->user->phone_home = $_POST['phone'];

			$acc->user->login = $_POST['username'];
			$acc->user->password = $_POST['password'];
			$acc->user->primary_street_address = $_POST['s_address1'];
			$acc->user->primary_street_address2 = (empty($_POST['s_address2'])?'':$_POST['s_address2']);
			$acc->user->primary_city = $_POST['s_city'];
			$acc->user->primary_state = $_POST['s_state'];
			$acc->user->primary_postal = $_POST['s_zip'];
			$acc->user->primary_country = $_POST['s_country'];
			$acc->user->passkey_id = $_POST['passKey'];

			$key = new \indagare\users\Passkey( $_POST['passKey'] );

			if ( ( $key == "false" ) || ( $key == false ) ) {
				throw new \Exception('Incorrect trial code.',0);
			}

			if ( $key->trials <= 0 ) {
				throw new \Exception('No trials remaining on this trial code.',0);
			}

			if ( ! $key->is_valid() ) {
				throw new \Exception('Invalid trial code.',0);
			}

			$nao = time();
			$acc->user->membership_created_at = date( 'Y-m-d H:i:s', $nao );
			$acc->user->membership_expires_at = date( 'Y-m-d H:i:s', strtotime( $key->type_def['expires'], $nao ) );
			$acc->user->membership_level = $key->type_def['membership_level'];

			$response['price'] = 0;
			$response['name'] = $key->type_def['name'];
			$response['length'] = ucwords( substr( $key->type_def['expires'], 1 ) );
			$response['startdate'] = date( 'm/d/Y', strtotime( $acc->user->membership_created_at ) );
			$response['enddate'] = date( 'm/d/Y', strtotime( $acc->user->membership_expires_at ) );

			// Create the account and decrement the passkey
			$uid = \indagare\db\CrmDB::createTrialUser( $acc->user );
			$response['success'] = true;

			\indagare\db\CrmDB::decrementPasskey( $key );

			// Send the user payload.  No clue what's in that.
			$payloadurl = "http://".\indagare\config\Config::$payloadserver."/users/$uid/index_user";
			$timeout = 5;
			stream_context_set_default( array( 'http' => array( 'timeout' => $timeout ) ) );
			$payload = @get_headers( $payloadurl );

			// Initiate the user session immediately!
			$u = \indagare\db\CrmDB::getUserById( $uid );
			$u->startSession( );

			$email_subject = createThankyouEmailSubject();
			$thankyou = createThankyouEmail( $acc->user->first_name . " " . $acc->user->last_name, $acc->user->primary_street_address, $acc->user->primary_city, $acc->user->primary_state, $acc->user->primary_postal, $acc->user->primary_country, $acc->user->email, $key->type_def['name'] );

			self::_email( $email_subject, $thankyou, $acc->user->email );
			self::_email_admin( $email_subject, $thankyou );
			self::_email_debug( $email_subject, $thankyou );

			//throw new \Exception('debug',0);
		} catch( \Exception $e ) {
			$response = array_merge( array( 'success' => false ), $response, array(
				'err' => $e->getMessage(),
			) );

			$msg = "Response:\r\n" . print_r( $response, true ) . "\r\n\r\n";
			if(!empty($key)) $msg .= "Key:\r\n" . print_r( $key, true ) . "\r\n\r\n";
			$msg .= "ACC:\r\n" . print_r( $acc, true ) . "\r\n\r\n";

			if($e->getMessage() == 'debug') {
				$response['$acc'] = $acc;
				if(!empty($nao)) $response['$nao'] = $nao;
				if(!empty($key)) $response['$key'] = $key;
				if(!empty($uid)) $response['$uid'] = $uid;
				if(!empty($payload)) $response['$payload'] = $payload;
				if(!empty($u)) $response['$u'] = $u;
				if(!empty($email_subject)) $response['$email_subject'] = $email_subject;
				if(!empty($thankyou)) $response['$thankyou'] = $thankyou;
			}

			self::_email_debug('newTrial_j: Processing Error', $msg );
			if ( $e->getCode() != 0 ) {
				self::_email_error('newTrial_j: Processing Error', $msg );
			}
		}

		print json_encode( $response );
		exit();
	}

	/**
	 * Handles payment processing and account setup for a paid membership.
	 * Returns status as a JSON object.
	 */
	public static function payment_j() {
		global $acc;

		$response = array(
			'success' => false,
			'startdate' => '',
			'enddate' => '',
			'name' => '',
			'length' => '',
			'price' => '',
		);
		try {
			//print "Test";
			$acc = \indagare\users\AccountCreator::getAccountCreator( );
			$acc->user->first_name = $_POST["fn"];
			$acc->user->last_name = $_POST["ln"];
			$acc->user->email = $_POST["email"];
			$acc->user->membership_level = $_POST["l"];
			$acc->user->membership_years = $_POST["y"];
			$acc->user->login = $_POST['username'];
			$acc->user->password = $_POST['password'];
			$acc->user->primary_street_address = $_POST['s_address1'];
			$acc->user->primary_street_address2 = $_POST['s_address2'];
			$acc->user->primary_city = $_POST['s_city'];
			$acc->user->primary_state = $_POST['s_state'];
			$acc->user->primary_postal = $_POST['s_zip'];
			$acc->user->primary_country = $_POST['s_country'];
			$acc->user->passkey_id = '';

			$nao = time();
			$order_id = $nao . "_" . rand( 1, 100 );

			$mb = \indagare\db\CrmDB::getMembershipByLevel( $acc->user->membership_level + 1 );
			$charge = $mb->getMembershipPrice( $acc->user->membership_years );
			if ( isset( $_POST['dc'] ) ) {
				$disc = floatval( $_POST['dc'] );
				if ( ( $disc < 0 ) || ( $disc >=100 ) ) {
					$disc = 0;
				}
				$mb->discount = $disc;
			}

			$response['name'] = $mb->name;
			$response['length'] = $acc->user->membership_years . ' Year' . ( $acc->user->membership_years > 1 ? 's' : '' );
			$response['price'] = $charge;

			// form data
			$myorder["chargetotal"] = $charge;
			$myorder["oid"] = $order_id;
			$myorder["address1"] = $acc->user->primary_street_address;
			$myorder["address2"] = $acc->user->primary_street_address2;
			$myorder["city"] = $acc->user->primary_city;
			$myorder["state"] = $acc->user->primary_state;
			$myorder["country"] = $acc->user->primary_country;
			$myorder["email"] = $acc->user->email;
			$myorder["zip"] = $acc->user->primary_postal;

			// setup recurring if order is for 1 year
			if ( $acc->user->membership_years == 1 ) {
				$myorder["action"] = "SUBMIT";
				$myorder["installments"] = "1";
				$myorder["threshold"] = "3";
				$myorder["startdate"] = "immediate";
				$myorder["periodicity"] = "yearly";
			}

			$acc->user->membership_created_at = date( 'Y-m-d H:i:s', $nao );
			$acc->user->membership_expires_at = date( 'Y-m-d H:i:s', strtotime( '+' . $acc->user->membership_years . ' years', $nao ) );

			$response['startdate'] = date( 'm/d/Y', strtotime( $acc->user->membership_created_at ) );
			$response['enddate'] = date( 'm/d/Y', strtotime( $acc->user->membership_expires_at ) );

			$result = self::_charge_it( $myorder );
			$response = array_merge( $response, $result );
			$response['success'] = false;

			if ( $response["r_approved"] != "APPROVED" ) {
				throw new \Exception( $response['r_error'] );
			}

			// Create the account and store the payment record
			$uid = \indagare\db\CrmDB::createUser( $acc->user );
			$oid = \indagare\db\CrmDB::addOrder( $uid, $charge, $response['r_approved'], $nao, $order_id, 1, substr( $_POST["cc_num"], -4 ), $_POST["cc_m"], $_POST["cc_y"] );
			\indagare\db\CrmDB::addLineItem( $oid, $charge . '00' );
			$response['success'] = true;

			// Send the user payload.  No clue what's in that.
			$payloadurl = "http://".\indagare\config\Config::$payloadserver."/users/$uid/index_user";
			$timeout = 5;
			stream_context_set_default( array( 'http' => array( 'timeout' => $timeout ) ) );
			$payload = @get_headers( $payloadurl );

			// Initiate the user session immediately!
			$u = \indagare\db\CrmDB::getUserById( $uid );
			$u->startSession( );

			// Do emails last! That way a failed email doesnt result in a charged card with an error notice.
			$email_subject = createThankyouEmailSubject();
			$thankyou = createThankyouEmail( $acc->user->getDisplayName(), $acc->user->primary_street_address, $acc->user->primary_city, $acc->user->primary_state, $acc->user->primary_postal, $acc->user->primary_country, $acc->user->email, $mb->name . " - " . $acc->user->membership_years . " years PRICE: $" . $charge . ".00" );

			self::_email( $email_subject, $thankyou, $acc->user->email );
			self::_email_admin( $email_subject, $thankyou );
			self::_email_debug( $email_subject, $thankyou );
		} catch( \Exception $e ) {
			$response = array_merge( array( 'success' => false ), $response, array(
				'err' => $e->getMessage(),
			) );

			$msg = "Response:\r\n" . print_r( $response, true ) . "\r\n\r\n";
			$msg .= "ACC:\r\n" . print_r( $acc, true ) . "\r\n\r\n";
			$msg .= "MyOrder:\r\n". print_r( $myorder, true ) . "\r\n\r\n";
			self::_email_debug('payment_j: Processing Error', $msg );
			if ( $e->getCode() != 0 ) {
				self::_email_error('payment_j: Processing Error', $msg );
			}
		}

		print json_encode( $response );
		exit();
	}

	/**
	 * Validates that a trial key is valid, and returns the type if it does.
	 */
	public static function chkTrialKey() {
		global $acc;
		$acc = \indagare\users\AccountCreator::getAccountCreator( );
		$key = \indagare\db\CrmDB::getPasskey( $_REQUEST["rc"] );

		if ( ( $key == "false" ) || ( $key == false ) ) {
			print "false";
			return;
		}

		if ( $key->is_valid() ) {
			$acc->user->passkey_id = $_REQUEST["rc"];
			print "true";
			print "|" . $key->type;
			return;
		}

		print "false";
	}

	/**
	 * Creates a new trial membership.
	 */
	public static function newTrial() {
		global $acc;

		$acc = \indagare\users\AccountCreator::getAccountCreator( );
		$acc->user->login = $_POST['username'];
		$acc->user->password = $_POST['password'];
		$acc->user->primary_street_address = $_POST['s_address1'];
		$acc->user->primary_street_address2 = $_POST['s_address2'];
		$acc->user->primary_city = $_POST['s_city'];
		$acc->user->primary_state = $_POST['s_state'];
		$acc->user->primary_postal = $_POST['s_zip'];
		$acc->user->primary_country = $_POST['s_country'];
		$acc->user->passkey_id = $_POST['passKey'];
		$acc->user->membership_level = 0;
		$acc->user->membership_years = 1;
		$acc->user->membership_created_at = date( 'Y-m-d H:i:s' );
		$acc->user->question_1 = $_POST["top_destinations"];
		$acc->user->question_2 = $_POST["fav_hotels"];
		$acc->user->question_3 = $_POST["reason_travel"];
		$acc->user->question_4 = $_POST["next_destination"];

		$key = \indagare\db\CrmDB::getPasskey( $_POST['passKey'] );
		if ( ( $key == "false" ) || ( $key == false ) ) {
			print "invalid trial code";
			return;
		}
		if ( $key->trials <= 0 ) {
			print "no trials remaining";
			return;
		}

		$new_level = $acc->user->membership_level;
		$acc->user->membership_level = $key->type_def['membership_level'];

		$acc->user->membership_expires_at = date( 'Y-m-d H:i:s', strtotime( $key->type_def['expires'] ) );
		$mbText = $key->type_def['name'];

		$uid = \indagare\db\CrmDB::createTrialUser( $acc->user );
		\indagare\db\CrmDB::decrementPasskey( $key );

		$email_subject = createThankyouEmailSubject();
		$thankyou = createThankyouEmail( $acc->user->first_name . " " . $acc->user->last_name, $acc->user->primary_street_address, $acc->user->primary_city, $acc->user->primary_state, $acc->user->primary_postal, $acc->user->primary_country, $acc->user->email, $mbText );

		$email = $acc->user->email;
		$m = new \indagare\util\IndagareMailer( );
		$m->sendHtml( $email_subject, $thankyou, $email );
		if ( ! empty( self::$debug_mail ) ) {
			$m->sendHtml( self::$debug_mail_prefix.$email_subject, $thankyou, self::$debug_mail );
		}
		if ( ! empty( self::$admin_mail ) ) {
			$m->sendHtml( self::$admin_mail_prefix.$email_subject, $thankyou, self::$admin_mail );
		}

		$u = \indagare\db\CrmDB::getUserById( $uid );
		$u->startSession( );
		$payloadurl = "http://".\indagare\config\Config::$payloadserver."/users/$uid/index_user";
		$timeout = 5;
		stream_context_set_default( array( 'http' => array( 'timeout' => $timeout ) ) );
		$payload = @get_headers( $payloadurl );
		print "true";
	}

	/**
	 * Handles payment processing and setup of new accounts
	 */
	public static function payment() {
		global $acc;

		$acc = \indagare\users\AccountCreator::getAccountCreator( );

		$acc->user->prefix = '';
		$acc->user->first_name = $_POST["fn"];
		$acc->user->last_name = $_POST["ln"];
		$acc->user->email = $_POST["email"];
		$acc->user->phone_home = $_POST['phone'];

		$acc->user->login = $_POST['username'];
		$acc->user->password = $_POST['password'];
		$acc->user->primary_street_address = $_POST['s_address1'];
		$acc->user->primary_street_address2 = $_POST['s_address2'];
		$acc->user->primary_city = $_POST['s_city'];
		$acc->user->primary_state = $_POST['s_state'];
		$acc->user->primary_postal = $_POST['s_zip'];
		$acc->user->primary_country = $_POST['s_country'];

		$acc->user->membership_level = $_POST["l"];
		$acc->user->membership_years = $_POST["y"];
		$acc->user->passkey_id = $_POST['passKey'];

		$acc->user->secondary_street_address = $_POST['address1'];
		$acc->user->secondary_street_address2 = $_POST['address2'];
		$acc->user->secondary_city = $_POST['city'];
		$acc->user->secondary_state = $_POST['state'];
		$acc->user->secondary_postal = $_POST['zip'];
		$acc->user->secondary_country = $_POST['country'];

		$order_id = time( ) + "_" + rand( 1, 100 );

		$mb = \indagare\db\CrmDB::getMembershipByLevel( $acc->user->membership_level + 1 );
		if ( isset( $_POST['dc'] ) ) {
			$mb->discount = $_POST['dc'];
		}

		//print $mb->toJSON();
		//echo "mb start";
		$charge = $mb->getMembershipPrice( $acc->user->membership_years );
		//print $charge;

		// 1909749438,staging.linkpt.net"1129
		// 1001177025,secure.linkpt.net:1129
		$mylphp = new \lphp( );


		// constants
		/*$myorder["host"]	   = "secure.linkpt.net";
		 $myorder["port"]	   = "1129";
		 $myorder["keyfile"] = "/home/client02/firstdata/1001177025.pem";
		 $myorder["configfile"] = "1001177025"; */

		//$myorder["debug"] = true;
		//$myorder["debugging"] = true;

		$myorder["host"] = \indagare\config\Config::$pay_host;
		$myorder["port"] = \indagare\config\Config::$pay_port;
		$myorder["keyfile"] = \indagare\config\Config::$pay_key;
		$myorder["configfile"] = \indagare\config\Config::$pay_config;

		// form data
		$myorder["name"] = $_POST["cc_holder"];
		$myorder["cardnumber"] = $_POST["cc_num"];
		$myorder["cardexpmonth"] = $_POST["cc_m"];
		$myorder["cardexpyear"] = $_POST["cc_y"];
		$myorder["cvmindicator"] = "provided";
		$myorder["cvmvalue"] = $_POST["ccv"];
		$myorder["chargetotal"] = $charge;
		$myorder["ordertype"] = "SALE";

		$myorder["oid"] = $order_id;

		$myorder["address1"] = $acc->user->primary_street_address;
		$myorder["address2"] = $acc->user->primary_street_address2;
		$myorder["city"] = $acc->user->primary_city;
		$myorder["state"] = $acc->user->primary_state;
		$myorder["country"] = $acc->user->primary_country;
		$myorder["email"] = $acc->user->email;
		$myorder["zip"] = $acc->user->primary_postal;

		// setup recurring if order is for 1 year
		if ( $acc->user->membership_years == 1 ) {
			$myorder["action"] = "SUBMIT";
			$myorder["installments"] = "1";
			$myorder["threshold"] = "3";
			$myorder["startdate"] = "immediate";
			$myorder["periodicity"] = "yearly";
		}

		$response = $mylphp->curl_process($myorder);  # use curl methods

		if ( $result["r_approved"] == "APPROVED" )// success
		{
			print $result["r_approved"] . "-" . $result['r_code'] . "-";
			$acc->user->membership_created_at = date( 'Y-m-d H:i:s' );
			$acc->user->membership_expires_at = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) + $acc->user->membership_years ) );
			//print "create user\n";

			try {
				$uid = \indagare\db\CrmDB::createUser( $acc->user );
				//print "$uid, create order\n";
				//print_r($acc->user->getID());
				$uid = \indagare\db\CrmDB::updateUserQuestion( $acc->user, $uid );
				$oid = \indagare\db\CrmDB::addOrder( $uid, $charge, $result['r_approved'], time( ), $order_id, 1, substr( $_POST["cc_num"], - 4 ), $_POST["cc_m"], $_POST["cc_y"] );
				\indagare\db\CrmDB::addLineItem( $oid, $mb->getMembershipPrice( $acc->user->membership_years ) . '00' );

			} catch(\Exception $e) {
				$m = new \indagare\util\IndagareMailer( );
				$thankyou = createThankyouEmail( $acc->user->first_name . " " . $acc->user->last_name, $acc->user->primary_street_address, $acc->user->primary_city, $acc->user->primary_state, $acc->user->primary_postal, $acc->user->primary_country, $acc->user->email, $mb->name . " - " . $acc->user->membership_years . " years PRICE: $" . $mb->getMembershipPrice( $acc->user->membership_years ) . ".00" );
				if ( ! empty( self::$debug_mail ) ) {
					$m->sendHtml( self::$debug_mail_prefix.'Error creating user', $thankyou . " " . $e, self::$debug_mail );
				}
			}
			//echo "1";

			$email_subject = createThankyouEmailSubject();
			$thankyou = createThankyouEmail( $acc->user->first_name . " " . $acc->user->last_name, $acc->user->primary_street_address, $acc->user->primary_city, $acc->user->primary_state, $acc->user->primary_postal, $acc->user->primary_country, $acc->user->email, $mb->name . " - " . $acc->user->membership_years . " years PRICE: $" . $mb->getMembershipPrice( $acc->user->membership_years ) . ".00" );
			$email = $acc->user->email;
			$m = new \indagare\util\IndagareMailer( );
			$m->sendHtml( $email_subject, $thankyou, $email );
			if ( ! empty( self::$debug_mail ) ) {
				$m->sendHtml( self::$debug_mail_prefix.$email_subject, $thankyou, self::$debug_mail );
			}
			if ( ! empty( self::$admin_mail ) ) {
				$m->sendHtml( self::$admin_mail_prefix.$email_subject, $thankyou, self::$admin_mail );
			}
			$payloadurl = "http://".\indagare\config\Config::$payloadserver."/users/$uid/index_user";
			$timeout = 5;
			stream_context_set_default( array( 'http' => array( 'timeout' => $timeout ) ) );
			$payload = @get_headers( $payloadurl );
			/* if($file_headers[0] == 'HTTP/1.0 200 OK')
			 {

			 }
			 else
			 {

			 } */

			$u = \indagare\db\CrmDB::getUserById( $uid );
			$u->startSession( );
			//echo "1";
			//echo "-sso:".$_SESSION["SSODATA"];
			//print "Status: $result[r_approved]<br>\n";
			//echo $payload;

		} else {
			// transaction failed, print the reason
			$m = new \indagare\util\IndagareMailer( );
			$thankyou = createThankyouEmail( $acc->user->first_name . " " . $acc->user->last_name, $acc->user->primary_street_address, $acc->user->primary_city, $acc->user->primary_state, $acc->user->primary_postal, $acc->user->primary_country, $acc->user->email, $mb->name . " - " . $acc->user->membership_years . " years PRICE: $" . $mb->getMembershipPrice( $acc->user->membership_years ) . ".00" );
			if ( ! empty( self::$debug_mail ) ) {
				$m->sendHtml( self::$debug_mail_prefix.'Card Declined', $thankyou . " " . $result["r_approved"] . "-" . $result['r_error'], self::$debug_mail );
			}
			print $result["r_approved"] . "-" . $result['r_error'];
		}
	}

	/**
	 * Handles renewal of existing accounts
	 */
	public static function renew() {
		global $acc;

		$user = \indagare\db\CrmDB::getExtendedUserById( $_POST["userid"] );

		$oldMb = $user->membership_level;

		$user->primary_street_address = $_POST['s_address1'];
		$user->primary_street_address2 = $_POST['s_address2'];
		$user->primary_city = $_POST['s_city'];
		$user->primary_state = $_POST['s_state'];
		$user->primary_postal = $_POST['s_zip'];
		$user->primary_country = $_POST['s_country'];
		$user->membership_years = $_POST['mb_y'];
		$user->membership_level = $_POST['mb'];
		$order_id = time( ) + "_" + rand( 1, 100 );

		$mb = \indagare\db\CrmDB::getMembershipByLevel( $user->membership_level );
		//print $mb->toJSON();
		$charge = $mb->getMembershipPrice( $user->membership_years );
		//print $charge;

		$mylphp = new \lphp( );

		$myorder["host"] = \indagare\config\Config::$pay_host;
		$myorder["port"] = \indagare\config\Config::$pay_port;
		$myorder["keyfile"] = \indagare\config\Config::$pay_key;
		$myorder["configfile"] = \indagare\config\Config::$pay_config;

		// form data
		$myorder["name"] = $_POST["cc_holder"];
		$myorder["cardnumber"] = $_POST["cc_num"];
		$myorder["cardexpmonth"] = $_POST["cc_m"];
		$myorder["cardexpyear"] = $_POST["cc_y"];
		$myorder["cvmindicator"] = "provided";
		$myorder["cvmvalue"] = $_POST["ccv"];
		$myorder["chargetotal"] = $charge;
		$myorder["ordertype"] = "SALE";

		$myorder["oid"] = $order_id;

		$myorder["address1"] = $user->primary_street_address;
		$myorder["address2"] = $user->primary_street_address2;
		$myorder["city"] = $user->primary_city;
		$myorder["state"] = $user->primary_state;
		$myorder["country"] = $user->primary_country;
		$myorder["email"] = $user->email;
		$myorder["zip"] = $user->primary_postal;

		$result = $mylphp->curl_process( $myorder );
		# use curl methods

		if ( $result["r_approved"] == "APPROVED" ) {
			// success
			print $result["r_approved"] . "-" . $result['r_code'] . "-";
			$user->membership_expires_at = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) + $user->membership_years ) );
			//print "create user\n";
			$uid = \indagare\db\CrmDB::updateUserExp( $user );
			$uid = \indagare\db\CrmDB::updateUserMB( $user );

			//print "$uid, create order\n";
			$oid = \indagare\db\CrmDB::addOrder( $uid, $charge, $result['r_approved'], time( ), $order_id, 1, substr( $_POST["cc_num"], - 4 ), $_POST["cc_m"], $_POST["cc_y"] );

			\indagare\db\CrmDB::addLineItem( $oid, $mb->getMembershipPrice( $acc->user->membership_years ) . '00' );

			$email_subject = 'Welcome back to Indagare!';
			if ( $oldMb < $user->membership_level ) {
				$thankyou = createThankyouUpgradeEmail( $user->first_name . " " . $user->last_name, $user->primary_street_address, $user->primary_city, $user->primary_state, $user->primary_postal, $user->primary_country, $user->email, $mb->name . " - " . $user->membership_years . " years PRICE: $" . $mb->getMembershipPrice( $user->membership_years ) . ".00" );
				$email_subject = 'Welcome to a new level of service at Indagare!';
			} else {
				$thankyou = createThankyouRenewEmail( $user->first_name . " " . $user->last_name, $user->primary_street_address, $user->primary_city, $user->primary_state, $user->primary_postal, $user->primary_country, $user->email, $mb->name . " - " . $user->membership_years . " years PRICE: $" . $mb->getMembershipPrice( $user->membership_years ) . ".00" );
			}
			$email = $user->email;
			$m = new \indagare\util\IndagareMailer( );
			$m->sendHtml( $email_subject, $thankyou, $email );
			if ( ! empty( self::$debug_mail ) ) {
				$m->sendHtml( self::$debug_mail_prefix.$email_subject, $thankyou, self::$debug_mail );
			}
			if ( ! empty( self::$admin_mail ) ) {
				$m->sendHtml( self::$admin_mail_prefix.$email_subject, $thankyou, self::$admin_mail );
			}

			//print "Status: $result[r_approved]<br>\n";

		} else {
			// transaction failed, print the reason
			$m = new \indagare\util\IndagareMailer( );
			$thankyou = createThankyouEmail( $acc->user->first_name . " " . $acc->user->last_name, $acc->user->primary_street_address, $acc->user->primary_city, $acc->user->primary_state, $acc->user->primary_postal, $acc->user->primary_country, $acc->user->email, $mb->name . " - " . $acc->user->membership_years . " years PRICE: $" . $mb->getMembershipPrice( $acc->user->membership_years ) . ".00" );
			if ( ! empty( self::$debug_mail ) ) {
				$m->sendHtml( self::$debug_mail_prefix.'Card Declined', $thankyou . " " . $result["r_approved"] . "-" . $result['r_error'], self::$debug_mail );
			}
			print $result["r_approved"] . "-" . $result['r_error'];
		}
	}
}

AjaxHandler::get_instance();

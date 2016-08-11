<?php
namespace indagare\iajax;

use WPSF\Contact;

require_once 'wpdef.php';
include_once 'user.php';
include_once 'db.php';
include_once 'mail.php';
include_once 'Mail.php';
include_once 'lphp.php';
include_once dirname(dirname(__FILE__)).'/resources/emails/thank_you.php';
include_once dirname(dirname(__FILE__)).'/resources/emails/apply.php';
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
		if(function_exists('add_action')) {
			add_action( 'wp_ajax_idj-trial', array( $this, 'chkTrialKey_wp' ) );
			add_action( 'wp_ajax_nopriv_idj-trial', array( $this, 'chkTrialKey_wp' ) );

			add_action( 'wp_ajax_idj-email', array( $this, 'chkEmail_wp' ) );
			add_action( 'wp_ajax_nopriv_idj-email', array( $this, 'chkEmail_wp' ) );

			add_action( 'wp_ajax_idj-login', array( $this, 'chkLogin_wp' ) );
			add_action( 'wp_ajax_nopriv_idj-login', array( $this, 'chkLogin_wp' ) );

			add_action( 'wp_ajax_idj-signup', array( $this, 'payment_wp' ) );
			add_action( 'wp_ajax_nopriv_idj-signup', array( $this, 'payment_wp' ) );

			add_action( 'wp_ajax_idj-renew', array( $this, 'renew_wp' ) );
			add_action( 'wp_ajax_nopriv_idj-renew', array( $this, 'renew_wp' ) );

			add_action( 'wp_ajax_idj-newcontact', array( $this, 'newcontact_wp' ) );
			add_action( 'wp_ajax_nopriv_idj-newcontact', array( $this, 'newcontact_wp' ) );
		}
	}

	private static $cc_types = array(
		'amex' => 'American Express',
		'diners_club_carte_blanche' => 'Diners Club Carte Blanche',
		'diners_club_international' => 'Diners Club International',
		'jcb' => 'JCB',
		'visa_electron' => 'Visa Electron',
		'visa' => 'Visa',
		'mastercard' => 'Mastercard',
		'maestro' => 'Maestro',
		'discover' => 'Discover',
	);

	/**
	 * Validates that the login exists.  Returns a JSON object.
	 */
	public static function chkLogin_wp() {
		header('Content-Type: application/json');
		$response = array();
		try {
			if ( empty( $_POST['login'] ) ) {
				throw new \Exception( 'Empty input', 0 );
			}
			$response['login'] = $_POST['login'];
			$l = sanitize_key( $_POST['login'] );
			$response['exists'] = ( username_exists( $l ) !== false );
		} catch ( \Exception $e ) {
			$response = array(
				'err' => $e->getMessage(),
			);

			$msg = "Response:\r\n" . print_r( $response, true ) . "\r\n\r\n";
			self::_email_debug(__FUNCTION__.': Processing Error', $msg );
			if ( $e->getCode() != 0 ) {
				self::_email_error(__FUNCTION__.': Processing Error', $msg );
			}
		}

		print json_encode( $response );
		exit();
	}

	/**
	 * Validates that the email exists.  Returns a JSON object.
	 */
	public static function chkEmail_wp() {
		header('Content-Type: application/json');
		$response = array();
		try {
			if ( empty( $_POST['email'] ) ) {
				throw new \Exception( 'Empty input', 0 );
			}
			$response['email'] = $_POST['email'];
			$l = sanitize_email( $_POST['email'] );
			$response['exists'] = ( email_exists( $l ) !== false );
		} catch ( \Exception $e ) {
			$response = array(
				'err' => $e->getMessage(),
			);

			$msg = "Response:\r\n" . print_r( $response, true ) . "\r\n\r\n";
			self::_email_debug(__FUNCTION__.': Processing Error', $msg );
			if ( $e->getCode() != 0 ) {
				self::_email_error(__FUNCTION__.': Processing Error', $msg );
			}
		}

		print json_encode( $response );
		exit();
	}

	/**
	 * Validates that a trial key exists, and returns various information
	 * about it.
	 */
	public static function chkTrialKey_wp() {
		header('Content-Type: application/json');
		global $acc;

		$response = array(
				'valid' => false,
				'id' => 0,
				'name' => 'Invalid code',
				'length' => 'Invalid code',
		);

		try {
			if(!isset($_REQUEST["rc"]) || empty($_REQUEST["rc"])) {
				throw new \Exception( 'Empty input', 0 );
			}

			$codes = \WPSF\TrialCode::get_code( $_REQUEST['rc'] );

			if ( is_wp_error( $codes ) || empty( $codes[0]['Id'] ) ) {
				throw new \Exception( 'Invalid code', 0 );
			}

			$response = array(
				'valid' => true,
				'id' => $codes[0]['Id'],
				'name' => $codes[0]['Name'],
				'length' => $codes[0]['Period'],
			);
		} catch( \Exception $e ) {
			$response = array(
				'valid' => false,
				'id' => 0,
				'name' => $e->getMessage(),
				'length' => $e->getMessage(),
				'err' => $e->getMessage(),
			);

			$msg = "Response:\r\n" . print_r( $response, true ) . "\r\n\r\n";
			self::_email_debug( __FUNCTION__ . ': Processing Error', $msg );
			if ( $e->getCode() != 0 ) {
				self::_email_error( __FUNCTION__ . ': Processing Error', $msg );
			}
		}

		print json_encode( $response );
		exit();
	}

	/**
	 * Creates a wordpress account
	 *
	 * @return integer|\WP_Error The ID of the new account, or WP_Error on failure.
	 */
	private static function create_wp_account() {
		$u = new \WP_User();
		$u->user_login = $_POST['username'];
		$u->user_pass = $_POST['password'];
		$u->user_email = $_POST['email'];
		$u->user_firstname = $_POST['fn'];
		$u->user_lastname = $_POST['ln'];
		$u->roles = array('subscriber');
		return wp_insert_user( $u );
	}

	/**
	 * Creates a new contact object from form data.
	 *
	 * @param integer $wpid  The Wordpress account ID for this Contact
	 *
	 * @return \WPSF\Contact The contact object with filled in data.
	 */
	private static function generate_sf_contact( $wpid = null ) {
		// Create and fill in the contact object
		$contact = new \WPSF\Contact();
		$contact['FirstName'] = $_POST['fn'];
		$contact['LastName'] = $_POST['ln'];
		$contact['Phone'] = $_POST['phone'];
		$contact['Email'] = $_POST['email'];
		$contact['WP_Username__c'] = $_POST['username'];
		$contact['WPID__c'] = $wpid;

		if(isset($_POST['s_address1'])) {
			$contact['MailingStreet'] = $_POST['s_address1'];
			$contact['MailingCity'] = $_POST['s_city'];
			$contact['MailingState'] = $_POST['s_state'];
			$contact['MailingPostalCode'] = $_POST['s_zip'];
			$contact['MailingCountry'] = $_POST['s_country'];
		}

		if(isset($_POST['address1'])) {
			$contact['OtherStreet'] = $_POST['address1'];
			$contact['OtherCity'] = $_POST['city'];
			$contact['OtherState'] = $_POST['state'];
			$contact['OtherPostalCode'] = $_POST['zip'];
			$contact['OtherCountry'] = $_POST['country'];
		}

		return $contact;
	}

	/**
	 * Creates a Salesforce Account object (including Contact record) from the
	 * current input form data.
	 *
	 * @param integer $wpid The Wordpress account ID
	 *
	 * @return \WPSF\Account|\WP_Error The account object on success, or WP_Error on failure.
	 */
	private static function create_sf_account( $wpid = null, $trial = null ) {
		$membership = null;
		if ( ! empty( $trial['Membership'] ) ) {
			$membership = $trial['Membership'];
		} else {
			$membership = new \WPSF\Membership( $_POST['l'] );
		}

		// Create and fill in the account object
		$account = new \WPSF\Account();

		$account['Name'] = $_POST['fn'].' '.$_POST['ln'];
		$account['Type'] = $account->picklistValue( 'Type', 'Customer' );

//		$account['RecordTypeId'] = '0121a0000001qM1AAI';

		$account['Email__c'] = $_POST['email'];
		$account['Phone'] = $_POST['phone'];

		$account['Membership__c'] = $membership['Id'];
		$account['Membership_Level__c'] = $account->picklistValue( 'Membership_Level__c', $membership['Membership_Level__c'] );
		$account['Membership_Status__c'] = $account->picklistValue( 'Membership_Status__c', 'Unrenewed' );
		$account['Membership_Start_Date__c'] = date( 'Y-m-d' );
		$account['Member_Since__c'] = date( 'Y-m-d' );
		$account['Is_Renewal__c'] = true;
		$account['Membership_End_Date__c'] = date( 'Y-m-d', strtotime( 'yesterday' ) );

		if(isset($_POST['s_address1'])) {
			$account['BillingStreet'] = $_POST['s_address1'];
			$account['BillingCity'] = $_POST['s_city'];
			$account['BillingState'] = $_POST['s_state'];
			$account['BillingPostalCode'] = $_POST['s_zip'];
			$account['BillingCountry'] = $_POST['s_country'];
		}

		if ( ! empty( $_POST['cc_num'] ) ) {
			$account['Credit_Card_Number__c'] = $_POST['cc_num'];
			$account['Credit_Card_Month__c'] = $_POST['cc_mon'];
			$account['Credit_Card_Year__c'] = $_POST['cc_yr'];
			$account['Card_CVV_Number__c'] = $_POST['cc_cvv'];
			$account['Credit_Card_Type__c'] = self::$cc_types[$_POST['cc_type']];
		}

		$contact = self::generate_sf_contact( $wpid );

		$contact['Primary_Contact__c'] = true;

		$account->add_contact( $contact );

//		var_dump ( $account );

		// Save everything to Salesforce
		return $account->create();
	}

	/**
	 * Handles payment processing and account setup for a paid membership.
	 * Returns status as a JSON object.
	 */
	public static function payment_wp() {
		header('Content-Type: application/json');
		global $acc;

		$response = array(
			'success' => false,
			'r_approved' => '',
			'id' => '',
			'r_ref' => '',
			'length' => '',
			'membertype' => '',
			'name' => '',
			'price' => '',
			'cardnum' => '',
			'cardtype' => '',
			'message' => '',
		);

		$trial = null;
		if ( ! empty( $_POST['trialid'] ) ) {
			$trial = \WPSF\TrialCode::get_code( $_POST['trialid'] );
			if ( empty( $trial ) || is_wp_error( $trial ) )  {
				$trial = null;
			} else {
				$trial = $trial[0];
			}
		}

		$acct_type = 'Membership';
		if ( ! empty( $trial['Id'] ) ) {
			if ( $trial['Type'] == 'Trial' ) {
				$acc_type = 'Trial Membership';
			} else if ( floatval( $trial['Amount'] ) <= 0 ) {
				$acc_type = 'Complementary Membership';
			}
		}

		if ( $_POST['mode'] != 'update' ) {
			// We are creating new.
			$id = self::create_wp_account();
			if ( is_wp_error( $id ) ) {
				print json_encode(array(
					'error' => 'WP user creation failure.',
					'messages' => $id->get_error_messages(),
				));
				exit();
			}

			$aid = self::create_sf_account( $id, $trial );

			if ( is_wp_error( $aid ) ) {
				wp_delete_user( $id );
				print json_encode(array(
					'error'=>'Account creation failure.',
					'messages' => $aid->get_error_messages(),
				));
				exit();
			}
			$account = new \WPSF\Account( $aid );
			$cid = $account['Contacts__x'][0]['Id'];

/*
			$account['recordTypeInfos'][0] = array(
				'available' => true,
				'defaultRecordTypeMapping' => true,
				'name' => 'Member',
				'recordTypeId' => '0121a0000001qM1AAI'
			);
*/

//			$account['recordTypeId'] = '0121a0000001qM1AAI';

			/** I HATE WORKAROUNDS LIKE THIS. Just let me save via the name dammit. **/
			global $wpsf_acf_fields;
			update_field( $wpsf_acf_fields['wpsf_contactid'], $cid, 'user_'.$id );

			wp_signon(array(
				'user_login' => $_POST['username'],
				'user_password' => $_POST['password'],
				'remember' => true,
			));

		} else {
			// We are updating an existing account.
			$account = \WPSF\Contact::get_account_wp();

			if ( ! empty( $_POST['cc_num'] ) && ( strpos( $_POST['cc_num'], '*' ) !== false ) ) {
				// We're updating card info too.  Do that first.
				$account['Credit_Card_Number__c'] = $_POST['cc_num'];
				$account['Credit_Card_Month__c'] = $_POST['cc_mon'];
				$account['Credit_Card_Year__c'] = $_POST['cc_yr'];
				$account['Card_CVV_Number__c'] = $_POST['cc_cvv'];
				$account['Credit_Card_Type__c'] = self::$cc_types[$_POST['cc_type']];
			}

			$acct_type = 'Renewal';
			if ( ! empty( $_POST['l'] ) ) {
				if ( $account['Membership__c'] != $_POST['l'] ) {
					$acct_type = 'Upgrade';
					$account['Membership_Old__c'] = $account['Membership__c'];
					$account['Membership__c'] = $_POST['l'];
				}
			}

			$account->update();
		}

		$charge = \WPSF\Payment::charge_account( $aid, $acct_type );

		$account = new \WPSF\Account( $aid );
		$revert = false;

		if ( is_wp_error( $charge ) ) {
			$response['message'] = 'Error occurred during processing:  '.$charge->get_error_message();
			$response['success'] = false;
			$revert = true;

		} else if ( $charge instanceof \WPSF\Payment ) {
			// Well, the charge made it through the system.  Time to see what's in it.
			if ( is_wp_error( $charge->last_error ) ) {
				// If the last thing was an error, return that.
				$reponse['message'] = 'Error occurred during processing.  '.$charge->get_error_message();
				$response['success'] = false;
				$revert = true;
			} else {
				$new_response = $charge->toResult();
				$response = array_merge( $response, $new_response );
				if ( ! $response['success'] ) {
					$revert = true;
				}

			}
		} else if ( $charge === true ) {
			// Well, the charge made it through the system but returned an account.
			$response['success'] = true;

			if ( ! empty( $account['Membership__x']['Period__c'] ) ) {
				$response['length'] = $account['Membership__x']['Period__c'];
			}

			if ( ! empty( $account['Membership__x']['Membership_Type__c'] ) ) {
				$response['membertype'] = $account['Membership__x']['Membership_Type__c'];
			}

			if ( ! empty( $account['Membership__x']['Name'] ) ) {
				$response['name'] = $account['Membership__x']['Name'];
			}

		} else {
			$response['success'] = false;
			$response['message'] = print_r( $charge, true );
			$revert = true;

		}

		if ( $revert ) {
			$account['Membership__c'] = $account['Membership_Old__c'];
		} else {
			$account['Membership_Status__c'] = $account->picklistValue( 'Membership_Status__c', 'Active' );
		}
		$account['Membership_Old__c'] = '';
		$account->update();

		if ( ! $response['success'] ) {
			return wp_send_json_error( $response );
		}

		return wp_send_json_success( $response );
	}

	/**
	 * Handles payment processing and account setup for a paid membership.
	 * Returns status as a JSON object.
	 */
	public static function renew_wp() {
		header('Content-Type: application/json');
		global $acc;

		$default_args = array(
			'new_level' => '',
		);

		$args = array();
		if ( ! empty( $_POST['l'] ) ) {
			$args['new_level'] = trim( $_POST['l'] );
		}

		$response = array(
			'success' => false,
			'r_approved' => '',
			'id' => '',
			'r_ref' => '',
			'length' => '',
			'membertype' => '',
			'name' => '',
			'price' => '',
			'cardnum' => '',
			'cardtype' => '',
			'message' => '',
			'args' => $args,
		);

		if ( empty( $args['new_level'] ) ) {
			$response = array_merge( $response, array( 'message' => 'Empty membership level' ) );
			return wp_send_json_error( $response );
		}

		// We are updating an existing account.
		$account = \WPSF\Contact::get_account_wp();
		$aid = $account['Id'];
		$acct_type = 'Renewal';

		$account = new \WPSF\Account( $aid );
		$account['Membership_Old__c'] = $account['Membership__c'];
		if ( ! empty( $_POST['l'] ) ) {
			if ( $account['Membership__c'] != $_POST['l'] ) {
				$acct_type = 'Upgrade';
				$account['Membership__c'] = $_POST['l'];
			}
		}

		$response['debug']['post-setup'] = array(
			'Membership__c' => $account['Membership__c'],
			'Membership_Old__c' => $account['Membership_Old__c'],
			'Membership_Status__c' => $account['Membership_Status__c'],
		);

		$account->update();

		$account = new \WPSF\Account( $aid );
		$response['debug']['pre-process'] = array(
			'Membership__c' => $account['Membership__c'],
			'Membership_Old__c' => $account['Membership_Old__c'],
			'Membership_Status__c' => $account['Membership_Status__c'],
		);

		$charge = \WPSF\Payment::charge_account( $aid, $acct_type );

		// Reload the account now (to pick up any changes from the payment process)
		$account = new \WPSF\Account( $aid );
		$revert = false;

		// Debug info
		$response['debug']['post-process'] = array(
			'Membership__c' => $account['Membership__c'],
			'Membership_Old__c' => $account['Membership_Old__c'],
		);

		if ( is_wp_error( $charge ) ) {
			$response['message'] = $charge->get_error_message();
			$response['success'] = false;
			$revert = true;

		} else if ( $charge instanceof \WPSF\Payment ) {
			// Well, the charge made it through the system.  Time to see what's in it.
			if ( is_wp_error( $charge->last_error ) ) {
				// If the last thing was an error, return that.
				$reponse['message'] = 'Error occurred during processing.  '.$charge->get_error_message();
				$response['success'] = false;
				$revert = true;

			} else {
				$new_response = $charge->toResult();
				$response = array_merge( $response, $new_response );
				if ( ! $response['success'] ) {
					$revert = true;
				}
			}
		} else if ( $charge === true ) {
			// Well, the charge made it through the system but returned an account without a payment object.
			// This should happen during trial or complimentary membership processing only.
			$response['success'] = true;

			if ( ! empty( $charge['Membership__x']['Period__c'] ) ) {
				$response['length'] = $account['Membership__x']['Period__c'];
			}

			if ( ! empty( $charge['Membership__x']['Membership_Type__c'] ) ) {
				$response['membertype'] = $account['Membership__x']['Membership_Type__c'];
			}

			if ( ! empty( $charge['Membership__x']['Name'] ) ) {
				$response['name'] = $account['Membership__x']['Name'];
			}
		} else {
			$response['success'] = false;
			$response['message'] = print_r( $charge, true );
			$revert = true;
		}

		if ( $revert ) {
			$account['Membership__c'] = $account['Membership_Old__c'];
		} else {
			$account['Membership_Status__c'] = $account->picklistValue( 'Membership_Status__c', 'Active' );
		}
		$account['Membership_Old__c'] = '';
		$account->update();

		$response['debug']['revert'] = $revert;
		$response['debug']['post-revert'] = array(
			'Membership__c' => $account['Membership__c'],
			'Membership_Old__c' => $account['Membership_Old__c'],
			'Membership_Status__c' => $account['Membership_Status__c'],
		);

		if ( empty( $response['success'] ) ) {
			return wp_send_json_error( $response );
		}

		return wp_send_json_success( $response );
	}

	/**
	 * Handles creating a new contact and account setup.
	 *
	 * @return string JSON string with the status of the
	 */
	public static function newcontact_wp() {
		header('Content-Type: application/json');
		$account = \WPSF\Contact::get_account_wp();

		if ( is_wp_error( $account ) || empty( $account ) ) {
			return wp_send_json_error( $account );
		}

		if ( ! $account->is_wp_primary() ) {
			// Not the primary contact.  Disallow.
			return wp_send_json_error( new \WP_Error('Permission denied') );
		}

		$id = self::create_wp_account();
		if ( is_wp_error( $id ) ) {
			return wp_send_json_error( new \WP_Error('WP User creation failed') );
		}

		// Create and fill in the contact object
		$contact = self::generate_sf_contact( $id );
		$contact['Primary_Contact__c'] = false;
		$contact['AccountId'] = $account['Id'];

		// Save everything to Salesforce
		$cid = $contact->create();
		if ( is_wp_error( $cid ) ) {
			wp_delete_user( $id );
			return wp_send_json_error( new \WP_Error('Contact creation failed') );
		}

		/** I HATE WORKAROUNDS LIKE THIS. Just let me save via the name dammit. **/
		global $wpsf_acf_fields;
		update_field( $wpsf_acf_fields['wpsf_contactid'], $cid, 'user_'.$id );

		$contact = new \WPSF\Contact( $cid );
		return wp_send_json_success( $contact->toArray(false) );
	}
}

AjaxHandler::get_instance();

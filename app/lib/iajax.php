<?php
namespace indagare\iajax;

use WPSF\Contact;

class AjaxHandler {
	/**
	 * Holds the instance of this plugin (once initialized)
	 * @var \indagare\iajax\AjaxHandler
	 */
	private static $instance = null;

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

			add_action( 'wp_ajax_idj-invite', array( $this, 'invite_wp' ) );
			add_action( 'wp_ajax_nopriv_idj-invite', array( $this, 'invite_wp' ) );
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
			'payment' => false,
			'amount' => 0,
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
				'trialname' => $codes[0]['Name'],
				'name' => $codes[0]['Membership']['Name'],
				'length' => $codes[0]['Period'],
				'payment' => ( empty( $codes[0]['Payment'] ) ? false : true ) ,
				'amount' => $codes[0]['Amount'],
				'pagetext' => $codes[0]['Page_Text__c'],
			);
			
		} catch( \Exception $e ) {
			$response = array(
				'valid' => false,
				'id' => 0,
				'name' => $e->getMessage(),
				'length' => $e->getMessage(),
				'err' => $e->getMessage(),
				'payment' => false,
				'amount' => 0,
				'pagetext' => '',
			);
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
		if ( ! empty( $_POST['cid'] ) ) {
			$c = new \WPSF\Contact( $_POST['cid'] );
			if ( empty( $c ) ) {
				return new \WP_Error('Error getting Contact information');
			}
			$email = $c->Email;
			$fn = $c->FirstName;
			$ln = $c->LastName;
		} else {
			$email = $_POST['email'];
			$fn = $_POST['fn'];
			$ln = $_POST['ln'];
		}

		$u = new \WP_User();
		$u->user_login = $_POST['username'];
		$u->user_pass = $_POST['password'];
		$u->user_email = $email;
		$u->user_firstname = $fn;
		$u->user_lastname = $ln;
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
		//$contact['WPID__c'] = $wpid;

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
		$account['Account_Alias__c'] = $_POST['fn'];
		$account['Self_Signup__c'] = true;
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

		if ( isset( $_POST['hearabout'] ) && ! empty( $_POST['hearabout'] ) ) {
			$account['How_Did_You_Hear_About_Us__c'] = $_POST['hearabout'];
		}
		if ( isset( $_POST['referby'] ) && ! empty( $_POST['referby'] ) ) {
			$account['Referred_By_non_lookup__c'] = $_POST['referby'];
		}
		if ( ! empty( $trial['Code'] ) ) {
			$account['Sign_Up_Promo_Code__c'] = $trial['Code'];
		}
		
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
		$user_signon = null;

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
				self::slack( 'AJAX Create: Error creating WordPress user account.', 'error' );
				exit();
			}

			$aid = self::create_sf_account( $id, $trial );

			if ( is_wp_error( $aid ) ) {
				wp_delete_user( $id );
				print json_encode(array(
					'error'=>'Account creation failure.',
					'messages' => $aid->get_error_messages(),
				));
				self::slack( 'AJAX Create: Error creating Salesforce account.', 'error' );
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

			$user_signon = wp_signon(array(
				'user_login' => $_POST['username'],
				'user_password' => $_POST['password'],
				'remember' => true,
			));
			wp_set_current_user( $id );

		} else {
			// We are updating an existing account.
			// I'm not sure we ever get here with the current logic, but it's here just in case.
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
				if ( ( $_POST['l'] != $account['Membership__c'] ) && $account->is_active() ) {
					$acct_type = 'Upgrade';
					$account['Membership_Old__c'] = $account['Membership__c'];
				}
				$account['Membership__c'] = $_POST['l'];
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
				if ( empty( $response['success'] ) ) {
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

		if ( $revert && ! empty( $account['Membership_Old__c'] ) ) {
			$account['Membership__c'] = $account['Membership_Old__c'];
		} else {
			$account['Membership_Status__c'] = $account->picklistValue( 'Membership_Status__c', 'Active' );
		}
		$account['Membership_Old__c'] = '';
		if ( ! empty( $trial["Name"] ) && ! empty( $trial["Gift_Credit__c"] ) ) {
			$account["Gift_Balance__c"] = $trial["Gift_Credit__c"];
			$account["Gifted_From__c"] = $trial["Name"];
		}
		$account->update();

		// Do this so we reduce the log output on initial creation
		global $WPSF_NewUserProcessing;
		$WPSF_NewUserProcessing = true;
		wpsf_apply_roles( $user_signon, $account, true );

		if ( ! $response['success'] ) {
			self::slack( 'AJAX Create: '.$response['message'], 'error' );
			return wp_send_json_error( $response );
		}

		return wp_send_json_success( $response );
	}

	/**
	 * Handles account setup for a paid membership that has an invite on it.
	 * Returns status as a JSON object.
	 */
	public static function invite_wp() {
		header('Content-Type: application/json');
		global $acc;

		$response = array(
			'success' => true,
		);

		$cid = $_POST['cid'];
		$c = new \WPSF\Contact( $cid );

		// We are creating new.
		$id = self::create_wp_account();
		if ( is_wp_error( $id ) ) {
			print json_encode(array(
				'error' => 'WP user creation failure.',
				'messages' => $id->get_error_messages(),
			));
			exit();
		}

		/** I HATE WORKAROUNDS LIKE THIS. Just let me save via the name dammit. **/
		global $wpsf_acf_fields;
		update_field( $wpsf_acf_fields['wpsf_contactid'], $cid, 'user_'.$id );

		wp_signon( array(
			'user_login' => $_POST['username'],
			'user_password' => $_POST['password'],
			'remember' => true,
		) );
		wp_set_current_user( $id );

		$c['WP_Username__c'] = $_POST['username'];
		$c->update();

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

		$account = \WPSF\Contact::get_account_wp();
		if ( empty( $account ) || is_wp_error( $account ) ) {
			$response = array_merge( $response, array( 'message' => __( 'Error retrieving account information. Please call for support.', 'indagare' ) ) );
			self::slack( 'AJAX Renew: Error loading Account object for current user.', 'moneyfail' );
			return wp_send_json_error( $response );
		}

		if ( empty( $args['new_level'] ) ) {
			$args['new_level'] = $account['Membership__c'];
		}

		if ( empty( $args['new_level'] ) ) {
			$response = array_merge( $response, array( 'message' => __( 'Unknown membership level. Please choose a different level or call for support.', 'indagare' ) ) );
			self::slack( 'AJAX Renew: Unknown membership level `' . $args['new_level'] . '`', 'moneyfail' );
			return wp_send_json_error( $response );
		}

		$m = new \WPSF\Membership( $args['new_level'] );
		if ( empty( $m['Listed_for_sale__c'] ) || ( $m['Listed_for_sale__c'] != '1' ) ) {
			$response = array_merge( $response, array( 'message' => __( 'Your existing Memership level cannot be renewed. Please choose a different level or call for support.', 'indagare' ) ) );
			self::slack( 'AJAX Renew: Memership renewal denied by sellable flag. MembershipID=`' . $args['new_level'] . '` Sellable=`'.$m['Listed_for_sale__c'].'`', 'moneyfail' );
			return wp_send_json_error( $response );
		}

		if ( empty( $account['Credit_Card_Number__c'] ) ||
			empty( $account['Credit_Card_Month__c'] ) ||
			empty( $account['Credit_Card_Year__c'] ) ||
			empty( $account['Card_CVV_Number__c'] ) ||
			empty( $account['Credit_Card_Type__c'] ) ||
			empty( $account['BillingStreet'] ) ||
			empty( $account['BillingCity'] ) ||
			empty( $account['BillingState'] ) ||
			empty( $account['BillingPostalCode'] ) ||
			empty( $account['BillingCountry'] ) ) {
				$response = array_merge( $response, array( 'message' => __( 'You must update the credit card on file before upgrading or renewing.', 'indagare' ) ) );
			self::slack( 'AJAX Renew: No CC or Billing on File.', 'moneyfail' );
			return wp_send_json_error( $response );
		}

		// We are updating an existing account.
		$aid = $account['Id'];
		$acct_type = 'Renewal';

		$response['debug']['pre-setup'] = array(
			'Membership__c' => $account['Membership__c'],
			'Membership_Old__c' => $account['Membership_Old__c'],
			'Membership_Status__c' => $account['Membership_Status__c'],
		);

		if ( ! empty( $args['new_level'] ) ) {
			if ( ( $args['new_level'] != $account['Membership__c'] ) && $account->is_active() ) {
				$acct_type = 'Upgrade';
				$account['Membership_Old__c'] = $account['Membership__c'];
			}
			$account['Membership__c'] = $args['new_level'];
		}

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
				if ( empty( $response['success'] ) ) {
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

		if ( $revert && ! empty( $account['Membership_Old__c'] ) ) {
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
			self::slack( 'AJAX Renew: ' . $response['message'], 'moneyfail' );
			return wp_send_json_error( $response );
		}

		return wp_send_json_success( $response );
	}

	/**
	 * Send a message via Slack (if possible)
	 * @param unknown $message
	 * @param string $type
	 */
	private static function slack( $message, $type = 'error' ) {
		if ( class_exists( 'Slack' ) ) {
			return Slack::send( $message, $type );
		}
		if ( class_exists( '\WPSF\Slack' ) ) {
			return \WPSF\Slack::send( $message, $type );
		}
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

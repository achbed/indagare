<?php namespace indagare\wp;

include_once 'user.php';
include_once 'db.php';

class WPContent {

	public static function getContent($page) {
		switch ($page) {
			case "signup" :
				return WPContent::getSignup();
			case "account" :
				return WPContent::getAccount();
			default :
				return "Content not available";
		}
	}

	private static function getAccount() {
		$wp_userid = get_current_user_id();

		$content = "";

		$var_user = array();
		$var_userExt = array();
		$var_userFFAs = array();
		$var_spouse = array();
		$var_spouseFfa = array();

		$sfid = get_field( 'wpsf_accountid', 'user_' . $wp_userid );

		print_r(array(
			'$wp_userid' => $wp_userid,
			'$sfid' => $sfid,
		));

		if ( ! empty( $sfid ) && class_exists( '\WPSF\Account' ) ) {
			// Salesforce!


			$account = new \WPSF\Account( $sfid );

			$contacts = array_keys( $account->Contacts__x );
			if ( ! empty( $contacts[0] ) ) {
				$contact = $account->Contacts__x[$contacts[0]];
			}

			$passports = array();
			foreach ( $contact->Passport_Visa__x as $k=>$v ) {
				$passports[] = $v->toArray();
			}


			// @TODO: Fix this!
			$memberlevel = 2;
			$var_user = array(
				'crmId' => $sfid,
				'data' => $account->toArray(),
				'fname' => $contact->FirstName,
				'lname' => $contact->LastName,
				'title' => $contact->Title,
				'email' => $contact->Email,
				'initial' => '',
				'prefix' => $contact->Salutation,
				'addr1' => $contact->MailingStreet,
				'addr2' => '',
				'city' => $contact->MailingCity,
				'state' => $contact->MailingState,
				'postal' => $contact->MailingPostalCode,
				'country' => $contact->MailingCountry,
				'mb' => $memberlevel,
				'mb_exp' => $account->Membership_End_Date__c,
				'phone_h' => $contact->HomePhone,
				'phone_w' => $contact->Phone,
				'phone_m' => $contact->MobilePhone,
				'passports' => $passports,
			);

			$var_userExt = array(
				'birthday' => $contact->Member_Birthday__c,
				'assistent' => $contact->AssistantName,
				'assistentEmail' => $contact->Assistant_Email__c,
				'assistentPhone' => $contact->AssistantPhone,
				'passport' => '',
				'contact_pref' => $contact->Preferred_Method_of_Contact__c,
				'delivery_pref' => $contact->Preferred_Format_to_Receive_Itineraries__c,
			);

			foreach ( $contact->Frequent_Travel__x as $k => $v ) {
				$ff = $v->toArray();
				$ff = array_merge( array(
					'id' => $k,
					'a' => $v->Frequent_Traveler_Program__c,
					'n' => $v->Frequent_Flyer_Number__c,
				), $ff );
				$var_userFFAs[] = $ff;
			}
		} else if ( ! \indagare\users\User::hasUserSession() ) {
				$wpuser = new \WP_User( $wp_userid );
				$var_user = array(
					'crmId' => -1,
					'fname' => $wpuser->first_name,
					'lname' => $wpuser->last_name,
					'title' => '',
					'email' => $wpuser->user_email,
					'initial' => '',
					'prefix' => '',
					'addr1' => '',
					'addr2' => '',
					'city' => '',
					'state' => '',
					'postal' => '',
					'country' => '',
					'mb' => '',
					'mb_exp' => '',
					'phone_h' => '',
					'phone_w' => '',
					'phone_m' => '',
				);
		} else {
			$userid = \indagare\users\User::getSessionUserID();
			$crmuser = \indagare\db\CrmDB::getExtendedUserById($userid);
			$userExt = \indagare\db\LocalCrmDB::getUser($userid);
			$expDate = date('m/d/Y', strtotime($crmuser->membership_expires_at));

			$var_user = array(
				'crmId' => $userid,
				'fname' => $crmuser->first_name,
				'lname' => $crmuser->last_name,
				'title' => $crmuser->prefix,
				'email' => $crmuser->email,
				'initial' => $crmuser->middle_initial,
				'prefix' => $crmuser->prefix,
				'addr1' => $crmuser->primary_street_address,
				'addr2' => $crmuser->primary_street_address2,
				'city' => $crmuser->primary_city,
				'state' => $crmuser->primary_state,
				'postal' => $crmuser->primary_postal,
				'country' => $crmuser->primary_country,
				'mb' => $crmuser->membership_level,
				'mb_exp' => $expDate,
				'phone_h' => $crmuser->phone_home,
				'phone_w' => $crmuser->phone_work,
				'phone_m' => $crmuser->phone_mobile,
			);

			if (isset($userExt['id'])){
				$var_userExt = array(
					'birthday' => $userExt["birthday"],
					'assistent' => $userExt["assistent_name"],
					'assistentEmail' => $userExt["assistent_email"],
					'assistentPhone' => $userExt["assistent_phone"],
					'passport' => $userExt["passport_country"],
					'contact_pref' => $userExt["contact_pref"],
					'delivery_pref' => $userExt["delivery_pref"],
				);

				$userFFAs = \indagare\db\LocalCrmDB::getFFAccounts($userExt["id"], 1);
				for ($i = 0; $i < count($userFFAs); $i++) {
					if(!empty($userFFAs[$i]["id"])) {
						$var_userFFAs[] = array(
							'id' => $userFFAs[$i]["id"],
							'a' => $userFFAs[$i]["airline"],
							'n' => $userFFAs[$i]["ff_number"],
						);
					}
				}

				$spouseArr = \indagare\db\LocalCrmDB::getFamilyMembers($userid, 1);
				if (count($spouseArr) > 0) {
					$var_spouse = array(
						'name' => $spouseArr[0]["name"],
						'birthday' => $spouseArr[0]["birthday"],
						'email' => $spouseArr[0]["email"],
						'passport' => $spouseArr[0]["passport_country"],
					);
					$var_spouseFfa = array();
					$spouseFFAArr = \indagare\db\LocalCrmDB::getFFAccounts($spouseArr[0]["id"], 2);
					foreach ($spouseFFAArr as $sFfa) {
						if(!empty($sFfa["id"])) {
							$var_spouseFfa[] = array(
								'id' => $sFfa["id"],
								'a' => $sFfa["airline"],
								'n' => $sFfa["ff_number"],
							);
						}
					}
				}

				$childrenArr = \indagare\db\LocalCrmDB::getFamilyMembers($userid, 2);
				if (count($childrenArr) > 0) {
					for ($i = 0; $i < count($childrenArr); $i++) {
						if(!empty($childrenArr[$i]["id"])) {
							$var_children_item = array(
								'id' => $childrenArr[$i]["id"],
								'name' => $childrenArr[$i]["name"],
								'birthday' => $childrenArr[$i]["birthday"],
								'ffa' => array(),
							);

							$childrenFfaArr = \indagare\db\LocalCrmDB::getFFAccounts($childrenArr[$i]["id"], 2);

							foreach($childrenFfaArr as $childrenFfaArrItem) {
								if(empty($childrenFfaArrItem["id"])) {
									continue;
								}
								$var_children_item['ffa'][] = array(
									"id" => $childrenFfaArrItem["id"],
									"ff_number" => $childrenFfaArrItem["ff_number"],
									"a" => $childrenFfaArrItem["airline"]
								);
							}
							$var_children[] = $var_children_item;
						}
					}
				}


				$prefs = \indagare\db\LocalCrmDB::getPreferences($userid);
				$var_userPrefs = array();
				foreach ($prefs as $pref) {
					$var_userPrefs[] = array(
						'pref' => $pref["preference"],
						'value' => $pref["value"],
					);
				}
			}
		}

		$memberships = \indagare\db\CrmDB::getMemberships();
		$var_mbs = array();
		foreach( $memberships as $m ) {
			$var_mbs[] = $m;
		}


		$content .= "<script type='text/javascript'>\n";

		$content .= "var user=" . json_encode($var_user) . ";\n";
		$content .= "var userExt=" . json_encode($var_userExt) . ";\n";
		$content .= "var userFFAs=" . json_encode($var_userFFAs) . ";\n";

		$content .= "var spouse=" . json_encode($var_spouse) . ";\n";
		$content .= "var spouseFfa=" . json_encode($var_spouseFfa) . ";\n";

		$content .= "var children=" . json_encode($var_children) . ";\n";

		$content .= "var userPrefs=" . json_encode($var_userPrefs) . ";\n";

		$content .= "var mbs = " . json_encode( $var_mbs ) . ";\n";

		$content .= "</script>\n";

		$content = $content . file_get_contents( $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/indagare/app/resources/account.html');

		return $content;
	}

	private static function getSignup() {

		$memberships = \indagare\db\CrmDB::getMemberships();

		$discount = 0;
		$discount_obj = \indagare\users\Discount::findDiscount();
		if ( $discount_obj->is_valid() ) {
			$discount = $discount_obj->percent;
		}


		$mb_js_arr = array();
		for ( $i = 0; $i < count($memberships); $i++) {
			$memberships[$i]->discount = $discount->percent;
			$mb_js_arr[] = $memberships[$i];
		}
		$mb_js_arr = json_encode( $mb_js_arr );
		$acc = \indagare\users\AccountCreator::getAccountCreator();
		$mb = "1";
		$rc = "";
		$reftype = 0;
		if (isset($_GET["mb"])) {
			$mb = sanitize_text_field( urldecode( $_GET["mb"] ) );
		}

		$showTrial = false;
		$trial = "";
		if (isset($_GET["trial"])) {
			$showTrial = true;
			if(!empty($_GET["trial"])) {
				// Do a string replace to ensure that we don't have JS crashing due to bad input
				$trial = sanitize_text_field( urldecode( $_GET['trial'] ) );
				$trial = str_replace('"', '\"', trim( $trial ) );
			}
		}
		else {
			//echo "mb: " . $acc->user->membership_level;
			if ($acc->user->membership_level != "trial"){
				//$mb = $acc->user->membership_level;
			}
		}
		$mb_y = 0;
		if ($acc->user->membership_years != "trial") {
			$mb_y = $acc->user->membership_years;
		}
		if (isset($_GET["referralcode"])) {
			$rc = sanitize_text_field( urldecode( $_GET["referralcode"] ) );
			if ("TRIAL-B" == substr($rc, 0, 7)) {
				$reftype = 1;
			}
		}
		//echo $acc->user->toString();
		$getstrings=array("pc","gdsType","cin","cout");
		$content = "<script type='text/javascript'>
			var rc = '" . $rc . "';
			var mb = " . $mb . ";
			var dc = " . intval( $discount_obj->percent ) . ";
			var dcode = '" . str_replace("'","\'",$discount_obj->code) . "';
			var dc_msg = '" . str_replace("'","\'",$discount_obj->description) . "';
			var trialCode = '" . $trial . "';
			var showTrial = " . ( $showTrial ? 'true' : 'false' ) . ";
			var reftype = " . $reftype . ";
			var y = " . $mb_y . ";
			var mbs = " . $mb_js_arr . ";
			var acc = " . $acc->user->toJSON() . ";
			var reg = new RegExp('(^|&)source=([^&]*)(&|$)');
			var r = window.location.search.substr(1).match(reg);
			if (r!=null)
			{
			 var redirect=unescape(r[2]);
			}
			else
			{
			 var redirect='';
			};
			";
		//$content.="var ";
		$content.="var swifttriparm={};
		";
		foreach ($getstrings as $keyget => $valueget)
		{
			if (isset($_GET[$valueget]))
			{
			  $content.="swifttriparm['".$valueget."']='".$_GET[$valueget]."';
			  ";
			}

		}
		$content.="</script>\n";

		$content = $content . file_get_contents( $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/indagare/app/resources/signup.html');

		return $content;
	}
}

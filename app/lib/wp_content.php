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
		$content = file_get_contents( $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/indagare/app/resources/account.html');
		return $content;
	}

	private static function getSignup() {

		$acc = \indagare\users\AccountCreator::getAccountCreator();
		$discount = 0;
		$mb = "1";
		$rc = "";
		$reftype = 0;

		$showTrial = false;
		$trial = "";
		if (isset($_GET["trial"])) {
			$showTrial = true;
			if(!empty($_GET["trial"])) {
				// Do a string replace to ensure that we don't have JS crashing due to bad input
				$trial = sanitize_text_field( urldecode( $_GET['trial'] ) );
				$trial = str_replace('"', '\"', trim( $trial ) );
			}
			$mb_js_arr = \WPSF\TrialCode::get_all();
		} else {
			$mb_js_arr = \WPSF\Membership::get_all();
		}
		if ( is_wp_error( $mb_js_arr ) ) {
			$mb_js_arr = array();
		}
		$mb_js_arr = json_encode( $mb_js_arr );

		$mb_y = 0;
		/*
		if (isset($_GET["referralcode"])) {
			$rc = sanitize_text_field( urldecode( $_GET["referralcode"] ) );
			if ("TRIAL-B" == substr($rc, 0, 7)) {
				$reftype = 1;
			}
		}*/
		//echo $acc->user->toString();
		$getstrings=array("pc","gdsType","cin","cout");
		$content = "<script type='text/javascript'>
			var trialCode = '" . $trial . "';
			var showTrial = " . ( $showTrial ? 'true' : 'false' ) . ";
			var reftype = " . $reftype . ";
			var y = " . $mb_y . ";
			var mbs = " . $mb_js_arr . ";
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
			var swifttriparm={};
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

<?php namespace indagare\wp;

include_once 'user.php';
include_once 'db.php';

class WPContent {
	/**
	 * Reads the given resource file from the resources folder, and returns
	 * it as a single string.
	 *
	 * @param string $file The name of the resource file (without any path).
	 *
	 * @return string The contents of the resource file or false on failure.
	 */
	private static function get_resource( $file, $folder = 'resources' ) {
		$path = dirname( __DIR__ ) . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $file;

		if ( ! file_exists( $path ) ) {
			return '<!-- missing '. $path.' -->';
		}

		$r = file_get_contents( $path );

		if( $r === false ) {
			$r = '';
		}

		return $r;
	}

	/**
	 * Returns HTML content for a given page
	 *
	 * @param string $page The page to retrieve.  Currently supports "signup", "account", or "invite".
	 *
	 * @return string The HTML content.
	 */
	public static function getContent($page) {
		print '<!-- '.__FUNCTION__.' called -->';
		switch ($page) {
			case "signup" :
				return WPContent::getSignup();
			case "account" :
				return WPContent::getAccount();
			case "invite" :
				return WPContent::getInvite();
			default :
				return "Content not available";
		}
	}

	/**
	 * Returns the HTML for the Site Invite form.
	 *
	 * @return string The HTML content.
	 */
	private static function getInvite() {
		$content = '';

		if ( empty( $_GET['c'] ) || empty( $_GET['h'] ) ) {
			// No data.  Redirect to ..... homepage?
			wp_redirect( '/' );
			return '';
		}

		$cid = $_GET['c'];
		$hash = $_GET['h'];
		$c = new \WPSF\Contact( $cid );
		if ( $hash != $c->get_invite_hash() ) {
			// Hash error. Redirect to ..... homepage for now, we need to
			// build an error page
			wp_redirect( '/' );
			return '';
		}

		$content .= self::get_resource('invite.html');

		return $content;
	}

	private static function getAccount() {
		print '<!-- '.__FUNCTION__.' called -->';
		$json_mode = 0;//JSON_PRETTY_PRINT;
		$content = "<script>\n";

		$a = \WPSF\Contact::get_account_wp();
		if(empty($a)) {
			$a = array('Membership__x'=>array(),'Contacts__x'=>array());
		} else {
			$a->filter_contacts();
			$a = $a->toArray(false);
			wpsf_wp_login();
		}

		$content .= "var SFData = {};\n";
		$content .= "SFData.Account = " . json_encode( $a, $json_mode ) . ";\n";
		$content .= "SFData.Membership = SFData.Account.Membership__x;\n";
		$content .= "SFData.Contacts = SFData.Account.Contacts__x;\n";
		$content .= "SFData.initLoad = true;\n";

		$a = \WPSF\Membership::get_sellable();
		usort( $a, function($a,$b) { return \WPSF\Membership::cmp_list($a,$b); } );

		$content .= "SFData.MembershipList = " . json_encode( $a, $json_mode ) . ";\n";

		$content .= "SFData.def={\n";

		$a = new \WPSF\Account();
		$content .= "Account:" . json_encode( $a->toArray(), $json_mode ) . ",\n";

		$a = new \WPSF\Contact();
		$content .= "Contact:" . json_encode( $a->toArray(), $json_mode ) . ",\n";

		$a = new \WPSF\PassportVisa();
		$content .= "PassportVisa:" . json_encode( $a->toArray(), $json_mode ) . ",\n";

		$a = new \WPSF\FrequentTravel();
		$content .= "FrequentTravel:" . json_encode( $a->toArray(), $json_mode ) . ",\n";

		$a = new \WPSF\Membership();
		$content .= "Membership:" . json_encode( $a->toArray(), $json_mode ) . ",\n";

		$a = \WPSF\Countries::countryPicklistValues();
		$content .= "Countries:" . json_encode( $a, $json_mode ) . ",\n";

		$a = \WPSF\Countries::statePicklistValues();
		$content .= "States:" . json_encode( $a, $json_mode ) . "\n";

		$content .= "};\n";
		$content .= "</script>\n";

		//$content .= file_get_contents( $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/indagare/app/resources/account.html');
		$content .= self::get_resource('account.html');

		$front = get_option('page_on_front');
		$rows = get_field('home-featured', $front );

		if($rows) {
			$content .= '<section class="related-articles contain" id="articles-for-dashboard" style="display:none";>'."\n";

			$count = 0;
			foreach($rows as $row) {
				$count++;
				if($count > 4) break;

						$imageobj = $row['home-featured-image'];
						$image = $imageobj['sizes']['thumb-large'];

						$content .= '<article>'."\n";
							$content .= '<a href="'.$row['home-featured-url'].'">'."\n";
								if ( $image ) {
									$content .= '<img src="'.$image.'" alt="Related" />'."\n";
								}
								$content .= '<span class="info">'."\n";
									$content .= '<h4>'.$row['home-featured-heading'].'</h4>'."\n";
									$content .= '<h3>'.$row['home-featured-title'].'</h3>'."\n";
								$content .= '</span><!-- .info -->'."\n";
							$content .= '</a>'."\n";
						$content .= '</article>'."\n";
			}

			$content .= '</section>'."\n";
		}

		$content .= "<script>jQuery('#articles-for-dashboard').appendTo('#dashboard>div').show();</script>";

		return $content;
	}

	private static function getSignup() {
		print '<!-- '.__FUNCTION__.' called -->';
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
			$mb_js_arr = \WPSF\Membership::get_sellable();
		}
		if ( is_wp_error( $mb_js_arr ) ) {
			$mb_js_arr = array();
		}
		$mb_js_arr = json_encode( $mb_js_arr );

		$mb_y = 0;
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

		//$content = $content . file_get_contents( $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/indagare/app/resources/signup.html');
		$content .= self::get_resource('signup.html');

		return $content;
	}
}

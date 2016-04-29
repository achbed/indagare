<?php
require_once WP_CONTENT_DIR . '/indagare_config.php';
include_once './user.php';
include_once './db.php';

if ( isset( $_REQUEST['submit'] ) && $_REQUEST['submit'] == "yes" ) {
	$u = indagare\db\CrmDB::getUser($_POST['externaluser']);
	if ( $u != false ) {
		if ( $u->validatePwd( $_POST['externalpassword'] ) ) {
     $u->startSession();
     $getarray=array("pc","gdsType","cin","cout");
     $_POST['ssoToken']=$_SESSION['SSODATA'];
			$url_prefix = \indagare\config\Config::$swifttrip_url . "/do/hotel/CheckHotelAvailability";
     $url=$url_prefix."?";
			foreach ( $_POST as $keypost => $valuepost ) {
         if ($keypost !== "externaluser" && $keypost !== "externalpassword"){
            $url.=$keypost."=".$valuepost."&";
         }
     }
     $url=substr($url,0,-1);
     header("Location: ".$url);
			exit();
    }
	}
	header( "Location: " . \indagare\config\Config::$external_login_redirect );
	exit();
}


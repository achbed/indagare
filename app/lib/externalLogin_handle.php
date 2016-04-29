<?php
require_once WP_CONTENT_DIR . '/indagare_config.php';
include_once './user.php';
include_once './db.php';

if ( isset( $_REQUEST['submit'] ) && $_REQUEST['submit'] == "yes" ) {
	$return=array();
	$u = indagare\db\CrmDB::getUser($_POST['username']);
	if ( $u->validatePwd( $_POST['password'] ) ) {
     $u->startSession();
     //echo "1";
     $getarray=array("pc","gdsType","cin","cout");
     $_POST['ssoToken']=$_SESSION['SSODATA'];
		$url_prefix = \indagare\config\Config::$swifttrip_url . "/do/hotel/CheckHotelAvailability";
     $url=$url_prefix."?";
		foreach ( $_POST as $keypost => $valuepost ) {
     	$url.=$keypost."=".$valuepost."&";
     }
     $url=substr($url,0,-1);
     //echo $url;
     $return['url']=$url;
     echo json_encode($return);
	} else {
     echo json_encode(array('url'=>""));
    }
}


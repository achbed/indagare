<?php
$http_origin = $_SERVER['HTTP_ORIGIN'];

if (stripos($http_origin,$_SERVER['SERVER_NAME']) !== false ) {
    header("Access-Control-Allow-Origin: $http_origin");
}
include_once 'app/lib/user.php';
include_once 'app/lib/db.php';
if (isset($_POST["usr"]) && isset($_POST["pwd"])) {
    $u = indagare\db\CrmDB::getUser($_POST["usr"]);
    if($u != false && $u->validatePwd($_POST["pwd"])) {
       $u->startSession();

		header('Content-Type', 'application/json');
		echo json_encode(array(
			'login' => true,
			'ssotoken' => $_SESSION["SSODATA"]
		));

    }
    else {
		header('Content-Type', 'application/json');
		echo json_encode(array(
			'login' => false,
			'ssotoken' => ''
		));
    }
}

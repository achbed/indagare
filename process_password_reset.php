<?php
include_once 'app/lib/user.php';
if (isset($_POST["email"]) ) {

    $email = \indagare\users\User::requestPwdReset($_POST["email"]);

    if($email) {

		header('Content-Type', 'application/json');
		echo json_encode(array(
			'email' => true
		));

    }
    else {
		header('Content-Type', 'application/json');
		echo json_encode(array(
			'email' => false
		));
    }
	

}
?>
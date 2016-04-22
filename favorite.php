<?php 
include_once 'app/lib/user.php';
include_once 'app/lib/db.php';

	$action = $_GET['action'];
	$postid = $_GET['postid'];
	$userid = indagare\users\User::getSessionUserID();
	
	if ( $action == 'add' ) {
	
		 indagare\users\User::addFavorite($postid);
	
	}
	
	if ( $action == 'remove' ) {

		 indagare\users\User::removeFavorite($userid,$postid);
	
	}

    header('Location: ' . $_SERVER["HTTP_REFERER"]);

?>
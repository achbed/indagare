<?php

include_once 'user.php';
include_once 'db.php';

foreach ($_POST as $key => $value) {
    echo $key . ' : ' . $value . '<br>'; 
}

 $userid = \indagare\users\User::getSessionUserID();
 $user = \indagare\db\CrmDB::getExtendedUserById($userid);
 
 $user->prefix = $_POST["prefix"];
 $user->first_name = $_POST["fn"];
 $user->middle_initial = $_POST["initial"];
 $user->last_name = $_POST["ln"];
 $user->email = $_POST["email"];
 $user->primary_street_address = $_POST["s_address1"];
 $user->primary_street_address2 = $_POST["s_address2"];
 $user->primary_city = $_POST["s_city"];
 $user->primary_state = $_POST["s_state"];
 $user->primary_postal = $_POST["s_zip"];
 $user->primary_country = $_POST["s_country"];
 $user->phone_home = $_POST["phone"];
 $user->phone_work = $_POST["phone_w"];
 $user->phone_mobile = $_POST["phone_m"];
 
 \indagare\db\CrmDB::updateUserAccountInfo($user);
 
 $redirect = "https://".$_SERVER['HTTP_HOST']."/account/";
 header("Location: $redirect");
<?php

include_once 'user.php';
include_once 'db.php';

/*foreach ($_POST as $key => $value) {
    echo $key . ' : ' . $value . '<br>';  
}*/
//print_r($_POST); 
$userid = \indagare\users\User::getSessionUserID();
$userArr = \indagare\db\LocalCrmDB::getUser($userid);

foreach ($_POST as $key => $value) {
    \indagare\db\LocalCrmDB::setPreference($key, $value, $userid);
}

// tw1 - 7
for ($i = 1; $i <= 7; $i++){
if ($_POST["tw" . $i]){
    \indagare\db\LocalCrmDB::setPreference("tw" . $i, "on", $userid);
}
else 
    \indagare\db\LocalCrmDB::setPreference("tw" . $i, "off", $userid);
}

//interest1 - 11
for ($i = 1; $i <= 11; $i++){
if ($_POST["interest" . $i]){
    \indagare\db\LocalCrmDB::setPreference("interest" . $i, "on", $userid);
}
else 
    \indagare\db\LocalCrmDB::setPreference("interest" . $i, "off", $userid);
}

$redirect = "https://".$_SERVER['HTTP_HOST']."/account/#tab5";
header("Location: $redirect");

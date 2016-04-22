<?php
include_once 'user.php';
include_once 'db.php';
$user = \indagare\db\CrmDB::getUserByRemoteKey($_GET["u"]);
$user->startSession();
$redirect = "https://".$_SERVER['HTTP_HOST']."/account/#tab2";
    header("Location: $redirect");


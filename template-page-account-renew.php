<?php
/**
 * Template Name: Account - Renew
 *
 * …
 * 
 * @package Thematic
 * @subpackage Templates
 */
 
include_once 'app/lib/user.php';
include_once 'app/lib/db.php';
$user = \indagare\db\CrmDB::getUserByRemoteKey($_GET["u"]);
$user->startSession();
$redirect = "https://".$_SERVER['HTTP_HOST']."/account/#tab2";
header("Location: $redirect");

?>
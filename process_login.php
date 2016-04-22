<?php
include_once 'app/lib/user.php';
include_once 'app/lib/db.php';
if (isset($_POST["usr"]) && isset($_POST["pwd"])) {
    $u = indagare\db\CrmDB::getUser($_POST["usr"]);
    if($u != false && $u->validatePwd($_POST["pwd"])) {
       $u->startSession();
       if (!isset($_POST["login_page"])){
            header('Location: ' . $_SERVER["HTTP_REFERER"]);
       }
       else {
           header('Location: /index.php');
       }
    }
    else {
        header('Location: ./login.php?msg=invalid_login');
    }
}
?>

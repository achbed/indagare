<?php

    include_once './app/lib/user.php';
    include_once './app/lib/db.php';
    
    if (isset($_POST["step"])) {
        if (isset($_POST["pwd"])) {
            $id = \indagare\db\LocalCrmDB::getResetKeyMember($_POST["key"]);
            if (count($id) == 0) {
                header('Location: ./login.php');
                return;
            }
            $u = \indagare\db\CrmDB::getUserById($id[0]);
            $u->setPassword($_POST["pwd"]);
            
            \indagare\db\CrmDB::updateUserPwd($u, $_POST["pwd"]);
            $u = \indagare\db\CrmDB::getUserById($id[0]);
            if($u != false && $u->validatePwd($_POST["pwd"])) {
                \indagare\db\LocalCrmDB::removeResetKey($_POST["key"]);
                $u->startSession();     
                header('Location: /index.php');
            }
            else {
                header('Location: ./login.php?msg=invalid_login');
            }
            return;
        }
    }
    
    if (!isset($_GET["key"])) {
        header('Location: ./login.php');
        return;
    }
    
    else {
        $key = $_GET["key"];
        $id = \indagare\db\LocalCrmDB::getResetKeyMember($key);
        if (count($id) == 0) {
            header('Location: ./login.php');
            return;
        }
        $u = \indagare\db\CrmDB::getUserById($id[0]);
        
    }
    
    
?>

<html>
    <head><title>login</title></head>
    <body>
        <?= $u->getDisplayName() ?>
        <form action="pwd_reset.php" method="POST">
            <input type="hidden" name="step" value="2">
            <input type="hidden" name="key" value="<?= $key ?>">
            New Password <input type="password" name="pwd"><br>
            verify Password <input type="password" name="pwd2"><br>
            <input type="submit" name="Login">
        </form>
    </body>
</html>

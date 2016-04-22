<?php
        include_once 'user.php';
        include_once 'db.php';
        include_once 'notifications.php';
        
        $task;
        if (isset($_GET["task"])) {
            $task = $_GET["task"];
        } 
        else {
            die("no task identified");
        }
        
        if ($task == "newsletter_signup") {
            if(!isset($_POST["email"])) {
                die ("missing email");
            }
            $email = $_POST["email"];
            $u = \indagare\db\CrmDB::getUserByEmail($email);
            if($u) {
                \indagare\db\CrmDB::updateUserEmailSub($u->getID(), 1);
            }
            else {
                \indagare\db\CrmDB::createNewsleterUser($email);
            }
            
            \indagare\notify\EmailNotification::sendNewsletterSignup($email);
            
            print "true";
        }
        else if ($task == "newsletter_remove") {
            if(!isset($_POST["email"])) {
                die ("missing email");
            }
            $email = $_POST["email"];
            $u = \indagare\db\CrmDB::getUserByEmail($email);
            if($u) {
                \indagare\db\CrmDB::updateUserEmailSub($u->getID(), 0);
            }
            
            print "true";
        }
        

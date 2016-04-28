<?php
    include_once 'user.php';
    include_once 'db.php';
    include_once 'mail.php';
    include_once 'Mail.php';
    include_once 'lphp.php';
    include_once 'config.php';
    include_once '../resources/emails/thank_you.php';
    include_once '../resources/emails/apply.php';


    $task;
    if (isset($_GET["task"])) {
        $task = $_GET["task"];
    }
    else {
        die("no task identified");
    }

    if ($task == "signup1") {
        $acc = indagare\users\AccountCreator::getAccountCreator();
        //print_r($_POST);
        $acc->user->prefix = $_POST["prefix"];
        $acc->user->first_name = $_POST["fn"];
        $acc->user->last_name = $_POST["ln"];
        $acc->user->middle_initial = $_POST["minitial"];
        $acc->user->email = $_POST["email"];
        $acc->user->membership_level = $_POST["l"];
        $acc->user->membership_years = $_POST["y"];
        $acc->user->passkey_id = $_POST["tgCode"];
        $acc->user->primary_street_address = $_POST['s_address1'];
        $acc->user->primary_street_address2 = $_POST['s_address2'];
        $acc->user->primary_city = $_POST['s_city'];
        $acc->user->primary_state = $_POST['s_state'];
        $acc->user->primary_postal = $_POST['s_zip'];
        $acc->user->primary_country = $_POST['s_country'];
        $acc->user->phone_home = $_POST['phone'];
    }
    else if ($task == "signup21") {
        $acc->user->passkey_id = $_GET["rc"];
        print ( indagare\users\Passkey::validatePasskey( $_GET["rc"] ) ? 'true' : 'false' );
    }
    else if ($task == "signup22") {
        $acc = indagare\users\AccountCreator::getAccountCreator();
        //print_r($acc);
        $acc->user->question_1 = $_POST["top_destinations"];
        $acc->user->question_2 = $_POST["fav_hotels"];
        $acc->user->question_3 = $_POST["reason_travel"];
        $acc->user->question_4 = $_POST["next_destination"];
        //print_r($acc);
        $name = $acc->user->prefix . " " . $acc->user->first_name
                . " " . $acc->user->middle_initial
                . " " . $acc->user->last_name;
        $email = $acc->user->email;
        $phone = $acc->user->phone_home;
        $address = $acc->user->primary_street_address . ", " .
        $acc->user->primary_street_address2 . ", " .
        $acc->user->primary_postal . " " .
        $acc->user->primary_city . " " .
        $acc->user->primary_state . ", " .
        $acc->user->primary_country;
/*         $q1 = $acc->user->question_1;
        $q2 = $acc->user->question_2;
        $q3 = $acc->user->question_3;
        $q4 = $acc->user->question_4;
        $message = "A new application for Indagare:

                    NAME
                    $name

                    EMAIL
                    $email

                    PHONE
                    $phone

                    ADDRESS
                    $address

                    QUESTIONNAIRE
                    1) Tell us your top three destinations.\n
                    $q1

                    2) Tell us your three favorite hotels.\n
                    $q2

                    3) What are the three main reasons you travel?\n
                    $q3

                    4) Where would you like to go next?\n
                    $q4";

        $m = new \indagare\util\IndagareMailer();
        $m->send('A new application for Indagare', $message, "Indagare <info@indagare.com>");

        $thank_you = createThankyouForApp();
        $m->send('Thank you for submitting an application to Indagare', $thank_you, $email); */
    }
    else if ($task == "chkLogin") {
        $u = indagare\users\User::checkLogin($_POST["login"]);
        if ($u) {
            print "true";
        }
        else {
            print "false";
        }
    }
    else if ($task == "chkTrialKey") {
        $acc->user->passkey_id = $_GET["rc"];
        $key = \indagare\db\CrmDB::getPasskey($_GET["rc"]);
        if ($key != "false") {
            if ($key->type == 3){
                print "false";
            }
            else if ($key->trials > 0) {
                print "true";
                print "|" . $key->type;
            }
            else {
            print "false";
        }
        }
        else {
            print "false";
        }
    }
    else if ($task == "newTrial") {
        $acc = indagare\users\AccountCreator::getAccountCreator();
        $acc->user->login = $_POST['username'];
        $acc->user->password = $_POST['password'];
        $acc->user->primary_street_address = $_POST['s_address1'];
        $acc->user->primary_street_address2 = $_POST['s_address2'];
        $acc->user->primary_city = $_POST['s_city'];
        $acc->user->primary_state = $_POST['s_state'];
        $acc->user->primary_postal = $_POST['s_zip'];
        $acc->user->primary_country = $_POST['s_country'];
        $acc->user->passkey_id = $_POST['passKey'];
        $acc->user->membership_level = 0;
        $acc->user->membership_years = 1;
        $acc->user->membership_created_at = date( 'Y-m-d H:i:s');
        $acc->user->question_1 = $_POST["top_destinations"];
        $acc->user->question_2 = $_POST["fav_hotels"];
        $acc->user->question_3 = $_POST["reason_travel"];
        $acc->user->question_4 = $_POST["next_destination"];

        $key = \indagare\db\CrmDB::getPasskey($_POST['passKey']);
        if ($key != "false") {
            $mbText = "Trial Membership";
            if ($key->trials > 0) {
                if ($key->type == 1) {
                    $mb = \indagare\db\CrmDB::getMembershipByLevel($acc->user->membership_level + 1);
                    $acc->user->membership_expires_at = date( 'Y-m-d H:i:s',
                        mktime(0, 0, 0, date("m"),   date("d"),   date("Y") + $acc->user->membership_years));
                    $mbText = $mb->name . " - " . $acc->user->membership_years . " years";
                }
                else {
                    $acc->user->membership_expires_at = date( 'Y-m-d H:i:s',
                                mktime(0, 0, 0, date("m"),   date("d") + 30,   date("Y")));
                    $mbText = "Trial Membership, 30 days";

                }
                $uid = \indagare\db\CrmDB::createTrialUser($acc->user);
                \indagare\db\CrmDB::decrementTrial($acc->user->passkey_id);

                $thankyou = createThankyouEmail($acc->user->first_name . " " . $acc->user->last_name,
                                $acc->user->primary_street_address,
                                $acc->user->primary_city,
                                $acc->user->primary_state,
                                $acc->user->primary_postal,
                                $acc->user->primary_country,
                                $acc->user->email,
                                $mbText);
                $email = $acc->user->email;
                $m = new \indagare\util\IndagareMailer();
                $m->sendHtml('Welcome to Indagare!', $thankyou, $email);
                //$m->sendHtml('Welcome to Indagare!', $thankyou, "admin@indagare.com");
                $m->sendHtml('Welcome to Indagare!', $thankyou, "holger@whiteboardlabs.com");
                $u = \indagare\db\CrmDB::getUserById($uid);
                $u->startSession();
                $payloadurl="http://new.api.indagare.com/users/$uid/index_user";
                $timeout=5;
                stream_context_set_default(array('http' => array('timeout' => $timeout)));
                $payload = @get_headers($payloadurl);
                print "true";
            }
            else {print "no trials remaining";}
        }
        else {print "invalid trial code";}
    }
    else if ($task == "payment") {
        //print "Test";
        $acc = indagare\users\AccountCreator::getAccountCreator();
        $acc->user->prefix = $_POST["prefix"];
        $acc->user->first_name = $_POST["fn"];
        $acc->user->last_name = $_POST["ln"];
        $acc->user->middle_initial = $_POST["minitial"];
        $acc->user->email = $_POST["email"];
        $acc->user->membership_level = $_POST["l"];
        $acc->user->membership_years = $_POST["y"];
        $acc->user->passkey_id = $_POST["tgCode"];
        $acc->user->login = $_POST['username'];
        $acc->user->password = $_POST['password'];
        $acc->user->primary_street_address = $_POST['s_address1'];
        $acc->user->primary_street_address2 = $_POST['s_address2'];
        $acc->user->primary_city = $_POST['s_city'];
        $acc->user->primary_state = $_POST['s_state'];
        $acc->user->primary_postal = $_POST['s_zip'];
        $acc->user->primary_country = $_POST['s_country'];
        $acc->user->passkey_id = $_POST['passKey'];
        $acc->user->question_1 = $_POST["top_destinations"];
        $acc->user->question_2 = $_POST["fav_hotels"];
        $acc->user->question_3 = $_POST["reason_travel"];
        $acc->user->question_4 = $_POST["next_destination"];
        $acc->user->secondary_street_address = $_POST['address1'];
        $acc->user->secondary_street_address2 = $_POST['address2'];
        $acc->user->secondary_city = $_POST['city'];
        $acc->user->secondary_state = $_POST['state'];
        $acc->user->secondary_postal = $_POST['zip'];
        $acc->user->secondary_country = $_POST['country'];

        $order_id = mktime() + "_" + rand(1, 100);

        $mb = \indagare\db\CrmDB::getMembershipByLevel($acc->user->membership_level + 1);
        //print $mb->toJSON();
        //echo "mb start";
        $charge = $mb->getMembershipPrice($acc->user->membership_years);
        //print $charge;

        // 1909749438,staging.linkpt.net"1129
        // 1001177025,secure.linkpt.net:1129
	$mylphp=new \lphp();

	// constants
	/*$myorder["host"]       = "secure.linkpt.net";
	$myorder["port"]       = "1129";
	$myorder["keyfile"] = "/home/client02/firstdata/1001177025.pem";
	$myorder["configfile"] = "1001177025"; */

        //$myorder["debug"] = true;
        //$myorder["debugging"] = true;

        $myorder["host"]       = \indagare\config\Config::$pay_host;
	$myorder["port"]       = \indagare\config\Config::$pay_port;
	$myorder["keyfile"] = \indagare\config\Config::$pay_key;
	$myorder["configfile"] = \indagare\config\Config::$pay_config;

	// form data
        $myorder["name"]     = $_POST["cc_holder"];
	$myorder["cardnumber"]    = $_POST["cc_num"];
	$myorder["cardexpmonth"]  = $_POST["cc_m"];
	$myorder["cardexpyear"]   = $_POST["cc_y"];
        $myorder["cvmindicator"] = "provided";
	$myorder["cvmvalue"]     = $_POST["ccv"];
	$myorder["chargetotal"]   = $charge;
	$myorder["ordertype"]     = "SALE";

        $myorder["oid"]  = $order_id;

	$myorder["address1"] = $acc->user->primary_street_address;
	$myorder["address2"] = $acc->user->primary_street_address2;
	$myorder["city"]     = $acc->user->primary_city;
	$myorder["state"]    = $acc->user->primary_state;
	$myorder["country"]  = $acc->user->primary_country;
	$myorder["email"]    = $acc->user->email;
	$myorder["zip"]      = $acc->user->primary_postal;

        // setup recurring if order is for 1 year
        if ($acc->user->membership_years == 1) {
            $myorder["action"]       = "SUBMIT";
            $myorder["installments"] = "2";
            $myorder["threshold"]    = "3";
            $myorder["startdate"]    = "immediate";
            $myorder["periodicity"]  = "yearly";

        }

        /*
         *
         * if (isset($pdata["startdate"]))
		{
			$xml .= "<periodic>";

			$xml .= "<startdate>" . $pdata["startdate"] . "</startdate>";

			if (isset($pdata["installments"]))
				$xml .= "<installments>" . $pdata["installments"] . "</installments>";

			if (isset($pdata["threshold"]))
						$xml .= "<threshold>" . $pdata["threshold"] . "</threshold>";

			if (isset($pdata["periodicity"]))
						$xml .= "<periodicity>" . $pdata["periodicity"] . "</periodicity>";

			if (isset($pdata["pbcomments"]))
						$xml .= "<comments>" . $pdata["pbcomments"] . "</comments>";

			if (isset($pdata["action"]))
				$xml .= "<action>" . $pdata["action"] . "</action>";

			$xml .= "</periodic>";
		}
         *
         */

	//if ($_POST["debugging"])
	//$myorder["debugging"]="true";

        # Send transaction. Use one of two possible methods  #
	//$result = $mylphp->process($myorder);       # use shared library model
        //
	$result = $mylphp->curl_process($myorder);  # use curl methods
	/*$result = array();
	$result["r_approved"] = "APPROVED";//"APPROVED";
	$result["r_code"] = $order_id;
	$result['r_error'] = "APPROVED";*/
        /*$result = array();
        $result["r_approved"] = "Declined";//"APPROVED";
        $result["r_code"] = $order_id;
        $result['r_error'] = "Declined";*/
	//$result["r_approved"] = "APPROVED";
	if ($result["r_approved"] == "APPROVED") 	// success
	{
            print $result["r_approved"] . "-" . $result['r_code'] . "-";
            $acc->user->membership_created_at = date( 'Y-m-d H:i:s');
            $acc->user->membership_expires_at = date( 'Y-m-d H:i:s',
                    mktime(0, 0, 0, date("m"),   date("d"),   date("Y") + $acc->user->membership_years));
            //print "create user\n";

            try {
            $uid = \indagare\db\CrmDB::createUser($acc->user);
            //print "$uid, create order\n";
            //print_r($acc->user->getID());
            $uid = \indagare\db\CrmDB::updateUserQuestion($acc->user,$uid);
            $oid = \indagare\db\CrmDB::addOrder($uid, $charge, $result['r_approved'],
                    mktime(), $order_id, 1, substr($_POST["cc_num"], -4), $_POST["cc_m"], $_POST["cc_y"]);

            //print "$oid, create m_item\n";
           // \indagare\db\CrmDB::addMembershipLineItem($oid, $acc->user->membership_level+1, $acc->user->membership_years);
            //\indagare\db\CrmDB::addMembershipLineItem($oid, $mb->getMembershipPrice($acc->user->membership_years), $acc->user->membership_years);
            \indagare\db\CrmDB::addLineItem($oid, $mb->getMembershipPrice($acc->user->membership_years) . '00');

          }
          catch(Exception $e) {
              $m = new \indagare\util\IndagareMailer();
              $thankyou = createThankyouEmail($acc->user->first_name . " " . $acc->user->last_name,
                    $acc->user->primary_street_address,
                    $acc->user->primary_city,
                    $acc->user->primary_state,
                    $acc->user->primary_postal,
                    $acc->user->primary_country,
                    $acc->user->email,
                    $mb->name . " - " . $acc->user->membership_years . " years PRICE: $" . $mb->getMembershipPrice($acc->user->membership_years) . ".00");

              $m->sendHtml('(Indagare error!', $thankyou . " " . $e, "holger@whiteboardlabs.com");

          }
          //echo "1";

            $thankyou = createThankyouEmail($acc->user->first_name . " " . $acc->user->last_name,
                    $acc->user->primary_street_address,
                    $acc->user->primary_city,
                    $acc->user->primary_state,
                    $acc->user->primary_postal,
                    $acc->user->primary_country,
                    $acc->user->email,
                    $mb->name . " - " . $acc->user->membership_years . " years PRICE: $" . $mb->getMembershipPrice($acc->user->membership_years) . ".00");
            $email = $acc->user->email;
            $m = new \indagare\util\IndagareMailer();
            $m->sendHtml('Welcome to Indagare!', $thankyou, $email);
            $m->sendHtml('Welcome to Indagare!', $thankyou, "admin@indagare.com");
            $m->sendHtml('Welcome to Indagare!', $thankyou, "holger@whiteboardlabs.com");
            $payloadurl="http://new.api.indagare.com/users/$uid/index_user";
            $timeout=5;
            stream_context_set_default(array('http' => array('timeout' => $timeout)));
            $payload = @get_headers($payloadurl);
            /* if($file_headers[0] == 'HTTP/1.0 200 OK')
            {

            }
            else
            {

            } */

            //$payload = file_get_contents("http://staging.api.indagare.com/users/$uid/index_user");
            $u = \indagare\db\CrmDB::getUserById($uid);
            $u->startSession();
            //echo "1";
            //echo "-sso:".$_SESSION["SSODATA"];
            //print "Status: $result[r_approved]<br>\n";
            //echo $payload;

	}
        else    // transaction failed, print the reason
	{
            $m = new \indagare\util\IndagareMailer();
              $thankyou = createThankyouEmail($acc->user->first_name . " " . $acc->user->last_name,
                    $acc->user->primary_street_address,
                    $acc->user->primary_city,
                    $acc->user->primary_state,
                    $acc->user->primary_postal,
                    $acc->user->primary_country,
                    $acc->user->email,
                    $mb->name . " - " . $acc->user->membership_years . " years PRICE: $" . $mb->getMembershipPrice($acc->user->membership_years) . ".00");

              $m->sendHtml('(Indagare Declined', $thankyou . " " . $result["r_approved"] . "-" . $result['r_error'], 'holger@whiteboardlabs.com');
            print $result["r_approved"] . "-" . $result['r_error'];
	}


    }
    else if ($task == "renew") {
        //print "Test";
        $user = \indagare\db\CrmDB::getExtendedUserById($_POST["userid"]);

        $oldMb = $user->membership_level;

        $user->primary_street_address = $_POST['s_address1'];
        $user->primary_street_address2 = $_POST['s_address2'];
        $user->primary_city = $_POST['s_city'];
        $user->primary_state = $_POST['s_state'];
        $user->primary_postal = $_POST['s_zip'];
        $user->primary_country = $_POST['s_country'];
        $user->membership_years = $_POST['mb_y'];
        $user->membership_level = $_POST['mb'];
        $order_id = mktime() + "_" + rand(1, 100);

        $mb = \indagare\db\CrmDB::getMembershipByLevel($user->membership_level);
        //print $mb->toJSON();
        $charge = $mb->getMembershipPrice($user->membership_years);
        //print $charge;

	$mylphp=new \lphp();

        $myorder["host"]       = \indagare\config\Config::$pay_host;
	$myorder["port"]       = \indagare\config\Config::$pay_port;
	$myorder["keyfile"] = \indagare\config\Config::$pay_key;
	$myorder["configfile"] = \indagare\config\Config::$pay_config;

	// form data
        $myorder["name"]     = $_POST["cc_holder"];
	$myorder["cardnumber"]    = $_POST["cc_num"];
	$myorder["cardexpmonth"]  = $_POST["cc_m"];
	$myorder["cardexpyear"]   = $_POST["cc_y"];
        $myorder["cvmindicator"] = "provided";
	$myorder["cvmvalue"]     = $_POST["ccv"];
	$myorder["chargetotal"]   = $charge;
	$myorder["ordertype"]     = "SALE";

        $myorder["oid"]  = $order_id;

	$myorder["address1"] = $user->primary_street_address;
	$myorder["address2"] = $user->primary_street_address2;
	$myorder["city"]     = $user->primary_city;
	$myorder["state"]    = $user->primary_state;
	$myorder["country"]  = $user->primary_country;
	$myorder["email"]    = $user->email;
	$myorder["zip"]      = $user->primary_postal;

	$result = $mylphp->curl_process($myorder);  # use curl methods

	if ($result["r_approved"] == "APPROVED") 	// success
	{
            print $result["r_approved"] . "-" . $result['r_code'] . "-";
            //$acc->user->membership_created_at = date( 'Y-m-d H:i:s');
            $user->membership_expires_at = date( 'Y-m-d H:i:s',
                    mktime(0, 0, 0, date("m"),   date("d"),   date("Y") + $user->membership_years));
            //print "create user\n";
            $uid = \indagare\db\CrmDB::updateUserExp($user);
            $uid = \indagare\db\CrmDB::updateUserMB($user);

            //print "$uid, create order\n";
            $oid = \indagare\db\CrmDB::addOrder($uid, $charge, $result['r_approved'],
                    mktime(), $order_id, 1, substr($_POST["cc_num"], -4), $_POST["cc_m"], $_POST["cc_y"]);

            //print "$oid, create m_item\n";
            //\indagare\db\CrmDB::addMembershipLineItem($oid, $user->membership_level, $user->membership_years);
            \indagare\db\CrmDB::addLineItem($oid, $mb->getMembershipPrice($acc->user->membership_years) . '00');

            print $result["r_approved"] . "-" . $result['r_code'] . "-";

            if ($oldMb < $user->membership_level) {
                $thankyou = createThankyouUpgradeEmail($user->first_name . " " . $user->last_name,
                    $user->primary_street_address,
                    $user->primary_city,
                    $user->primary_state,
                    $user->primary_postal,
                    $user->primary_country,
                    $user->email,
                    $mb->name . " - " . $user->membership_years . " years PRICE: $" . $mb->getMembershipPrice($user->membership_years) . ".00");
            }
            else {
                $thankyou = createThankyouRenewEmail($user->first_name . " " . $user->last_name,
                    $user->primary_street_address,
                    $user->primary_city,
                    $user->primary_state,
                    $user->primary_postal,
                    $user->primary_country,
                    $user->email,
                    $mb->name . " - " . $user->membership_years . " years PRICE: $" . $mb->getMembershipPrice($user->membership_years) . ".00");
            }
            $email = $user->email;
            $m = new \indagare\util\IndagareMailer();
            $m->sendHtml('Welcome to Indagare!', $thankyou, $email);
            $m->sendHtml('Welcome to Indagare!', $thankyou, "admin@indagare.com");
            $m->sendHtml('Welcome to Indagare!', $thankyou, "holger@whiteboardlabs.com");

            //print "Status: $result[r_approved]<br>\n";

	}
        else    // transaction failed, print the reason
	{
            print $result["r_approved"] . "-" . $result['r_error'];
	}
    }
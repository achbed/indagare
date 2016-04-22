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

if ($task == "uppref") 
{
	//die("as");
	function buildBirthday($m, $d, $y) {
		return date( 'Y-m-d H:i:s', mktime(0, 0, 0, $m, $d, $y));
	}
	$userid = \indagare\users\User::getSessionUserID();
	$userArr = \indagare\db\LocalCrmDB::getUser($userid);
	//print_r($_POST);
	foreach ($_POST as $key => $value) {
		\indagare\db\LocalCrmDB::setPreference($key, $value, $userid);
	}
	
	// tw1 - 7
	for ($i = 1; $i <= 7; $i++){
		if (isset($_POST["tw" . $i])){
			\indagare\db\LocalCrmDB::setPreference("tw" . $i, "on", $userid);
		}
		else
			\indagare\db\LocalCrmDB::setPreference("tw" . $i, "off", $userid);
	}
	
	//interest1 - 11
	for ($i = 1; $i <= 11; $i++){
		if (isset($_POST["interest" . $i])){
			\indagare\db\LocalCrmDB::setPreference("interest" . $i, "on", $userid);
		}
		else
			\indagare\db\LocalCrmDB::setPreference("interest" . $i, "off", $userid);
	}
	echo json_encode(array("result"=>"true"));	
}
elseif ($task == "upaccount") 
{
	function buildBirthday($m, $d, $y) {
		return date( 'Y-m-d H:i:s', mktime(0, 0, 0, $m, $d, $y));
	}
	$userid = \indagare\users\User::getSessionUserID();
	$userArr = \indagare\db\LocalCrmDB::getUser($userid);
	$id = 0;
	//echo "1";
	if(!isset($userArr['CRSuserID'])) {
		\indagare\db\LocalCrmDB::addUser($userid,
				buildBirthday($_POST['m_bday_m'], $_POST['m_bday_d'], $_POST['m_bday_y']),
				$_POST['a_name'], $_POST['a_email'], $_POST['a_phone'], $POST['m_pass']);
	}
	else {
		\indagare\db\LocalCrmDB::updateUser($userid,
				buildBirthday($_POST['m_bday_m'], $_POST['m_bday_d'], $_POST['m_bday_y']),
				$_POST['a_name'], $_POST['a_email'], $_POST['a_phone'], $_POST['m_pass'],$_POST['contact_pref'],$_POST['delivery_pref']);
	}
	
	
	
	$userArr = \indagare\db\LocalCrmDB::getUser($userid);
	
	$i = 0;
	
	if (isset($_POST["remFFA"])){
		if ($_POST["remFFA"] != 0) {
			$ids = split(",", $_POST["remFFA"]);
			for ($i = 0; $i < count($ids); $i++) {
				\indagare\db\LocalCrmDB::remFFAccount($ids[$i]);
			}
		}
	}
	if (isset($_POST["remSFFA"])){
		if ($_POST["remSFFA"] != 0) {
			$ids = split(",", $_POST["remSFFA"]);
			for ($i = 0; $i < count($ids); $i++) {
				\indagare\db\LocalCrmDB::remFFAccount($ids[$i]);
			}
		}
	}
	if (isset($_POST["remCFFA"])){
		if ($_POST["remCFFA"] != 0) {
			$ids = split(",", $_POST["remCFFA"]);
			for ($i = 0; $i < count($ids); $i++) {
				\indagare\db\LocalCrmDB::remFFAccount($ids[$i]);
			}
		}
	}
	if (isset($_POST["remChild"])){
		if ($_POST["remChild"] != 0) {
			$ids = split(",", $_POST["remChild"]);
			for ($i = 0; $i < count($ids); $i++) {
				\indagare\db\LocalCrmDB::removeFamilyMember($ids[$i]);
			}
		}
	}
	
	while(isset($_POST["m_ffa$i"])){
		\indagare\db\LocalCrmDB::setFFAccount($_POST["m_ffaId$i"],
				$_POST["m_ffn$i"], ltrim(rtrim($_POST["m_ffa$i"])), ltrim(rtrim($userArr["id"])));
		$i++;
	}
	
	if (isset($_POST['s_name']) && $_POST['s_name'] != ""){
		if ($_POST['s_id'] == 0) {
			$s_id = \indagare\db\LocalCrmDB::addFamiliyMember($userid,
					buildBirthday($_POST['s_bday_m'], $_POST['s_bday_d'], $_POST['s_bday_y']),
					1, $_POST['s_email'], $_POST['s_pass'], $_POST['s_name']);
		}
		else {
			$s_id = $_POST['s_id'];
			\indagare\db\LocalCrmDB::updateFamiliyMember($_POST['s_id'],
					buildBirthday($_POST['s_bday_m'], $_POST['s_bday_d'], $_POST['s_bday_y']),
					1, $_POST['s_email'], $_POST['s_pass'], $_POST['s_name']);
		}
	
		$i = 0;
	
		while(isset($_POST["s_ffa$i"])){
			$ff_id = $_POST["s_ffa$i"];
			if ($ff_id == 0) {
				$ff_id = \indagare\db\LocalCrmDB::addFFAccount(ltrim(rtrim($_POST["s_ffn$i"])), ltrim(rtrim($_POST["s_ffa$i"])));
				\indagare\db\LocalCrmDB::addFF2Spose($ff_id, $s_id);
			}
			else {
				\indagare\db\LocalCrmDB::updateFFAccount($ff_id, ltrim(rtrim($_POST["s_ffn$i"])), ltrim(rtrim($_POST["s_ffa$i"])));
			}
			$i++;
		} 
	}
	
	$i = 0;
	while (isset($_POST['c' . $i . '_id'])) {
		$c_id = $_POST['c' . $i . '_id'];
		if ($c_id == 0) {
			$c_id = \indagare\db\LocalCrmDB::addFamiliyMember($userid,
					buildBirthday($_POST["c" . $i . "_bday_m"], $_POST["c" . $i . "_bday_d"], $_POST["c" . $i . "_bday_y"]),
					2, '', '', $_POST["c" . $i . "_name"]);
		}
		else {
			\indagare\db\LocalCrmDB::updateFamiliyMember($c_id,
					buildBirthday($_POST["c" . $i . "_bday_m"], $_POST["c" . $i . "_bday_d"], $_POST["c" . $i . "_bday_y"]),
					2, '', '', $_POST["c" . $i . "_name"]);
		}
		$j = 0;
	
		while(isset($_POST["c_ffaId$i" . "_" . $j])){
			$ff_id = $_POST["c_ffaId$i" . "_" . $j];
			if ($ff_id == 0) {
				$ff_id = \indagare\db\LocalCrmDB::addFFAccount(ltrim(rtrim($_POST["c_ffn$i" . "_" . $j])), ltrim(rtrim($_POST["c_ffa$i" . "_" . $j])));
				\indagare\db\LocalCrmDB::addFF2Spose($ff_id, $c_id);
			}
			else {
				\indagare\db\LocalCrmDB::updateFFAccount($ff_id, ltrim(rtrim($_POST["c_ffn$i" . "_" . $j])), ltrim(rtrim($_POST["c_ffa$i" . "_" . $j])));
			}
			$j++;
		}
		$i++;
	}
	echo json_encode(array("result"=>"true"));
	
}
/*  elseif ($task=="upcontact")
{
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
	echo json_encode(array("result"=>"true"));
} */	
/* elseif ($task=="uprenew")
{
	$user = \indagare\db\CrmDB::getUserById($_POST["userid"]);	
	$oldMb = $user->membership_level;	
	$user->primary_street_address = $_POST['s_address1'];
	$user->primary_street_address2 = $_POST['s_address2'];
	$user->primary_city = $_POST['s_city'];
	$user->primary_state = $_POST['s_state'];
	$user->primary_postal = $_POST['s_zip'];
	$user->primary_country = $_POST['s_country'];
	$user->membership_years = $_POST['mb_y'];
	$user->membership_level = $_POST['mb'];
	$order_id = time() + "_" + rand(1, 100);	
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
		//$acc->user->membership_created_at = date( 'Y-m-d H:i:s');
		$user->membership_expires_at = date( 'Y-m-d H:i:s',
				mktime(0, 0, 0, date("m"),   date("d"),   date("Y") + $user->membership_years));
		//print "create user\n";
		$uid = \indagare\db\CrmDB::updateUserExp($user);
		$uid = \indagare\db\CrmDB::updateUserMB($user);
	
		//print "$uid, create order\n";
		$oid = \indagare\db\CrmDB::addOrder($uid, $charge, $result['r_approved'],
				time(), $order_id, 1, substr($_POST["cc_num"], -4), $_POST["cc_m"], $_POST["cc_y"]);
	
		//print "$oid, create m_item\n";
		\indagare\db\CrmDB::addMembershipLineItem($oid, $user->membership_level, $user->membership_years);
	
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
		//$m->sendHtml('Welcome to Indagare!', $thankyou, "admin@indagare.com");
		$m->sendHtml('Welcome to Indagare!', $thankyou, "holger@whiteboardlabs.com");
	
		//print "Status: $result[r_approved]<br>\n";
	
	}
	else    // transaction failed, print the reason
	{
		print $result["r_approved"] . "-" . $result['r_error'];
	}
	echo json_encode(array("result"=>"true"));	
	
} */

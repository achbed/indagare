<?php namespace indagare\db;

include_once ("config.php");
include_once ("user.php");

class CrmDB {
    
    static function updateUser($user) {
        $passkey = mysql_real_escape_string($user->passkey_id);
        $pk_id = CrmDB::getPasskeyID($passkey); 
        if ($pk_id=="") {
        	$pk_id="'".$pk_id."'";
        }
        $connection = self::getConnection();

        $id = $user->getID();
        
        $fname = mysql_real_escape_string($user->first_name);
        $lname = mysql_real_escape_string($user->last_name);
        $salt = \indagare\users\User::createSalt($user->first_name);
        $pwd = \indagare\users\User::encryptPwd($user->password, $salt);
        $login = mysql_real_escape_string($user->login);
        $prefix = mysql_real_escape_string($user->prefix);
        $minitial = mysql_real_escape_string($user->middle_initial);
        $email = mysql_real_escape_string($user->email);
        $address = mysql_real_escape_string($user->primary_street_address);
        $address2 = mysql_real_escape_string($user->primary_street_address2);
        $city = mysql_real_escape_string($user->primary_city);
        $state = mysql_real_escape_string($user->primary_state);
        $zip = mysql_real_escape_string($user->primary_postal);
        $country = mysql_real_escape_string($user->primary_country);
        $wants_emails = mysql_real_escape_string($user->wants_emails);
        $mLevel = mysql_real_escape_string($user->membership_level + 1);
        $mYears = mysql_real_escape_string($user->membership_years);
        $created = mysql_real_escape_string($user->membership_created_at);
        $expires = mysql_real_escape_string($user->membership_expires_at);
        $phone_h = mysql_real_escape_string($user->phone_home);
        $phone_w = mysql_real_escape_string($user->phone_work);
        $phone_m = mysql_real_escape_string($user->phone_mobile);
        
        $sql = "UPDATE users SET 
            login='$login', 
            email='$email', 
            crypted_password='$pwd', 
            salt='$salt', 
            first_name= '$fname', 
            last_name='$lname', 
            middle_initial='$minitial', 
            prefix='$prefix', 
            passkey_id=$pk_id,
            membership_level = $mLevel,
            membership_created_at = '$created',
            membership_expires_at = '$expires',
            primary_street_address='$address', 
            primary_extended='$address2', 
            primary_city='$city', 
            primary_state='$state', 
            primary_postal='$zip', 
            primary_country='$country', 
            wants_emails=$wants_emails 
            WHERE id = $id";
        //            membership_years = $mYears,
        //print ($sql . "\n");
        
         //mysql_select_db('indagare-admin_staging') or die('Could not select database');   
        mysql_query($sql) or die('updateUser() -> Query failed: ' . mysql_error()); 
        //mysql_free_result($result);        
        mysql_close($connection);
        return $id;
    }
    
    static function updateUserAccountInfo($user) {
        $passkey = mysql_real_escape_string($user->passkey_id);
        $pk_id = CrmDB::getPasskeyID($passkey); 
        
        $connection = self::getConnection();

        $id = $user->getID();
        
        $fname = mysql_real_escape_string($user->first_name);
        $lname = mysql_real_escape_string($user->last_name);
        $prefix = mysql_real_escape_string($user->prefix);
        $minitial = mysql_real_escape_string($user->middle_initial);
        $email = mysql_real_escape_string($user->email);
        $address = mysql_real_escape_string($user->primary_street_address);
        $address2 = mysql_real_escape_string($user->primary_street_address2);
        $city = mysql_real_escape_string($user->primary_city);
        $state = mysql_real_escape_string($user->primary_state);
        $zip = mysql_real_escape_string($user->primary_postal);
        $country = mysql_real_escape_string($user->primary_country);
        $phone_h = mysql_real_escape_string($user->phone_home);
        $phone_w = mysql_real_escape_string($user->phone_work);
        $phone_m = mysql_real_escape_string($user->phone_mobile);
        
        $sql = "UPDATE users SET 
            email='$email', 
            first_name= '$fname', 
            last_name='$lname', 
            middle_initial='$minitial', 
            prefix='$prefix', 
            primary_street_address='$address', 
            primary_extended='$address2', 
            primary_city='$city', 
            primary_state='$state', 
            primary_postal='$zip', 
            primary_country='$country', 
            phone_home='$phone_h',
            phone_work='$phone_w',
            phone_mobile='$phone_m'
            WHERE id = $id";
        //print ($sql . "\n");

        mysql_query($sql) or die('updateUser() -> Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
        return $id;
    }
    static function updateUserQuestion($user,$id)
    {      	    	    	    	
    	$connection = self::getConnection();
    	$question_1=mysql_real_escape_string($user->question_1);
    	$question_2=mysql_real_escape_string($user->question_2);
    	$question_3=mysql_real_escape_string($user->question_3);
    	$question_4=mysql_real_escape_string($user->question_4);
    	$sql="UPDATE users SET question_1='".$question_1."',question_2='".$question_2."',question_3='".$question_3."',question_4='".$question_4."' WHERE id =".$id;
    	mysql_query($sql) or die('updateUserQuestion() -> Query failed: ' . mysql_error());
    	//mysql_free_result($result);
    	mysql_close($connection);
    	return $id;
    }
    static function updateUserExp($user) {

        $connection = self::getConnection();

        $id = $user->getID();
        
        $expires = $user->membership_expires_at;
        
        $sql = "UPDATE users SET membership_expires_at = '$expires', membership_status = 'active' WHERE id = $id";
        //print ($sql . "\n");

        mysql_query($sql) or die('updateUserExp() -> Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
        return $id;
    } 
    static function updateUserMB($user) {

        $connection = self::getConnection();

        $id = $user->getID();
        
        $membership = $user->membership_level;
        $seconds="";
        $secondary_street_address=mysql_real_escape_string($user->secondary_street_address);
        $secondary_extended=mysql_real_escape_string($user->secondary_extended);
        $secondary_city=mysql_real_escape_string($user->secondary_city);
        $secondary_state=mysql_real_escape_string($user->secondary_state);
        $secondary_country=mysql_real_escape_string($user->secondary_country);
        $secondary_postal=mysql_real_escape_string($user->secondary_postal);
        $vars=array('secondary_street_address', 'secondary_extended', 'secondary_city', 'secondary_state', 'secondary_postal', 'secondary_country');
        foreach ($vars as $keyvars => $valuevars) 
        {
          $seconds.=",".$valuevars."='".$$valuevars."'";
        }        
        $sql = "UPDATE users SET membership_level = $membership $seconds WHERE id = $id";
        //print ($sql . "\n");

        mysql_query($sql) or die('updateUserExp() -> Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
        return $id;
    }
    static function createUser($user) {
        //$passkey = mysql_real_escape_string($user->passkey_id);
        //$pk_id = CrmDB::getPasskeyID($passkey);
        
        $u = \indagare\db\CrmDB::getUserByEmail($user->email);
        if ($u) {
            //print ("got user\n");
            $user->setID($u->getID());
            $user->wants_emails = $u->wants_emails;
            //return \indagare\db\CrmDB::updateUser($user);  
        }
        
        
        
        $connection = self::getConnection(); 
        $fname = mysql_real_escape_string($user->first_name);
        $lname = mysql_real_escape_string($user->last_name);
        $salt = \indagare\users\User::createSalt($user->first_name);
        $pwd = \indagare\users\User::encryptPwd($user->password, $salt);
        $login = mysql_real_escape_string($user->login);
        $prefix = mysql_real_escape_string($user->prefix);
        $minitial = mysql_real_escape_string($user->middle_initial);
        $email = mysql_real_escape_string($user->email);
        
        
        $address = mysql_real_escape_string($user->primary_street_address);
        $address2 = mysql_real_escape_string($user->primary_street_address2);
        $city = mysql_real_escape_string($user->primary_city);
        $state = mysql_real_escape_string($user->primary_state);
        $zip = mysql_real_escape_string($user->primary_postal);
        $country = mysql_real_escape_string($user->primary_country);
        $wants_emails = mysql_real_escape_string($user->wants_emails);
        $mLevel = mysql_real_escape_string($user->membership_level + 1);
        $mYears = mysql_real_escape_string($user->membership_years);
        $created = mysql_real_escape_string($user->membership_created_at);
        $expires = mysql_real_escape_string($user->membership_expires_at);
        $secondary_street_address=mysql_real_escape_string($user->secondary_street_address);
        $secondary_extended=mysql_real_escape_string($user->secondary_extended);
        $secondary_city=mysql_real_escape_string($user->secondary_city);
        $secondary_state=mysql_real_escape_string($user->secondary_state);
        $secondary_country=mysql_real_escape_string($user->secondary_country);
        $secondary_postal=mysql_real_escape_string($user->secondary_postal);
        $phone = mysql_real_escape_string($user->phone_home);
        
        $remote_key = uniqid() . '_' . md5(mt_rand());
        //passkey_id,
        $sql = "INSERT INTO users (login, email, crypted_password, salt, 
            first_name, last_name, middle_initial, prefix, 
            membership_level, membership_created_at,
            membership_expires_at,
            primary_street_address, primary_extended, primary_city, 
            primary_state, primary_postal, primary_country, wants_emails, 
            membership_status, remote_key, created_at,secondary_street_address,
            secondary_extended,secondary_city,secondary_state,secondary_postal,
            secondary_country, phone_home, wants_mailings) VALUES(
            '$login', '$email', '$pwd', '$salt', '$fname', '$lname', '$minitial', 
            '$prefix', $mLevel, '$created', '$expires', 
            '$address', '$address2', '$city', '$state',
            '$zip', '$country', 1, 'active', '$remote_key', "
                . "'$created', '$secondary_street_address', '$secondary_extended', "
                . "'$secondary_city', '$secondary_state', '$secondary_postal', "
                . "'$secondary_country', '$phone', 1)";

        //print ($sql . "\n");
        
        $result=mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        $id = mysql_insert_id();
        //mysql_free_result($result);
        mysql_close($connection);
        return $id;
    }
    
    static function createTrialUser($user) {
        $passkey = mysql_real_escape_string($user->passkey_id);
        $pk_id = CrmDB::getPasskeyID($passkey);
        
        $u = \indagare\db\CrmDB::getUserByEmail($user->email);
        if ($u) {
            //print ("got user\n");
            $user->setID($u->getID());
            $user->wants_emails = $u->wants_emails;
            //return \indagare\db\CrmDB::updateUser($user);  
        }
        
        
        
        $connection = self::getConnection(); 
        $fname = mysql_real_escape_string($user->first_name);
        $lname = mysql_real_escape_string($user->last_name);
        $salt = \indagare\users\User::createSalt($user->first_name);
        $pwd = \indagare\users\User::encryptPwd($user->password, $salt);
        $login = mysql_real_escape_string($user->login);
        $prefix = mysql_real_escape_string($user->prefix);
        $minitial = mysql_real_escape_string($user->middle_initial);
        $email = mysql_real_escape_string($user->email);
        
        
        $address = mysql_real_escape_string($user->primary_street_address);
        $address2 = mysql_real_escape_string($user->primary_street_address2);
        $city = mysql_real_escape_string($user->primary_city);
        $state = mysql_real_escape_string($user->primary_state);
        $zip = mysql_real_escape_string($user->primary_postal);
        $country = mysql_real_escape_string($user->primary_country);
        $wants_emails = mysql_real_escape_string($user->wants_emails);
        $mLevel = "6"; //mysql_real_escape_string($user->membership_level + 1);
        $mYears = mysql_real_escape_string($user->membership_years);
        $created = mysql_real_escape_string($user->membership_created_at);
        $expires = mysql_real_escape_string($user->membership_expires_at);
        $secondary_street_address=mysql_real_escape_string($user->secondary_street_address);
        $secondary_extended=mysql_real_escape_string($user->secondary_extended);
        $secondary_city=mysql_real_escape_string($user->secondary_city);
        $secondary_state=mysql_real_escape_string($user->secondary_state);
        $secondary_country=mysql_real_escape_string($user->secondary_country);
        $secondary_postal=mysql_real_escape_string($user->secondary_postal);
        
        
        $remote_key = uniqid() . '_' . md5(mt_rand());
        //passkey_id,
        $sql = "INSERT INTO users (login, email, crypted_password, salt, 
            first_name, last_name, middle_initial, prefix, 
            membership_level, membership_created_at,
            membership_expires_at, passkey_id,
            primary_street_address, primary_extended, primary_city, 
            primary_state, primary_postal, primary_country, wants_emails, 
            membership_status, remote_key, created_at,secondary_street_address,secondary_extended,secondary_city,secondary_state,secondary_postal,secondary_country) VALUES(
            '$login', '$email', '$pwd', '$salt', '$fname', '$lname', '$minitial', 
            '$prefix', $mLevel, '$created', '$expires', $pk_id,
            '$address', '$address2', '$city', '$state',
            '$zip', '$country', $wants_emails, 'active', '$remote_key', '$created', '$secondary_street_address', '$secondary_extended', '$secondary_city', '$secondary_state', '$secondary_postal', '$secondary_country')";

        //print ($sql . "\n");
        
        $result=mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        $id = mysql_insert_id();
        //mysql_free_result($result);
        mysql_close($connection);
        return $id;
    }
    
    static function createNewsleterUser($email) {
        $connection = self::getConnection();
        $date = date( 'Y-m-d H:i:s');
        $sql = "INSERT INTO users (email, wants_emails, created_at) VALUES('$email', 1, '$date')";
        mysql_query($sql) or die('Query failed: ' . mysql_error());  
        //mysql_free_result($result);
        mysql_close($connection);
        
    }
    static function addOrder($user, $charge, $status, $created_at, $oid, $recurring, $cc, $cc_month, $cc_year){
        $connection = self::getConnection();
        $sql = "INSERT INTO orders (user_id, charge_total, status, created_at, oid, is_recurring, cc_number, cc_expmonth, cc_expyear) "
                . "VALUES($user, $charge, '$status', $created_at, '$oid', $recurring, $cc, $cc_month, $cc_year)";
        //print ($sql . "\n");
        $result=mysql_query($sql) or die('Query failed: ' . mysql_error());  
        $id = mysql_insert_id();
        //mysql_free_result($result);
        mysql_close($connection);
        return $id;
    }
    static function addMembershipLineItem($order, $membership, $membership_length){
        $connection = self::getConnection();
        $sql = "INSERT INTO membership_line_items (order_id, membership_id, membership_length, created_at) "
                . "VALUES($order, $membership, $membership_length, '" . date( 'Y-m-d H:i:s') . "')";
        $result=mysql_query($sql) or die('Query failed: ' . mysql_error());  
        //mysql_free_result($result);
        mysql_close($connection);
    } 
    
    static function addLineItem($order, $price){
        $connection = self::getConnection();
        $sql = "INSERT INTO line_items (order_id, quantity, created_at, cached_price) "
                . "VALUES($order, 1, '" . date( 'Y-m-d H:i:s') . "', $price)";
        $result=mysql_query($sql) or die('Query failed: ' . mysql_error());  
        //mysql_free_result($result);
        mysql_close($connection);
    } 
    
    static function addAddress($address1, $address2, $city, $state, $postal, $country){
        $connection = self::getConnection();
        $sql = "INSERT INTO address (street_address, extended, city, state, postal, country) "
                . "VALUES('$address1', '$address2', '$city', '$state', '$postal', '$country', '" . date( 'Y-m-d H:i:s') . "')";
        mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        $id = mysql_insert_id();
        mysql_close($connection); 
        return $id;
    }
    static function getPasskeyID($pk) {
        $connection = self::getConnection();
        $pk_id = 0;
        $pk_id_sql = "SELECT id FROM passkeys WHERE passkey = '$pk'"; 
        //print ($pk_id_sql . "\n");
        $result = mysql_query($pk_id_sql) or die('Get PasskeyID() -> Query failed: ' . mysql_error());
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $pk_id = $row['id'];        
        mysql_free_result($result);        
        mysql_close($connection);
        return $pk_id;
    }
    // get user by email
    static function getUserByEmail($email) {
        $connection = self::getConnection();
        // no more sql injections
        $email = mysql_real_escape_string($email);
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        //print (count($row) . '<br>');
        if ($row) {
            $user = new \indagare\users\User($row['id']);
            $user->setLogin($row['login']);
            $user->setPassword($row['crypted_password']);
            $user->setSalt($row['salt']);
            $user->first_name = $row['first_name'];
            $user->last_name = $row['last_name'];
            $user->email = $row['email'];
            mysql_free_result($result);
            mysql_close($connection);
            return $user;
        }
        else {
            mysql_free_result($result);
            mysql_close($connection);
            return false;
        }
        mysql_free_result($result);
        mysql_close($connection);  
    }  
    // get user by remote key
    static function getUserByRemoteKey($login) {
        $connection = self::getConnection();
        // no more sql injections
        $login = mysql_real_escape_string($login);
        $sql = "SELECT * FROM users WHERE remote_key = '$login'";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        //print (count($row) . '<br>');
        if ($row) {
            $user = new \indagare\users\User($row['id']);
            $user->setLogin($row['login']);
            $user->setPassword($row['crypted_password']);
            $user->setSalt($row['salt']);
            $user->first_name = $row['first_name'];
            $user->last_name = $row['last_name'];
            $user->email = $row['email'];
            mysql_free_result($result);
            mysql_close($connection);
            return $user;
        }
        else {
            mysql_free_result($result);
            mysql_close($connection);
            return false;
        }
        mysql_free_result($result);
        mysql_close($connection);
    }
    // get user by login
    static function getUser($login,$additional=array()) {
        $connection = self::getConnection();
        // no more sql injections
        $login = mysql_real_escape_string($login);
        //print_r($additional);
        if (is_array($additional)&&(count($additional)!=0)) 
        {
        	$addstrings=" ";
        	if (isset($additional['joint'])) {
        		$joint=$additional['joint'];
        		unset($additional['joint']);
        	}
        	else
        	{
        		$joint="AND";
        	}
        	$addstrings.=$joint;
        	foreach ($additional as $keyadd => $valueadd) 
        	{
        		$addstrings.=" ".$keyadd."='".$valueadd."' ".$joint;
        	}
        	$addstrings=substr($addstrings,0,strlen($addstrings)-strlen($joint));
        }
        else 
        {
        	$addstrings="";
        }
        //echo $addstrings;	
        $sql = "SELECT * FROM users WHERE login = '$login' OR email='$login'".$addstrings;
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        //print (count($row) . '<br>');
        if ($row) {
            $user = new \indagare\users\User($row['id']);
            $user->setLogin($row['login']);
            $user->setPassword($row['crypted_password']);
            $user->setSalt($row['salt']);
            $user->first_name = $row['first_name'];
            $user->last_name = $row['last_name'];
            $user->email = $row['email'];
            mysql_free_result($result);
            mysql_close($connection);
            return $user;
        }
        else {
            mysql_free_result($result);
            mysql_close($connection);
            return false;
        }
        mysql_free_result($result);
        mysql_close($connection);
    }
    // get user by id
    static function getUserById($id) {
        $connection = self::getConnection();
        // no more sql injections
        $id = mysql_real_escape_string($id);
        $sql = "SELECT * FROM users WHERE id = $id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        //print (count($row) . '<br>');
        if ($row) {
            $user = new \indagare\users\User($row['id']);
            $user->setLogin($row['login']);
            $user->setPassword($row['crypted_password']);
            $user->setSalt($row['salt']);
            $user->first_name = $row['first_name'];
            $user->last_name = $row['last_name'];
            $user->email = $row['email'];
            $user->primary_street_address = $row['primary_street_address'];
            $user->primary_street_address2 = $row['primary_extended'];
            $user->primary_city = $row['primary_city'];
            $user->primary_state = $row['primary_state'];
            $user->primary_postal = $row['primary_postal'];
            $user->primary_country = $row['primary_country'];
            
            mysql_free_result($result);
            mysql_close($connection);
            return $user;
        }
        else {
            mysql_free_result($result);
            mysql_close($connection);
            return false;
        }
        mysql_free_result($result);
        mysql_close($connection);
    }
    
    static function getExtendedUserById($id) {
        $connection = self::getConnection();
        // no more sql injections
        $id = mysql_real_escape_string($id);
        $sql = "SELECT * FROM users WHERE id = $id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        //print (count($row) . '<br>');
        if ($row) {
            $user = new \indagare\users\User($row['id']);
            $user->setLogin($row['login']);
            $user->setPassword($row['crypted_password']);
            $user->setSalt($row['salt']);
            $user->first_name = $row['first_name'];
            $user->last_name = $row['last_name'];
            $user->middle_initial = $row['middle_initial'];
            $user->prefix = $row['prefix'];
            $user->email = $row['email'];
            $user->membership_level = $row['membership_level'];
            $user->membership_expires_at = $row['membership_expires_at'];
            
            $user->primary_street_address = $row['primary_street_address'];
            $user->primary_street_address2 = $row['primary_extended'];
            $user->primary_city = $row['primary_city'];
            $user->primary_state = $row['primary_state'];
            $user->primary_postal = $row['primary_postal'];
            $user->primary_country = $row['primary_country'];
            
            $user->phone_home = $row['phone_home'];
            $user->phone_work = $row['phone_work'];
            $user->phone_mobile = $row['phone_mobile'];
            
            mysql_free_result($result);
            mysql_close($connection);
            return $user;
        }
        else {
            mysql_free_result($result);
            mysql_close($connection);
            return false;
        }
        mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function updateUserEmailSub($user_id, $sub) {
        $connection = self::getConnection();
        $s = mysql_real_escape_string($sub);
        $id = mysql_real_escape_string($user_id);
        $sql = "UPDATE users SET wants_emails = $s WHERE id = $id";
        $result=mysql_query($sql) or die('Query failed: ' . mysql_error());
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function updateUserPwd($u) {
        $connection = self::getConnection();
        $pwd = $u->getEncryptedPassword();
        $id = $u->getID();
        $sql = "UPDATE users SET crypted_password = '$pwd' WHERE id = $id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function updateLastLogin($uid) {
        $connection = self::getConnection();
        $d = date( 'Y-m-d H:i:s');
        $sql = "UPDATE users SET last_logged_in_at = '$d' WHERE id = $uid";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    static function getTrips($id) {
        $ret = array();
        $connection = self::getConnection();
        $id = mysql_real_escape_string($id);
        $sql = "SELECT * FROM trips WHERE member_id = $id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$i] = new \indagare\trips\Trip($row['id'], $row['member_id']);
            $ret[$i]->start_date = $row['start_date'];
            $ret[$i]->end_date = $row['end_date'];
            $ret[$i]->pdf_content_type = $row['pdf_content_type'];
            $ret[$i]->pdf_file_name = $row['pdf_file_name'];
            $ret[$i]->is_canceled = $row['is_cancelled'];
            $ret[$i]->room_rate = $row['room_rate'];
            $i++;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    static function getPasskey($key) {
        $key = mysql_real_escape_string($key);
        $ret = array();
        $connection = self::getConnection();
        $sql = "SELECT * FROM passkeys WHERE passkey = '$key' AND active = 1";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $passkey = "false";
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $passkey = new \indagare\users\Passkey($row["id"], $row["passkey"], 
                    $row["user_id"], $row["discount"], $row["trial_memberships_remaining"]);
            $passkey->active = $row["active"];
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $passkey;
    }
    
    static function getGiftCode($key) {
        $key = mysql_real_escape_string($key);
        $ret = array();
        $connection = self::getConnection();
        $sql = "SELECT g.code, g.redeemed, m.membership_id, m.membership_length FROM `indagare_staging`.`gift_codes` g JOIN membership_line_items m ON g.id = m.gift_code_id WHERE g.code = '$key'";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $giftCode = false;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $giftCode = $row;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return giftCode;
    }
    
    static function decrementTrial($keyID) {
        $key = self::getPasskey($keyID);
        $count = $key->trials - 1;
        $connection = self::getConnection();
        $sql = "UPDATE passkeys SET trial_memberships_remaining = $count WHERE passkey = '$keyID'";
        $result=mysql_query($sql) or die('Query failed: ' . mysql_error());
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    static function getItineraries($id) {
        $ret = array();
        $connection = self::getConnection();
        $id = mysql_real_escape_string($id);
        $sql = "SELECT * FROM itineraries WHERE user_id = $id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$i] = new \indagare\trips\Itinerary($row["id"], $row["user_id"]);
            $ret[$i]->created_at = $row["created_at"];
            $ret[$i]->description = $row["description"];
            $ret[$i]->title = $row["title"];
            $ret[$i]->updated_at = $row["updated_at"];
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    static function getItineraryItems($itinerayId) {
        $ret = array();
        $connection = self::getConnection();
        $id = mysql_real_escape_string($id);
        $sql = "SELECT * FROM itinerary_items WHERE itinerary_id = $id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$i] = new \indagare\trips\ItineraryItem($row["id"], $row["itinerary_id"]);
            $ret[$i]->article_id = $row["article_id"];
            $ret[$i]->created_at = $row["created_at"];
            $ret[$i]->updated_at = $row["updated_at"];
            $ret[$i]->position = $row["position"];
            $i++;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function getMemberships() {
        $ret = array();
        $connection = self::getConnection();
        $sql = "SELECT * FROM memberships WHERE active = 1 ORDER BY membership_level";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$i] = new \indagare\users\Membership($row["id"], 
                    $row["membership_level"], $row["name"], 
                    $row["price_1_year"], $row["price_2_year"], $row["price_3_year"]);
            $i++;
        }  
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function getMembershipByLevel($level) {
        $ret = false;
        $l = mysql_real_escape_string($level);
        $connection = self::getConnection();
        $sql = "SELECT * FROM memberships WHERE membership_level = $l AND active = 1";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());     
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret = new \indagare\users\Membership($row["id"], 
                    $row["membership_level"], $row["name"], 
                    $row["price_1_year"], $row["price_2_year"], $row["price_3_year"]);
            $i++;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function getFavorite($user_id, $post_id) {
        
        $ret = array();
        if ( ( $user_id <= 0 ) || ( $post_id <= 0 ) ) {
          return $ret;
        }
        $connection = self::getConnection();
        $sql = "SELECT * FROM favorites WHERE user_id = $user_id AND article_id = $post_id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$i] = new \indagare\users\Favorite($row["id"], 
                    $row["article_id"], $row["user_id"], 
                    $row["created_at"], $row["updated_at"]);
            $i++;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function getFavorites($user_id) {
        
        $ret = array();
        if ( $user_id <= 0 ) {
          return $ret;
        }
        $connection = self::getConnection();
        $sql = "SELECT * FROM favorites WHERE user_id = $user_id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$i] = new \indagare\users\Favorite($row["id"], 
                    $row["article_id"], $row["user_id"], 
                    $row["created_at"], $row["updated_at"]);
            $i++;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function createFavorite($favorite) {

        if ( ( $favorite->article_id <= 0 ) || ( $favorite->user_id <= 0 ) ) {
          return;
        }
    
        $connection = self::getConnection();    

        $sql = "INSERT INTO favorites (article_id, user_id) VALUES(" .
                $favorite->article_id . "," . $favorite->user_id . ")";

        //$result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        mysql_query($sql) or die('Query failed: ' . mysql_error());
        //mysql_free_result($result);
        mysql_close($connection);
    
    }

    public static function removeFavorite($user_id,$post_id) {
      
        if ( ( $user_id <= 0 ) || ( $post_id <= 0 ) ) {
          return $ret;
        }
        $connection = self::getConnection();    

//        $sql = "DELETE FROM favorites WHERE id = " . $favorite->id;
        $sql = "DELETE FROM favorites WHERE user_id = $user_id AND article_id = $post_id";

        //$result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        $result=mysql_query($sql) or die('Query failed: ' . mysql_error());
        //mysql_free_result($result);
        mysql_close($connection);
    
    }
    //'root', '$QL@cc3ss7'
    //'172.31.104.13', 'wpdbuser', 'c0mm0nmySQL'
    //'172.31.104.11', 'staging01', 'c0mm0nmySQL'
    //'172.31.104.14', 'prod01', 'c0mm0nmySQL'
    //indagare-admin_staging
    static function getConnection(){
        $connection = mysql_connect(\indagare\config\Config::$crm_db_server, \indagare\config\Config::$crm_db_user, \indagare\config\Config::$crm_db_pwd)
        or die('Could not connect: ' . mysql_error());      
        mysql_select_db(\indagare\config\Config::$crm_db) or die('Could not select database');//'indagare_production'
        return $connection;
    }
}

class LocalCrmDB {
    
    public static function setFFAccount($id, $ffnumber,$airline, $user) {
        if ($id == 0) {
            $ffa = \indagare\db\LocalCrmDB::addFFAccount($ffnumber, $airline);
            \indagare\db\LocalCrmDB::addFF2Member($ffa, $user);
        }
        else {
            \indagare\db\LocalCrmDB::updateFFAccount($id, $ffnumber, $airline);
        }
    }
    
    public static function remFFAccount($id) {
        $connection = self::getConnection();   
        $sql = "DELETE FROM rel_Entity_FrequentFlyersAccount WHERE FfaID = $id";
        //$result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        $sql = "DELETE FROM FrequentFlyerAccounts WHERE FFaID = $id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function addFFAccount($ffnumber,$airline) {
        $connection = self::getConnection();    
        $sql = "INSERT INTO FrequentFlyerAccounts (FFNumber, Airline) VALUES('". mysql_real_escape_string($ffnumber) ."', '" . mysql_real_escape_string($airline). "')";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        $id = mysql_insert_id();
        //mysql_free_result($result);
        mysql_close($connection);
        return $id;
    }
    
    public static function addFF2Spose($ff_id, $member_id) {
        $connection = self::getConnection();    
        $sql = "INSERT INTO rel_Entity_FrequentFlyersAccount (EntityID, Entity, FfaID) VALUES($member_id, 2, $ff_id)";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function addFF2Member($ff_id, $member_id) {
        $connection = self::getConnection();    
        $sql = "INSERT INTO rel_Entity_FrequentFlyersAccount (EntityID, Entity, FfaID) VALUES($member_id, 1, $ff_id)";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function updateFFAccount($ffaID, $ffnumber,$airline) {
        $connection = self::getConnection();    
        $sql = "UPDATE FrequentFlyerAccounts SET FFNumber='". mysql_real_escape_string($ffnumber) . "', "
                . "Airline='" . mysql_real_escape_string($airline) . "' WHERE FfaID = $ffaID";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function getFFAccounts($id, $entity) {
        $id = intval($id);
        $entity = intval($entity);
        $ret = array();
        $connection = self::getConnection();
        $sql = sprintf( "
            SELECT * 
            FROM FrequentFlyerAccounts 
            WHERE FfaID IN (
                SELECT FfaID 
                FROM rel_Entity_FrequentFlyersAccount 
                WHERE EntityID=%d AND Entity=%d
            )", $id, $entity);
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $c = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$c] = $row;
            $c++;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function addFamiliyMember($user_id,$birthday,$type,$email, $passport, $name) {
        $name = mysql_real_escape_string($name);
        $connection = self::getConnection();    
        $sql = "INSERT INTO FamilyMembers (UserID, Birthday, TypeID, PassportCountry, "
                . "Email, Name) VALUES($user_id, '$birthday',"
                . "$type,'" . mysql_real_escape_string($email) . "', '" . mysql_real_escape_string($passport) . "', '" . mysql_real_escape_string($name) . "')";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        $id = mysql_insert_id();
        //mysql_free_result($result);
        mysql_close($connection);
        return $id;
    }
    
    public static function removeFamilyMember($id) {
        LocalCrmDB::remFFAccount($id);
        $connection = self::getConnection();   
        $sql = "DELETE FROM FamilyMembers WHERE FamilyMemberID = $id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function getFamilyMembers($user_id, $type) {
        $ret = array();
        $connection = self::getConnection();
        $sql = "SELECT * FROM FamilyMembers WHERE UserID = $user_id AND TypeID = $type";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $c = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$c] = $row;
            $c++;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function updateFamiliyMember($fm_id,$birthday,$type,$email, $passport, $name) {
        $name = mysql_real_escape_string($name);
        $connection = self::getConnection();    
        $sql = "UPDATE FamilyMembers SET Birthday='$birthday', Email='" . mysql_real_escape_string($email) . "', "
                . "PassportCountry='" . mysql_real_escape_string($passport) . "', Name='" . mysql_real_escape_string($name) . "' WHERE FamilyMemberID = $fm_id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function addUser($user_id,$birthday,$aname,$aemail,$aphone, $passport) {
        $aname = mysql_real_escape_string($aname);
        $connection = self::getConnection();    
        $sql = "INSERT INTO Users (CRSuserID, Birthday, AssistentName, "
                . "AssistentEmail, AssistentPhone, PassportCountry) VALUES($user_id, '$birthday',"
                . "'" . mysql_real_escape_string($aname) . "','" . mysql_real_escape_string($aemail) . "','" . mysql_real_escape_string($aphone) . "', '" . mysql_real_escape_string($passport) . "')";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    
    }
    
    public static function getPreferences($user) {
        $ret = array();
        $connection = self::getConnection();
        $sql = "SELECT * FROM User_Preferences WHERE userID = $user";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $c = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$c] = $row;
            $c++;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function getPreference($user, $pref) {
        $ret = array();
        $connection = self::getConnection();
        $sql = "SELECT * FROM User_Preferences WHERE userID = $user AND preference = '$pref'";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $ret = mysql_fetch_array($result, MYSQL_ASSOC);
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function setPreference($pref, $value, $user) {
        $p = self::getPreference($user, $pref);
        $connection = self::getConnection();    
        $pref = mysql_real_escape_string($pref);
        $value = mysql_real_escape_string($value);
        if (!isset($p["id"])) {
            $sql = "INSERT INTO User_Preferences (preference, value, userID) VALUES ('$pref', '$value', $user)";
        }
        else {
            $sql = "UPDATE User_Preferences SET value='$value'"
                    . " WHERE userID = $user AND preference = '$pref'";
        }
        //echo $sql."<br/>";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    
    }

    public static function updateUser($user_id,$birthday,$aname,$aemail,$aphone, $passport,$contact_pref,$delivery_pref) {
        $connection = self::getConnection();   
        $passport = mysql_real_escape_string($passport);
        $sql = "UPDATE Users SET Birthday='$birthday', AssistentName='" . mysql_real_escape_string($aname) . "', "
                . "AssistentEmail='" . mysql_real_escape_string($aemail) . "', AssistentPhone='" . mysql_real_escape_string($aphone) . "' , PassportCountry='" . mysql_real_escape_string($passport). "' , contact_pref='" . mysql_real_escape_string($contact_pref) . "' , delivery_pref='" . mysql_real_escape_string($delivery_pref) ."'"
                . "WHERE CRSuserID = $user_id";
        //echo $sql;
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    public static function getUser($id) {
        $ret = array();
        $connection = self::getConnection();
        $sql = "SELECT * FROM Users WHERE CRSuserID = $id";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $ret = mysql_fetch_array($result, MYSQL_ASSOC);
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function addResetKey($user_id,$key) {
        $connection = self::getConnection();    
        $sql = "INSERT INTO pwd_reset_key (member_id, reset_key) VALUES($user_id, '$key')";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    
    }
    
    public static function getResetKeyMember($key) {
        
        $ret = array();
        $connection = self::getConnection();
        $sql = "SELECT * FROM pwd_reset_key WHERE reset_key = '$key'";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ret[$i] = $row["member_id"];
            $i++;
        }
        mysql_free_result($result);
        mysql_close($connection);
        return $ret;
    }
    
    public static function removeResetKey($key) {
        $connection = self::getConnection();    
        $sql = "DELETE FROM pwd_reset_key WHERE reset_key = '$key'";
        $result = mysql_query($sql) or die('Query failed: ' . mysql_error()); 
        //mysql_free_result($result);
        mysql_close($connection);
    }
    
    static function getConnection(){
        $connection = mysql_connect('localhost', 'root', '!nd@g@r3')
        or die('Could not connect: ' . mysql_error());      
        mysql_select_db('indagare_crm') or die('Could not select database');
        return $connection;
    }
    public static function getUsermembersinfo() {
    	$ret = array();
    	$connection = self::getConnection();
    	$sql= "SELECT * FROM Users";
    	$result=mysql_query($sql) or  die('Query failed: ' . mysql_error());
    	$seeds = 0;
    	//echo "1";
    	while ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
    		unset($row['id']);
    		$firstlastname=array();
    		$userinfosys=CrmDB::getUserById($row['CRSuserID']);
    		$firstlastname["first_name"]=$userinfosys->first_name;
    		$firstlastname["last_name"]=$userinfosys->last_name;
    		$row=array_merge($firstlastname,$row);
    		$family1=self::getFamilyMembers($row['CRSuserID'], 1);
    		foreach ($family1 as $keyfamily1=>$valuefamily1)
    		{    		   
    		   foreach ($valuefamily1 as $keycolumn1=>$valuecolumn1)
    		  {
    		  	$row["Family".$valuefamily1['FamilyMemberID']."_".$keycolumn1]=$valuecolumn1;    		  	
    		  }
    		  $ffcountsfamily1=self::getFFAccounts($valuefamily1['FamilyMemberID'],1);
    		  foreach ($ffcountsfamily1 as $keyffaccountsfamily1=>$valueffacountsfamily1)
    		  {
    		  	foreach ($valueffacountsfamily1 as $keyffcolumnfamily1=>$valueffcolumnfamily1)
    		  	{
    		  	 if ($keyffcolumnfamily1!="FfaID") 
    		  	 {
    		  	 	$row["Family".$valuefamily1['FamilyMemberID']."_"."FFAccounts".$valueffacountsfamily1['FfaID']."_".$keyffcolumnfamily1]=$valueffcolumnfamily1;
    		  	 }	    		  	 
    		  	 //print_r($row['CRSuserID'].":"."Family".$valuefamily1['FamilyMemberID'].":".$keyffcolumnfamily1.":".$valueffcolumnfamily1."<br/>");
    		  	}
    		  };
    		};
    			
    		$family2=self::getFamilyMembers($row['CRSuserID'], 2);
    		foreach ($family2 as $keyfamily2=>$valuefamily2)
    		{
    			foreach ($valuefamily2 as $keycolumn2=>$valuecolumn2)
    			{
    				$row["Family".$valuefamily2['FamilyMemberID']."_".$keycolumn2]=$valuecolumn2;
    			}
    			$ffcountsfamily2=self::getFFAccounts($valuefamily2['FamilyMemberID'],2);
    			foreach ($ffcountsfamily2 as $keyffaccountsfamily2=>$valueffacountsfamily2)
    			{
    				foreach ($valueffacountsfamily2 as $keyffcolumnfamily2=>$valueffcolumnfamily2)
    				{
    					if ($keyffaccountsfamily2!="FfaID") 
    					{
    					  $row["Family".$valuefamily2['FamilyMemberID']."_"."FFAccounts".$valueffacountsfamily2['FfaID']."_".$keyffcolumnfamily2]=$valueffcolumnfamily2;
    					}
    					
    					//print_r($row['CRSuserID'].":"."Family".$valuefamily2['FamilyMemberID'].":".$keyffcolumnfamily2.":".$valueffcolumnfamily2."<br/>");
    				}
    			};
    		};
    		
    		/* $ffcounts=self::getFFAccounts($row['CRSuserID'],1);
    		foreach ($ffcounts as $keyffaccounts=>$valueffacounts)
    		{
    		  foreach ($valueffacounts as $keyffcolumn=>$valueffcolumn)
    		  {
    		  	$row["FFAccounts".$valueffacounts['FfaID']."_".$keyffaccounts]=$valueffacounts;
    		  }		
    		};
    		
    		$preferences=self::getPreferences($row['CRSuserID']);
    		foreach ($preferences as $keypreferences=>$valuepreferences)
    		{
    		   foreach ($valuepreferences as $keyprecolumn=>$valueprecolumn)
    		   {
    		   	$row["Preferences".$valuepreferences['id']."_".$keyprecolumn]=$valueprecolumn;
    		   }    				
    		}; */
    		$question=array("count"=>"How often do you travel?","planning_style"=>"How would you describe your trip planning style?","tw"=>"Who do you travel with? Check all that apply.","features"=>"When choosing a hotel, which features are most important to you? Rank in order from 1 (lowest) - 10 (highest).","hotel_style"=>"What style of hotel do you prefer?","hotel_amenities"=>"What hotel amenities do you value most?","beverages"=>"Please list your beverages of choice.","allergies"=>"Allergies/Food Restrictions","itinerary_pref"=>"How do you like to travel","itinerary_pref2"=>"How do you like to travel","memories"=>"Please share some of your most fond travel memories and tell us why they were so special.","peeves"=>"What are your pet peeves when traveling?","decisions"=>"Tell us what influences your travel decisions.","else"=>"Is there anything else you would like to share about your travel preferences?","interest"=>"Please select your interests","sh_class"=>"Short Haul-Class of service","sh_seat"=>"Short Haul-Seat preference","sh_location"=>"Short Haul-Location","lh_class"=>"Long Haul-Class of service","lh_seat"=>"Long Haul-Seat preference","lh_location"=>"Long Haul-Location");
    		$row=array_merge($row,$question);
    		$ret[$seeds] = $row;
    		$answer=$row;
    		$CRSuserID_answer=$answer['CRSuserID'];
    		$question_keys=array_keys($question);
    		//print_r($answer_temp);
    		foreach ($answer as $keyanswertemp=>$valueanswertemp)
    		{
    		  if (in_array($keyanswertemp,$question_keys)) {
    		  	switch ($keyanswertemp) {
    		  		case "count":
    		  		 $count_options=array(1=>"Every school holiday plus a summer trip",2=>"One big trip a year","3"=>"2-3 weeks per year plus long weekends","4"=>"4-5 weeks per year plus long weekends","5"=>"6+ weeks a year");
    		  		 $preferences_count=self::getPreference($CRSuserID_answer,"count");
    		  		 if ($preferences_count["value"]!="") {
    		  		 	$answer[$keyanswertemp]=$count_options[$preferences_count["value"]];
    		  		 }
    		  		 else
    		  		 {
    		  		 	$answer[$keyanswertemp]="";
    		  		 };	
    		  		 
    		  		break;
    		  		case "planning_style":
    		   			$planning_style_options=array(1=>"Last minute booker (within one month of travel)",2=>"Average advance planner (within 1-4 months of travel)",3=>"Scheduled traveler (4-8 months)",4=>"Early-booker (8-12 months prior)");
    		  			$preferences_planning_style=self::getPreference($CRSuserID_answer,"planning_style");
    		  			if ($preferences_planning_style["value"]!="") {
    		  				$answer[$keyanswertemp]=$planning_style_options[$preferences_planning_style["value"]];
    		  			}
    		  			else
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			};	 	  			
    		  		break;
    		  		case "itinerary_pref":
    		  			$itinerary_pref_options=array(1=>"Do you prefer a robust and busy itinerary?",2=>"or a more relaxed schedule with a mix of activities and down time? ");
    		  			$preferences_itinerary_pref=self::getPreference($CRSuserID_answer,"itinerary_pref");
    		  			if ($preferences_itinerary_pref["value"]!="") {
    		  				$answer[$keyanswertemp]=$itinerary_pref_options[$preferences_itinerary_pref["value"]];
    		  			}
    		  			else 
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			};	    		  			
    		  		break;
    		  		
    		  		case "itinerary_pref2":
    		  			$itinerary_pref2_options=array(1=>"Do you prefer a robust and busy itinerary?",2=>"or a more relaxed schedule with a mix of activities and down time? ");
    		  			$preferences_itinerary_pref2=self::getPreference($CRSuserID_answer,"itinerary_pref2");
    		  			if ($preferences_itinerary_pref2["value"]!="") {
    		  				$answer[$keyanswertemp]=$itinerary_pref2_options[$preferences_itinerary_pref2["value"]];
    		  			}
    		  			else
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			};
    		  		break;
    		  		case "sh_class":
    		  			$sh_class_options=array(1=>"economy",2=>"premium economy",3=>"business",4=>"first");
    		  			$preferences_sh_class=self::getPreference($CRSuserID_answer,"sh_class");
    		  			if ($preferences_sh_class["value"]!="") {
    		  				$answer[$keyanswertemp]=$sh_class_options[$preferences_sh_class["value"]];
    		  			}
    		  			else
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			};
    		  		break;
    		  		
    		  		case "sh_seat":
    		  			$sh_seat_options=array(1=>"front",2=>"back",3=>"right",4=>"left");
    		  			$preferences_sh_seat=self::getPreference($CRSuserID_answer,"sh_seat");
    		  			if ($preferences_sh_seat["value"]!="") {
    		  				$answer[$keyanswertemp]=$sh_seat_options[$preferences_sh_seat["value"]];
    		  			}
    		  			else
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			};
    		  		break;
    		  		case "sh_location":
    		  			$sh_location_options=array(1=>"window",2=>"aisle",3=>"middle");
    		  			$preferences_sh_location=self::getPreference($CRSuserID_answer,"sh_location");
    		  			if ($preferences_sh_location["value"]!="") {
    		  				$answer[$keyanswertemp]=$sh_location_options[$preferences_sh_location["value"]];
    		  			}
    		  			else
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			};
    		  		break;
    		  		
    		  		
    		  		case "lh_class":
    		  			$lh_class_options=array(1=>"economy",2=>"premium economy",3=>"business",4=>"first");
    		  			$preferences_lh_class=self::getPreference($CRSuserID_answer,"lh_class");
    		  			if ($preferences_lh_class["value"]!="") {
    		  				$answer[$keyanswertemp]=$lh_class_options[$preferences_lh_class["value"]];
    		  			}
    		  			else
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			};
    		  			break;
    		  		
    		  		case "lh_seat":
    		  			$lh_seat_options=array(1=>"front",2=>"back",3=>"right",4=>"left");
    		  			$preferences_lh_seat=self::getPreference($CRSuserID_answer,"lh_seat");
    		  			if ($preferences_lh_seat["value"]!="") {
    		  				$answer[$keyanswertemp]=$lh_seat_options[$preferences_lh_seat["value"]];
    		  			}
    		  			else
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			};
    		  			break;
    		  		case "lh_location":
    		  			$lh_location_options=array(1=>"window",2=>"aisle",3=>"middle");
    		  			$preferences_lh_location=self::getPreference($CRSuserID_answer,"sh_location");
    		  			if ($preferences_lh_location["value"]!="") {
    		  				$answer[$keyanswertemp]=$lh_location_options[$preferences_lh_location["value"]];
    		  			}
    		  			else
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			};
    		  		break;
    		  		
    		  		case "tw":
    		  		   $tw_options=array("tw1"=>"My family (including children)","tw2"=>"My family (all adults)","tw3"=>"Couple","tw4"=>"Friend getaways","tw5"=>"Multi-generational trips","tw6"=>"Large-scale destination celebrations","tw7"=>"Multi-family trips");
    		  		   $tws=array();
    		  		   for ($i = 1; $i <= 7; $i++)
    		  		   {
    		  		   	$preferences_tw=self::getPreference($CRSuserID_answer,"tw".$i);
  		   		   	    if (($preferences_tw["value"]!="")&&($preferences_tw["value"]=="on")) {
    		  		   		$tws["tw".$i]=$tw_options["tw".$i];
    		  		   	}
    		  		   }
    		  		   if (count($tws)>0) 
    		  		   {
    		  		   	$totaltws=implode(",",$tws);
    		  		   	$answer[$keyanswertemp]=$totaltws;
    		  		   }
    		  		   else 
    		  		   {
    		  		   	$answer[$keyanswertemp]="";
    		  		   }
    		  		   
    		  		break;   		  		
    		  		
    		  		case "interest":
    		  			$interest_options=array("interest1"=>"Sports","interest2"=>"Food & Wine","interest3"=>"Shopping","interest4"=>"Museums and Galleries","interest5"=>"History","interest6"=>"Nature","interest7"=>"Cooking","interest8"=>"Hiking","interest9"=>"Live music","interest10"=>"Performing Arts","interest11"=>"Other");
    		  			$interestsforall=array();
    		  			for ($i = 1; $i <= 11; $i++)
    		  			{
    		  			 $preferences_interest=self::getPreference($CRSuserID_answer,"interest".$i);
    		  			 if (($preferences_interest["value"]!="")&&($preferences_interest["value"]=="on")) 
    		  			 {
    		  			 	$interestsforall["interest".$i]=$interest_options["interest".$i];
    		  			 }
    		  			}
    		  			if (count($interestsforall)>0) {
    		  				$totalinterests=implode(",",$interestsforall);
    		  				$answer[$keyanswertemp]=$totalinterests;
    		  			}
    		  			else
    		  			{
    		  				$answer[$keyanswertemp]="";
    		  			}   	    		  			
    		  		break;
    		  		
    		  		case "features" :
    		  			$feature_options=array("features_1"=>"Service","features_3"=>"Location","features_3"=>"Reputation","features_4"=>"Design style","features_5"=>"Sense of place","features_6"=>"Brand name","features_7"=>"Pool","features_8"=>"Gym","features_9"=>"Family-friendly service","features_10"=>"Price");
    		  			$features=array();
    		  			foreach ($feature_options as $key_feature=>$value_feature)
    		  			{
    		  				$preference_feature=self::getPreference($CRSuserID_answer,"features_".$i);    		  			   
    		  				$features[$key_feature]=$feature_options[$key_feature].":".$preference_feature["value"];
    		  			}
    		  			$totalfeatures=implode(",",$features);
    		  			$answer[$keyanswertemp]=$totalfeatures;		  			
    		  		break;
    		  		   
    		  		default:
    		  			$preference_others=self::getPreference($CRSuserID_answer,$keyanswertemp);
    		  			$answer[$keyanswertemp]=$preference_others["value"];
    		  		break;
    		  	}
    		  }
    		  else 
    		  {
    		  	$answer[$keyanswertemp]="";
    		  }		
    		}
    		//print_r($answer_temp);
    		$seeds++;
    		$ret[$seeds] = $answer;
    		$seeds++;
    	}
    	mysql_free_result($result);
    	mysql_close($connection);
    	return $ret;
    }
}


<?php namespace indagare\users;

include_once 'Favorite.php';
include_once 'db.php';
include_once 'notifications.php';
include_once 'passkey.php';

class User {
    
    // transient stuff
    private $SSOkey = "iDosK3HfsJY0fdCI";
    private $SSOiv = "0123456789abcdef";
    
    private $id;
    public $login = null;
    public $email;
    public $password;
    private $salt;
    public $first_name = null;
    public $last_name = null;
    public $middle_initial = null;
    public $prefix = null;
    private $role = null;
    public $membership_level  = null;
    public $membership_years  = null;
    public $membership_created_at = null;
    public $membership_expires_at = null;
    public $primary_street_address  = null;
    public $primary_street_address2 = null;
    public $primary_extended = null;
    public $primary_city = null;
    public $primary_state = null;
    public $primary_postal = null;
    public $primary_country = null;
    public $secondary_street_address = null;
    public $secondary_extended = null;
    public $secondary_city = null;
    public $secondary_postal = null;
    public $secondary_state = null;
    public $secondary_country =null;
    public $phone_home = "";
    public $phone_work = "";
    public $phone_mobile = "";
    public $pref_html_email = 1;
    private $wants_mailings;
    private $created_at;
    private $updated_at;
    private $remember_token_expires_at; 
    private $billing_country;
    private $referred_by;
    public $passkey_id = null;
    public $question_1 = null;
    public $question_2 = null;
    public $question_3 = null;
    public $question_4 = null;
    private $member_klass;
    private $pending;
    private $note;
    private $company;
    private $comments_count;
    private $is_author;
    private $avatar_id;
    private $bio;
    private $posts_count;
    private $remote_key;
    private $did_setup;
    private $time_zone;
    private $mobile_address;
    private $mobile_provider;
    private $took_quiz;
    private $primary_address_id;
    private $secondary_address_id;
    private $shipping_address_id;
    private $last_logged_in_at;
    private $show_name_type;
    private $show_member_level;
    private $show_avatar;
    private $show_bio;
    private $show_iq_points;
    private $show_destination_points;
    private $show_my_destinations;
    private $show_my_benefits;
    private $show_favorites;
    private $show_comments;
    private $show_posts;
    private $benefits;
    private $membership_status;
    private $notes;
    public  $wants_emails = 1;
    private $delta;
    private $is_booker;
    private $referred_by_member_id;
    private $referred_by_non_member;
    private $sent_welcome_kit_date;
    private $specialist_id;
    
    function __construct($id) {
        $this->id = $id;
    }
    
    public static function requestPwdReset($email) {
        $u = \indagare\db\CrmDB::getUserByEmail($email);
        if ($u != false) {
            $key = sha1($u->email . $u->login);
            
            if(count(\indagare\db\LocalCrmDB::getResetKeyMember($key)) == 0){
                \indagare\db\LocalCrmDB::addResetKey($u->id, $key);
            }
            
            \indagare\notify\EmailNotification::sendResetPWD($key, $email, $u->getDisplayName());          
            return true;
        }
        return false;
    }
    
    public static function encryptPwd($pwd, $salt) {
        return sha1("--" . $salt . "--" . $pwd . "--");
    }
    
    public static function createSalt($str) {
        return sha1($str);
    }
    
    public static function checkLogin($login) {
    	$additionals=array("membership_status"=>"active","joint"=>"AND");
        $u = \indagare\db\CrmDB::getUser($login,$additionals);
        if ($u != false) {
            return true;
        }
        return false;
    }
    
    public static function hasUserSession(){
        session_start();
        //print_r('User' . $_SESSION["userlogin"]);
        if (isset($_SESSION['userlogin'])) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public static function getSessionUserID() {
        session_start();
        if (isset($_SESSION['userid'])) {
            return $_SESSION['userid'];
        }
        else {
            return false;
        }
    }
    
    public static function getUserBySession(){
        session_start();
        //print_r('User' . $_SESSION["userlogin"]);
        if (isset($_SESSION['userlogin'])) {
            return \indagare\db\CrmDB::getUser($_SESSION['userlogin']);
        }
        else {
            return false;
        }
    }
    
    public static function hasFavorite($post_id) {
        if (count(\indagare\db\CrmDB::getFavorite(User::getSessionUserID(), $post_id)) == 0) {
            return false;
        }
        return true;
    }
    
    public static function addFavorite($post_id) {
        \indagare\db\CrmDB::createFavorite(new \indagare\users\Favorite(0, $post_id, User::getSessionUserID(), 0, 0));   
    }
    
    public static function getFavorites() {
       return \indagare\db\CrmDB::getFavorites(User::getSessionUserID());
    }
    
    public static function removeFavorite($user_id,$post_id) {
        \indagare\db\CrmDB::removeFavorite($user_id,$post_id);
    }
    
    public function getDisplayName() {
        return $this->first_name . " " . $this->last_name;
    }
    
    private function encryptSSOData($str, $key, $iv) {
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
        $str = $this->addpadding($str, $block);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, "", "cbc", "");
        mcrypt_generic_init($td, $key, $iv);
        $encrypted = mcrypt_generic($td, $str);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $base64encoded= base64_encode($encrypted);
        return urlencode ($base64encoded);
    }
    
    private function addpadding($string, $blocksize = 16){
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }
    
    public function validatePwd($pwd) {
        if (User::encryptPwd($pwd, $this->getSalt()) == $this->password) {
            return true;
        }
        return false;
    }

    public function startSession() {
        @session_start();
        $_SESSION['userlogin'] = $this->login;
        $_SESSION['userid'] = $this->id;
        $_SESSION['SSODATA'] = $this->encryptSSOData("firstName=" . $this->first_name . 
                "&lastName=" . $this->last_name . "&email=" . $this->email . "&memberId=" . $this->id, 
                $this->SSOkey, $this->SSOiv);
        \indagare\db\CrmDB::updateLastLogin($this->id);
        //session_write_close();
    }
    
    public function setLogin($login) {
        $this->login = $login;
    }
    
    public function setPassword($pwd) {
        $this->password = $pwd;
    }
    
    public function getEncryptedPassword() {
        return User::encryptPwd($this->password, $this->getSalt());
    }
    
    public function setSalt($salt) {
        $this->salt = $salt;
    }
    
    private function getSalt() {
        return $this->salt;
    }
    
    public function getID() {
        return $this->id;
    }
    
    public function setID($id) {
        $this->id = $id;
    }
    
    public function toString() {
        return 'id: ' . $this->id . 
                ', Login: ' . $this->login . 
                ', first_name: ' . $this->first_name .
                ', last_name: ' . $this->last_name . 
                ', email: ' .
                $this->email .
                ', level: ' .
                $this->membership_level .
                ', years: ' .
                $this->membership_years;
    }
    
    public function toJSON() {
        return "{ firstname: '" .
                $this->first_name .
                "', middle_initial: '" .
                $this->middle_initial .
                "', lastname: '" .
                $this->last_name .
                "', email: '" .
                $this->email .
                "', level: '" .
                $this->membership_level .
                "', years: '" .
                $this->membership_years .
        "' }";
    }
    
    
}

class AccountCreator {
    public $user;
    
    public static function getAccountCreator() {
        session_start();
        if (isset($_SESSION["accountCreator"])) {        	
            return $_SESSION["accountCreator"];
        }
        else {
            $acc = new AccountCreator();
            $acc->user = new User(0);
            $acc->user->membership_level  = 1;
            $acc->user->membership_years  = 1;
            $_SESSION["accountCreator"] = $acc;
            return $acc;
        }
        
    }
}

class Membership {
    private $id;
    private $level;
    public $name;
    public $p1;
    public $p2;
    public $p3;
    public $discount = 0;
    
    function getMembershipPrice($years) {
        $price = 0;
        switch ($years) {
            case 1 :
                $price = $this->p1/100;
                break;
            case 2 :
                $price = $this->p2/100;
                break;
            case 3 :
                $price = $this->p3/100;
                break;
            default :
                $price = 0;
        }
        if ($this->discount > 0 || $price > 0) {
            if ($years == 1)
                return $price * ((100 - $this->discount)/100);
        }
        return $price;
    }
    
    function __construct($id, $level, $name, $p1, $p2, $p3) {
        $this->id = $id;
        $this->level = $level;
        $this->name = $name;
        $this->p1 = $p1;
        $this->p2 = $p2;
        $this->p3 = $p3;
    }
    
    public function toJSON () {
    	return json_encode(array(
    		'id' => $this->id,
    		'level' => $this->level,
    		'name' => $this->name,
    		'discount' => $this->discount,
    		'p1' => ($this->getMembershipPrice(1) * 100),
    		'p2' => ($this->getMembershipPrice(2) * 100),
    		'p3' => ($this->getMembershipPrice(3) * 100),
    	));
    	/*
        return "{ id: " . $this->id .
        ", level: " . $this->level . 
        ", name: '" . $this->name .
        "', discount: " . $this->discount .
        ", p1: " . ($this->getMembershipPrice(1) * 100) . 
        ", p2: " . ($this->getMembershipPrice(2) * 100) .
        ", p3: " . ($this->getMembershipPrice(3) * 100) . 
        "}";
        */
    }
}

define( 'PROMOTION_TYPE_UNKNOWN', 0 );
define( 'PROMOTION_TYPE_DOLLAR_OFF', 1 );
define( 'PROMOTION_TYPE_PERCENT_OFF', 2 );

class Promotion {
    
    /**
     * The code used for this promotion
     * @var string
     */
    public $code = '';

    /**
     * The short name of this promotion
     * @var string
     */
    public $name = '';

    /**
     * The description of this promotion
     * @var string
     */
    public $description = '';

    /**
     * A short message displayed to the user to alert them about the promotion
     * @var string
     */
    public $message = '';

    /**
     * The type of discount.  Must be one of:
     * PROMOTION_TYPE_DOLLAR_OFF
     * PROMOTION_TYPE_PERCENT_OFF
     * @var integer
     */
    public $type = PROMOTION_TYPE_UNKNOWN;

    /**
     * The amount of the discount
     * @var float
     */
    public $amount = 0;

    public function __construct($code, $type, $amount, $name = '', $description = '', $message = '') {
        $this->code = $code;
        $this->type = $type;
        $this->amount = $amount;
        $this->name = ( empty( $name ) ? $code : $name );
        $this->description = $description;
        $this->message = $message;
    }
    
    /**
     * Applies the discount to the given rate.
     *
     * @param float $rate  The rate to apply the discount to.
     *
     * @return float The rate after the discount was applied.
     */
    public function apply( $rate ) {
        $return = floatval( $rate );

        if ( $this->type == PROMOTION_TYPE_DOLLAR_OFF ) {
            $return -= $amount;
    }
    
        if ( $this->type == PROMOTION_TYPE_PERCENT_OFF ) {
            $return *= ( 1 - ( $amount / 100 ) );
        }

        return $return;
    }
}

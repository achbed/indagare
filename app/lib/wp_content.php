<?php namespace indagare\wp;

include_once 'user.php';
include_once 'db.php';

class WPContent {
    
    public static function getContent($page) {
        switch ($page) {
            case "signup" :
            	//echo $page;
                return WPContent::getSignup();
            case "account" :
                return WPContent::getAccount();
            default :
                return "Content not available";
        }
    }
    
    private static function getAccount() {
        $userid = \indagare\users\User::getSessionUserID();
        $user = \indagare\db\CrmDB::getExtendedUserById($userid);
        $userExt = \indagare\db\LocalCrmDB::getUser($userid);
        $expDate = date('m/d/Y', strtotime($user->membership_expires_at));
        $content = "<script type='text/javascript'>\n
        	// WPContent::getAccount\n
            var user = {crmId:" . $userid . "," .
                "fname:" . json_encode($user->first_name) . "," .
                "lname:  " . json_encode($user->last_name) . "," . 
                "title: " . json_encode($user->prefix) . "," .
                "email:  " . json_encode($user->email) . "," . 
                "initial:  " . json_encode($user->middle_initial) . "," . 
                "prefix:  " . json_encode($user->prefix) . "," . 
                "addr1:  " . json_encode($user->primary_street_address) . "," . 
                "addr2:  " . json_encode($user->primary_street_address2) . "," .     
                "city:  " . json_encode($user->primary_city) . "," . 
                "state:  " . json_encode($user->primary_state) . "," . 
                "postal:  " . json_encode($user->primary_postal) . "," .  
                "country:  " . json_encode($user->primary_country) . "," . 
                "mb:  '" . $user->membership_level . "'," . 
                "mb_exp:  '" . $expDate . "'," . 
                "phone_h:  " . json_encode($user->phone_home) . "," . 
                "phone_w:  " . json_encode($user->phone_work) . "," . 
                "phone_m:  " . json_encode($user->phone_mobile) . 
                "};\n";
        //echo "userid: " . $userExt['id'];           
        if (isset($userExt['id'])){
            $content .= "var userExt = {birthday: '" . $userExt["birthday"] . 
                    "', assistent: " . json_encode($userExt["assistent_name"]) . 
                    ", assistentEmail: " . json_encode($userExt["assistent_email"]) . 
                    ", assistentPhone: " . json_encode($userExt["assistent_phone"]) . 
                    ", passport: " . json_encode($userExt["passport_country"]) .
                    ", contact_pref: " . json_encode($userExt["contact_pref"]) .
                    ", delivery_pref: " . json_encode($userExt["delivery_pref"]) .
                    //"', other: '" . $userExt["Other"] .
                    "};\n";
            $userFFAs = \indagare\db\LocalCrmDB::getFFAccounts($userExt["id"], 1);
            $content .= "var userFFAs = [];\n";
            for ($i = 0; $i < count($userFFAs); $i++) {
            	if(!empty($userFFAs[$i]["id"])) {
                $content .= "userFFAs[" . $i . "] = {id: " . $userFFAs[$i]["id"] . 
                        ", a:" . json_encode($userFFAs[$i]["airline"]) . ", n:" . json_encode($userFFAs[$i]["ff_number"]) . "};\n";
            }
            }
            $spouseArr = \indagare\db\LocalCrmDB::getFamilyMembers($userid, 1);
            if (count($spouseArr) > 0) {
                $content .= "var spouse = {name: " . json_encode($spouseArr[0]["name"]) . 
                        ", birthday: '" . $spouseArr[0]["birthday"] . 
                        "', email: " . json_encode($spouseArr[0]["email"]) . 
                        ", passport: " . json_encode($spouseArr[0]["passport_country"]) . "};";
                $spouseFFAArr = \indagare\db\LocalCrmDB::getFFAccounts($spouseArr[0]["id"], 2);
                $content .= "var spouseFfa = [];";
                foreach ($spouseFFAArr as $sFfa) {
                	if(!empty($sFfa["id"])) {
                    $content .= "spouseFfa.push({id:" . $sFfa["id"] . ", n:" . 
                            json_encode($sFfa["ff_number"]) . ", a:" . json_encode($sFfa["airline"]) . "});";
                }
            }
            }
            $childrenArr = \indagare\db\LocalCrmDB::getFamilyMembers($userid, 2);
            if (count($childrenArr) > 0) {
                $content .= "var children = [];\n";

                for ($i = 0; $i < count($childrenArr); $i++) {
                    $childrenFfaArr = \indagare\db\LocalCrmDB::getFFAccounts($childrenArr[$i]["id"], 2);

                    $cFfaArray = array();
                    foreach($childrenFfaArr as $childrenFfaArrItem) {
                    	if(empty($childrenFfaArrItem["id"])) {
                    		continue;
                        }
                    	$cFfaArray[] = array(
                    		"id" => $childrenFfaArrItem["id"],
                    		"ff_number" => $childrenFfaArrItem["ff_number"],
                    		"a" => $childrenFfaArrItem["airline"]
                    	);
                    }
                    $cFfa = json_encode( $cFfaArray );

                    if(!empty($childrenArr[$i]["id"])) {
                    $content .= "children[". $i ."] = {id: " . 
                            $childrenArr[$i]["id"] . 
                            ", birthday: '" . $childrenArr[$i]["birthday"] . 
                            "', name:" . json_encode($childrenArr[$i]["name"]) . ", ffa:" . $cFfa . "};\n";
                }
            }
            }


            $prefs = \indagare\db\LocalCrmDB::getPreferences($userid);
            $content .= "var userPrefs = [];\n";
            foreach ($prefs as $pref) {
                    $content .= "userPrefs.push({pref: " . json_encode($pref["preference"]) . ", value: " . 
                            json_encode($pref["value"]) . "});";
                }
        }
        
        $memberships = \indagare\db\CrmDB::getMemberships();
        $mb_js_arr = "[";
        for ( $i = 0; $i < count($memberships); $i++) {
            $mb_js_arr = $mb_js_arr . $memberships[$i]->toJSON();
            if ($i + 1 < count($memberships)){
                $mb_js_arr = $mb_js_arr . ",";
            }
        }
        $content .= "var mbs = " . $mb_js_arr . "];\n"; 
        $content .= "</script>\n";      
        $content = $content . file_get_contents( $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/indagare/app/resources/account.html');     
        return $content;
    }

    private static function getSignup() {  
        
        $memberships = \indagare\db\CrmDB::getMemberships();
        
        // 11/05/2015 New section for discount code
        $discount = 0; // percent of discount to be applied, if 0 n o dicount is given.
        if (isset($_GET["dc"])) {
            if ($_GET["dc"] == "mailinglist") {
                $discount = 20;
                
            }

        }
        // 11/05/2015 End discount code
        
        
        
        $mb_js_arr = "[";
        for ( $i = 0; $i < count($memberships); $i++) {
            
            $memberships[$i]->discount = $discount;
            
            $mb_js_arr = $mb_js_arr . $memberships[$i]->toJSON();
            if ($i + 1 < count($memberships)){
                $mb_js_arr = $mb_js_arr . ",";
            }
        }
        $mb_js_arr = $mb_js_arr . "]";
        $acc = \indagare\users\AccountCreator::getAccountCreator();
        $mb = "1";
        $rc = "";
        $reftype = 0;
        if (isset($_GET["mb"])) {
            $mb = $_GET["mb"];  
        }

        $showTrial = "false";
        $trial = "false";
        if (isset($_GET["trial"])) {
            if ("TRIAL-" == substr($_GET["trial"], 0, 6)) {
                $trial = $_GET["trial"]; 
            }
            $showTrial = "true";
        }
        else {
            //echo "mb: " . $acc->user->membership_level;
            if ($acc->user->membership_level != "trial"){
                //$mb = $acc->user->membership_level;
            }
        }  
        $mb_y = 0;
        if ($acc->user->membership_years != "trial") {
            $mb_y = $acc->user->membership_years;
        }    
        if (isset($_GET["referralcode"])) {
            $rc = $_GET["referralcode"];
            if ("TRIAL-B" == substr($_GET["referralcode"], 0, 7)) {
                $reftype = 1;
            }
        }
        //echo $acc->user->toString();
        $getstrings=array("pc","gdsType","cin","cout");                
        $content = "<script type='text/javascript'> 
            var rc = '" . $rc . "';
            var mb = " . $mb . "; 
            var dc = " . $discount . ";
            var trialCode = '" . $trial . "';
            var showTrial = " . $showTrial . ";
            var reftype = " . $reftype . ";
            var y = " . $mb_y . "; 
            var mbs = " . $mb_js_arr . "; 
            var acc = " . $acc->user->toJSON() . ";
            var reg = new RegExp('(^|&)source=([^&]*)(&|$)');
            var r = window.location.search.substr(1).match(reg);
            if (r!=null)
            {
             var redirect=unescape(r[2]);		
            }
            else
            {
             var redirect='';  		
            };
            ";
        //$content.="var ";
        $content.="var swifttriparm={};
        ";
        foreach ($getstrings as $keyget => $valueget) 
        {
        	if (isset($_GET[$valueget])) 
        	{
        	  $content.="swifttriparm['".$valueget."']='".$_GET[$valueget]."';
        	  ";
        	}
        	
        }
        $content.="</script>\n";
        //echo $content;
        $content = $content . file_get_contents( $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/indagare/app/resources/signup.html');   
        return $content;
    }
}

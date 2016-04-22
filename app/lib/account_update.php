<?php
include_once 'user.php';
include_once 'db.php';

/*foreach ($_POST as $key => $value) {
    echo $key . ' : ' . $value . '<br>';  
}*/
    //print_r($_POST);
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
    
    function buildBirthday($m, $d, $y) {
       return date( 'Y-m-d H:i:s', mktime(0, 0, 0, $m, $d, $y));
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
    
    $redirect = "https://".$_SERVER['HTTP_HOST']."/account/#tab4";
    header("Location: $redirect");

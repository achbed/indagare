<?php 
include_once 'user.php';
include_once 'db.php';
//echo "1";
if (isset($_REQUEST['submit'])&&$_REQUEST['submit']=="yes") 
{
	//$return=array();
	//print_r($_POST);
	$u = indagare\db\CrmDB::getUser($_POST['externaluser']);
	if ($u!=false)
	{	 
    if($u->validatePwd($_POST['externalpassword'])) 
    {
     $u->startSession();
     $getarray=array("pc","gdsType","cin","cout");
     $_POST['ssoToken']=$_SESSION['SSODATA'];
     $url_prefix="https://book.indagare.com/do/hotel/CheckHotelAvailability";
     $url=$url_prefix."?";
     foreach ($_POST as $keypost => $valuepost)
     {
         if ($keypost !== "externaluser" && $keypost !== "externalpassword"){
            $url.=$keypost."=".$valuepost."&";
         }
     }
     $url=substr($url,0,-1);
     //$return['url']=$url;
     //echo json_encode($return);
     header("Location: ".$url);
    }
    else 
    {
     header("Location: "."http://www.indagare.com/external-login/");  	
     //echo json_encode(array('url'=>""));  	
    }
	}
	else 
	{
	 header("Location: "."http://www.indagare.com/external-login/");
	 //echo json_encode(array('url'=>""));
	}	
}


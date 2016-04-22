<?php 
class IDSService {
	
	public static  $lowestRates=array();	
	public static function getAvl($chain, $hotels, $from, $to) 
	{
	   $url_prefix="http://data.windsurfercrs.com/dsb/rest/json?auth=8ffdd2b1-894a-4fcb-a125-a9113711207d&method=getIDSAvailability&";
	   $url=$url_prefix."chain=".$chain."&hotels=".$hotels."&from=".$from."&to=".$to;	   
	   $jsoncontents=file_get_contents($url);
	   $outputforjson=json_decode($jsoncontents,true);
	   if (count($outputforjson['result'])==0) 
	   {
	   	$outputforjson['errors']="We couldn't find any results in webservices";
	   }
	   return $outputforjson;	   	   
	}
	public static function getAvlByRateCode($rateCode,$chain, $hotels, $from, $to) 
	{		
		$avl_ratecode_temp=self::getAvl($chain, $hotels, $from, $to);
		$avl_ratecode=$avl_ratecode_temp['result'];
		$returns=array();
        $checkcode=false;
		if (count($avl_ratecode)>0)
		{
		 foreach ($avl_ratecode as $keyrate=>$valuerate) 
		 {
		 	$checkcode=false;
		 	foreach ($valuerate as $key2rate=>$value2rate)
		 	{
		 	  if ($key2rate=="rates") {
		 	  	foreach ($value2rate as $key3rate => $value3rate) 
		 	  	{		 	  				 	  	  
		 	  	  if ($value3rate['code']==$rateCode) 
		 	  	 {
		 	  	  $checkcode=true;
		 	  	 }
		 	  	}
		 	  	
		 	  }					 				 		
		 	}	
		 	if ($checkcode==true) 
		 	{
		 		$returns[]=$valuerate;
		 	}		 	
		 } 		 
		}
		else
		{
		  $returns=array();
		}
		return $returns;			
	}
	public static function submitReservation($allinputs) 
	{
	 $returns=array();
	 $error=array();
	 $datas=array();
	 //print_r($allinputs);
	 $loc_fields=array("fname"=>"First Name","lname"=>"Last Name","country"=>"Country","address"=>"Address","city"=>"City","state"=>"State","zip"=>"ZIP","phone"=>"Phone","email"=>"Email","purpose"=>"Purpose of Visit","cardname"=>"Name on Card","cardnumber"=>"Card Number","card_month"=>"Expiration","card_year"=>"Expiration","cvv"=>"CVV","checkbox1"=>"Terms&Conditions");
	 $loc_fields_keys=array_keys($loc_fields);
	 foreach ($allinputs as $key=>$value)
	 {
	  if (in_array($key,$loc_fields_keys)) 
	  {
	   switch ($key) 
	   {
	  	case 'email':
	  	  $checkmail=self::is_valid_email($value);
	  	  if ($checkmail==false) 
	  	  {
	  	  	$error[$key]="Please check the ".$loc_fields[$key]." format";
	  	  }	
	  	break;
	  		  		
	  	default:
	  	  if (($value=="")||($value=="0")) 
	  	  {
	  		$error[$key]="Please ensure ".$loc_fields[$key]." wouldn't be empty"	;
	  	  };
	  	break;
	   }
	   $datas[$key]=$value;
	  }
	  else 
	  {
	  	$datas[$key]=$value;
	  }			  	
	 }
	 $returns["data"]=$datas;
	 $returns["error"]=$error;
	 return $returns;	  
	}
	public static function is_valid_email($email, $test_mx = false)
	{
		if(eregi("^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email))
		{	if($test_mx)
			{
			 list($username, $domain) = split("@", $email);
			 return getmxrr($domain, $mxrecords);
			}
		    else
		    {
		     return true;
		    }
		}    
		else
		{	
		    return false;
		}
	}
	public static function getLowestRate($hotelCode,$chain, $hotels, $from, $to) 
	{
		$lowestrate_temp=self::getAvl($chain, $hotels, $from, $to);
		$lowestrate=$lowestrate_temp['result'];
		$returns=array();
		$allratesforroom=array();
		if (count($lowestrate)>0) 
		{
			foreach ($lowestrate as $keylowrate1 => $valuelowrate1) 
			{
				
				if ($valuelowrate1['code']==$hotelCode) 
			   {					
				if (is_array($valuelowrate1)) 
				{
				   foreach ($valuelowrate1 as $keylowrate2 => $valuelowrate2) 
				   {
				   	if (is_array($valuelowrate2)) 
				   	{
				   	 foreach ($valuelowrate2 as $keylowrate3 => $valuelowrate3) 
					 {
					 	if (is_array($valuelowrate3)) {
					 	 foreach ($valuelowrate3 as $keylowrate5 => $valuelowrate5) 
						 {
							if (is_array($valuelowrate5)) 
							{
								foreach ($valuelowrate5 as $keylowrate6 => $valuelowrate6) 
								{
									if (is_array($valuelowrate6)) 
									{
										foreach ($valuelowrate6 as $keylowrate7 => $valuelowrate7) {
											if ($keylowrate7=="net") 
											{
												$allratesforroom[]=$valuelowrate7;
											};
										};
									}
								};
							}

						 };
					 	}
						
					 };
				   	}
					
				   }
				}
				
			   }
			   $returns[$hotelCode]=min($allratesforroom);
			   $allratesforroom=array();
			};
			
		}
		else 
		{
		   $returns=array();
		}
		self::$lowestRates=$returns;
		return 	$returns;
	}
	
}






?>
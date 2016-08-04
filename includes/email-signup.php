<?php
//fill in these values for with your own information
$api_key = '2b2936e2f9a2f24744769910044b6b5b-us1';
$datacenter = 'us1';
//$list_id = '4962a21673'; // Indagare mailing list
$list_id = '013366b0c9'; // Indagare Test mailing list
$email = $_POST['email'];
$fname = '';
$lname = '';
if( isset($_POST['fname']) ) {
	$fname = $_POST['fname'];
}
if( isset($_POST['lname']) ) {
	$lname = $_POST['lname'];
}
$status = 'subscribed';
if(!empty($_POST['status'])){
    $status = $_POST['status'];
}
$url = 'https://'.$datacenter.'.api.mailchimp.com/3.0/lists/'.$list_id.'/members/';
$username = 'apikey';
$password = $api_key;
$data = array("email_address" => $email,"status" => $status,"merge_fields" => array( 'FNAME' => $fname, 'LNAME' => $lname) );
$data_string = json_encode($data);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$api_key");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);
$result=curl_exec ($ch);
curl_close ($ch);
echo $result;
?>
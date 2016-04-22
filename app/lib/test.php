<?php
$uid="200";
$url="http://staging.api.indagare.com/users/$uid/index_user";
//$url="https://www.google.com/imghp?hl=en&tab=ni"; 
//$url="http://sd.sina.com.cn/news/sdyw/2014-11-04/080178764.html";

//$payload = file_get_contents("http://staging.api.indagare.com/users/$uid/index_user",true, $ctx);
/* stream_context_set_default(array('http' => array('timeout' => 5)));
$headers = @get_headers($url);
if($file_headers[0] == 'HTTP/1.0 200 OK')
{
   $file_exists = true;
} else {
   $file_exists = false;
} */
//echo file_exists($url);
//echo "2";
//echo empty($headers);
//print_r($headers);
$timeout=5;
$header=null;
$curl = curl_init();
curl_setopt ($curl, CURLOPT_URL, $url);
curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
curl_setopt ($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
curl_setopt ($curl, CURLOPT_HEADER, (int)$header);
curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
$html = curl_exec ($curl);
$httpcode=curl_getinfo($curl,CURLINFO_HTTP_CODE);
if ($httpcode==200)
{
	echo "200";
}
else 
{
	echo "1";
}
	
curl_close ($curl);




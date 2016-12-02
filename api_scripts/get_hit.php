<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


$AWS_ACCESS_KEY_ID = "";
$AWS_SECRET_ACCESS_KEY = "";
$SERVICE_NAME = "AWSMechanicalTurkRequester";
$SERVICE_VERSION = "2014-08-15";

$operation = "GetHIT";
$HITId=$_REQUEST['hid'];

$timestamp = generate_timestamp(time());
$signature = generate_signature($SERVICE_NAME, $operation, $timestamp, $AWS_SECRET_ACCESS_KEY);



function generate_timestamp($time) {
  return gmdate("Y-m-d\TH:i:s\Z", $time);
}

function hmac_sha1($key, $s) {
  return pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
         pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $s))));
}

function generate_signature($service, $operation, $timestamp, $secret_access_key) {
  $string_to_encode = $service . $operation . $timestamp;
  $hmac = hmac_sha1($secret_access_key, $string_to_encode);
  $signature = base64_encode($hmac);
  return $signature;
}


function print_errors($error_nodes) {
  print "There was an error processing your request:\n";
  foreach ($error_nodes as $error) {
    print "  Error code:    " . $error->Code . "\n";
    print "  Error message: " . $error->Message . "\n";
  }
}


$url2 = "https://mechanicalturk.amazonaws.com/onca/xml"
. "?Service=" . urlencode($SERVICE_NAME)
. "&Operation=" . urlencode($operation)
. "&Version=" . urlencode($SERVICE_VERSION)
. "&Timestamp=" . urlencode($timestamp)
. "&AWSAccessKeyId=" . urlencode($AWS_ACCESS_KEY_ID)
. "&HITId=".urlencode($HITId)
. "&Signature=" . urlencode($signature);


$xml = simplexml_load_file($url2);

//print_r($xml);

//echo "<br><br><br>";
//echo $xml->HIT->Question;
preg_match('/.*vid=(?P<vid>\d+).*/',$xml->HIT->Question,$matches);
echo $matches['vid'];
//X=str_replace('&',' ',str_replace('>',' ',strstr($xml->HIT->Question,"vid=")));
//Xs=explode(X,' ');
//echo Xs[0];
/*if($xml->GetHITResult->Request->IsValid=="True")
  echo "OK";
else
  echo "X";
*/
//echo "<br>";


?>

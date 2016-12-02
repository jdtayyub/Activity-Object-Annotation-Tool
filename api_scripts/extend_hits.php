<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


include("parameters.php");


$operation = "ExtendHIT";
$HITId=$_REQUEST['hid'];

$ExpirationIncrementInSeconds=24*60*60;  //24 hours

$timestamp = generate_timestamp(time());
$signature = generate_signature($SERVICE_NAME, $operation, $timestamp, $AWS_SECRET_ACCESS_KEY);


include("api_functions.php");


//echo $HITId."<br>";

$url2 = "https://mechanicalturk.amazonaws.com/onca/xml"
. "?Service=" . urlencode($SERVICE_NAME)
. "&Operation=" . urlencode($operation)
. "&ExpirationIncrementInSeconds=".urlencode($ExpirationIncrementInSeconds)
. "&Version=" . urlencode($SERVICE_VERSION)
. "&Timestamp=" . urlencode($timestamp)
. "&AWSAccessKeyId=" . urlencode($AWS_ACCESS_KEY_ID)
. "&HITId=".urlencode($HITId)
. "&Signature=" . urlencode($signature);

//echo $url2;

$xml = simplexml_load_file($url2);

//print_r($xml);

if($xml->ExtendHITResult->Request->IsValid=="True")
  echo "OK";
else
  echo "X";

//echo "<br>";


?>

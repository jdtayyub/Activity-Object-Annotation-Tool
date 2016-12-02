<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include("parameters.php");


$operation = "GetHIT";
$HITId=$_REQUEST['hid'];

$timestamp = generate_timestamp(time());
$signature = generate_signature($SERVICE_NAME, $operation, $timestamp, $AWS_SECRET_ACCESS_KEY);


include("api_functions.php");


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

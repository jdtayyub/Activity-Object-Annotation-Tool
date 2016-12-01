<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$AWS_ACCESS_KEY_ID = "AKIAJKM6LPZABA4IEBLA";
$AWS_SECRET_ACCESS_KEY = "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";
$SERVICE_NAME = "AWSMechanicalTurkRequester";
$SERVICE_VERSION = "2014-08-15";

$operation = "CreateHIT";
$title = "Test Title2";
$description = "Test Description";

$url = "https://annotateme.com/activity_annotator.php?vid=40";
$frame_height = 800;  

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

function constructQuestion($url, $frame_height) {
     $question1 = '<ExternalQuestion xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2006-07-14/ExternalQuestion.xsd">';
     $question1 .= '<ExternalURL>'.$url.'</ExternalURL>';
     $question1 .= '<FrameHeight>'.$frame_height.'</FrameHeight>';
     $question1 .= '</ExternalQuestion>';

     return $question1;
}


$url2 = "https://mechanicalturk.sandbox.amazonaws.com/onca/xml"  
  . "?Service=" . urlencode($SERVICE_NAME)
  . "&Operation=" . urlencode($operation)
  . "&Title=" . urlencode($title)
  . "&Description=". urlencode($description)
  . "&Reward.1.Amount=.01"
  . "&Reward.1.CurrencyCode=USD"
  . "&Question=" . urlencode(constructQuestion($url, $frame_height))
  . "&AssignmentDurationInSeconds=1800"
  . "&LifetimeInSeconds=86400"
  . "&Version=" . urlencode($SERVICE_VERSION)
  . "&Timestamp=" . urlencode($timestamp)
  . "&AWSAccessKeyId=" . urlencode($AWS_ACCESS_KEY_ID)
  . "&Signature=" . urlencode($signature);


// Make the request
$xml = simplexml_load_file($url2);

print_r($xml);

// Check for and print results and errors
function print_errors($error_nodes) {
  print "There was an error processing your request:\n";
  foreach ($error_nodes as $error) {
    print "  Error code:    " . $error->Code . "\n";
    print "  Error message: " . $error->Message . "\n";
  }
}

$hit = $xml->HIT->HITId;
if ($hit) {
  print "HIT Number: " . $hit . "\n";
}

?>


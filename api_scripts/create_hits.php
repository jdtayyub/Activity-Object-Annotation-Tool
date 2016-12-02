<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//$AWS_ACCESS_KEY_ID = "";
//$AWS_SECRET_ACCESS_KEY = "";
$SERVICE_NAME = "AWSMechanicalTurkRequester";
$SERVICE_VERSION = "2014-08-15";

$operation = "CreateHIT";
$title = "Highly Paid: Simple activity video annotation (BONUSES up to 3.5 USD per assignment based on work quality)";
$description = "In this task you are asked to watch a video multiple times and write all the activities you observe using short labels (2-3 words). Please allow up to 1-2 minutes for the video to load.";


$assignment_duration=1200;
$reward=0.20;

//echo $description;

$url = "https://annotateme.com/activity_annotator.php?vid=";
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


//scan the video folder
$folder_list=scandir("../vids");

$arr = array(2,5);//3,5,6,10,11,12,13,14,15,21,22,23,24,25,41,42,43,44,45,47,48,53);
//for($v=2;$v<count($folder_list);$v++)

foreach ($arr as $key=>$id)
{
    $url_v=$url.$id;
    echo $url_v.'<br>';
    //$url_v=$url.substr($folder_list[$v],3);


    //check how many frame the video is
    $images=scandir("../vids/vid$id/images");
    echo (count($images)-2)."<br><br>";
    //$assignment_duration=??
    //$reward=??



    //https://mechanicalturk.amazonaws.com/onca/xml https://mechanicalturk.sandbox.amazonaws.com

    $url2 = "https://mechanicalturk.amazonaws.com/onca/xml"
  . "?Service=" . urlencode($SERVICE_NAME)
  . "&Operation=" . urlencode($operation)
  . "&Title=" . urlencode($title)
  . "&Description=". urlencode($description)
  . "&Reward.1.Amount=".$reward
  . "&Reward.1.CurrencyCode=USD"
  . "&Question=" . urlencode(constructQuestion($url_v, $frame_height))
  . "&AssignmentDurationInSeconds=".$assignment_duration
  . "&LifetimeInSeconds=10800"
  . "&Version=" . urlencode($SERVICE_VERSION)
  . "&Timestamp=" . urlencode($timestamp)
  . "&AWSAccessKeyId=" . urlencode($AWS_ACCESS_KEY_ID)
  . "&Signature=" . urlencode($signature)
  . "&QualificationRequirement.1.QualificationTypeId=00000000000000000040"
  . "&QualificationRequirement.1.Comparator=GreaterThan"
  . "&QualificationRequirement.1.IntegerValue=20"
  . "&QualificationRequirement.2.QualificationTypeId=000000000000000000L0"
  . "&QualificationRequirement.2.Comparator=GreaterThan"
  . "&QualificationRequirement.2.IntegerValue=80"
  . "&MaxAssignments=5"
  . "&Keywords=activity,%20video,%20labelling,%20annotation,%20ground+truth";




    /*
    $xml = simplexml_load_file($url2);
    echo $xml."<br>";
    $hit = $xml->HIT->HITId;
    if ($hit) {
      print "HIT Number: " . $hit . "\n";
    }
    */

}








// Check for and print results and errors
function print_errors($error_nodes) {
  print "There was an error processing your request:\n";
  foreach ($error_nodes as $error) {
    print "  Error code:    " . $error->Code . "\n";
    print "  Error message: " . $error->Message . "\n";
  }
}



?>

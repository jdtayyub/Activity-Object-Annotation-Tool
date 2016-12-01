<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


$AWS_ACCESS_KEY_ID = "AKIAJKM6LPZABA4IEBLA";
$AWS_SECRET_ACCESS_KEY = "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";
$SERVICE_NAME = "AWSMechanicalTurkRequester";
$SERVICE_VERSION = "2014-08-15";

$operation = "GetAssignmentsForHIT";
//$HITId=$_REQUEST['hid'];

$HITId=$_REQUEST['hid'];
//$AssignmentStatus=
$SortProperty="AcceptTime";
$PageSize=100;
$PageNumber=1;


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


//echo $HITId."<br>";

$url2 = "https://mechanicalturk.amazonaws.com/onca/xml"
. "?Service=" . urlencode($SERVICE_NAME)
. "&Operation=" . urlencode($operation)
. "&HITId=".urlencode($HITId)
. "&Version=" . urlencode($SERVICE_VERSION)
. "&Timestamp=" . urlencode($timestamp)
. "&AWSAccessKeyId=" . urlencode($AWS_ACCESS_KEY_ID)
. "&Signature=" . urlencode($signature)
. "&PageSize=100"
. "&PageNumber=". urlencode($PageNumber);



$xml = simplexml_load_file($url2);

echo "<head><style>
table {
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid black;
}
</style>
<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js\"></script>
<script>";
?>

$(document).ready(function(){
  $.post("get_hit.php",{hid:"<?php echo $HITId;?>"},
    function(data){
      $('#v').text(data);
    });


});



<?php
echo "</script></head>";


echo "Video <span id='v' ></span>";
echo "<table><tr><td>aid<td>wid<Td>Status<Td>Answer";
foreach($xml->GetAssignmentsForHITResult->Assignment as $a)
{
  echo "<tr><td>".$a->AssignmentId."<td>".$a->WorkerId."<td>".$a->AssignmentStatus."<td>".str_replace('\r','<br>',$a->Answer);
}
echo "</table>";




?>

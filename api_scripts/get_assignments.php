<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


include("parameters.php");


$operation = "GetAssignmentsForHIT";
//$HITId=$_REQUEST['hid'];

$HITId=$_REQUEST['hid'];
//$AssignmentStatus=
$SortProperty="AcceptTime";
$PageSize=100;
$PageNumber=1;


$timestamp = generate_timestamp(time());
$signature = generate_signature($SERVICE_NAME, $operation, $timestamp, $AWS_SECRET_ACCESS_KEY);


include("api_functions.php");

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

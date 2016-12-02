<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


$AWS_ACCESS_KEY_ID = "";
$AWS_SECRET_ACCESS_KEY = "";
$SERVICE_NAME = "AWSMechanicalTurkRequester";
$SERVICE_VERSION = "2014-08-15";

$operation = "SearchHITs";

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




?>

<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<style>
table {
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid black;
}
</style>
</head>

<?php

echo "<center><h1>Manage HITs<h1>";

$url2 = "https://mechanicalturk.amazonaws.com/onca/xml"
. "?Service=" . urlencode($SERVICE_NAME)
. "&Operation=" . urlencode($operation)
. "&AWSAccessKeyId=" . urlencode($AWS_ACCESS_KEY_ID)
. "&Timestamp=" . urlencode($timestamp)
. "&Version=" . urlencode($SERVICE_VERSION)
. "&PageSize=100"
. "&SortProperty=Expiration"
. "&Signature=" . urlencode($signature);


//echo $url2;

$xml = simplexml_load_file($url2);

$i=1;
echo "<form method='post' action='manage_hits.php'>";
echo "<table><tr><th><th>HIT id<th>Title<th>Expiring<th>Done<th>Renew";
$total=0;
$done=0;
foreach($xml->SearchHITsResult->HIT as $hit)
{
  //print_r($hit);
  $percentage_achieved=($hit->MaxAssignments-$hit->NumberOfAssignmentsAvailable)/($hit->MaxAssignments+0.00);
  $remaining_time_percentage=1-max((strtotime(str_replace("Z","",str_replace("T"," ",$hit->Expiration)))-time())/(1*24.0*60*60) ,0);

  $total+=$hit->MaxAssignments;
  $done+=($hit->MaxAssignments-$hit->NumberOfAssignmentsAvailable);

  echo "<tr><td>".$i++;
  ?>
  <td><a href='https://annotateme.com/api_scripts/get_assignments.php?hid=<?php echo $hit->HITId?>' ><?php echo $hit->HITId?></a>
  <?php
  echo "<td>".$hit->Title;

  /* onclick='$.post("get_hit.php",{hid:"<?php echo $hit->HITId?>"},
  //   function(data){
  //     alert("Video "+data);
   });'*/

  if($hit->MaxAssignments-$hit->NumberOfAssignmentsAvailable==$hit->MaxAssignments)
    echo "<td>";
  else {
    $remaining_mins=max(strtotime(str_replace("Z","",str_replace("T"," ",$hit->Expiration)))-time(),0)/60;
    $h=intval($remaining_mins/60);
    $m=intval($remaining_mins%60);
    //str_replace("T"," ",substr($hit->Expiration,0,16))." ".
    echo "<td style='color:white;background-color:rgba(".intval($remaining_time_percentage*255).",0,0,0.7)'>".$h.":".str_pad($m, 2,'0');
  }
  echo "<td style='color:white;background-color:rgb(0,".intval($percentage_achieved*255).",0)'>".($hit->MaxAssignments-$hit->NumberOfAssignmentsAvailable)."/".$hit->MaxAssignments."
  <td>";

    if($hit->MaxAssignments-$hit->NumberOfAssignmentsAvailable!=$hit->MaxAssignments)
    {
    ?>

    <input type='checkbox' name='cb_<?php echo $hit->HITId;?>' onclick='
    $.post("extend_hits.php",
    {hid:"<?php echo $hit->HITId;?>"},
    function(data)
    {

      if(data=="OK")
        location.reload();
      else
        alert("Error!");
    }
    );'>

<?php
  }
}

echo "<tr><td><td><td><td><td> [<span id='spn_done'>$done</span>/<span id='spn_total'>$total</span>]</table></center>";
?>

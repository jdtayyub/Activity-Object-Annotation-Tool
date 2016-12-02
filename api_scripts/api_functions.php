<?php
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

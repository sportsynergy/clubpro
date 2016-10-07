<?php
 

include '../application.php';

// Handle users coming from a Jonas club site
//$u = $this->params['url'];
// time=2323&vendor=323232&userid=2323&target-page=2323&value=2323

$time = $_REQUEST["time"];
$vendor = $_REQUEST["vendor"];
$userid = $_REQUEST["userid"];
$targetpage = $_REQUEST["target-page"];
$value = $_REQUEST["value"];

if (!isset($time) || !isset($vendor) || !isset($userid) || !isset($time) || !isset($targetpage) || !isset($value) ){
	die("invalid request");
} 

 
// Decode the "expected" signature
// - urldecode is not used, causes problems with "+" turning into " " (space)
// - base64_decode
$signature = base64_decode($value);
if (isDebugEnabled(1)) logMessage("choe3: signature: ". $signature);

 
// Concat params
$args = $time.'|'.$vendor.'|'.$userid.'|'.$targetpage;
if (isDebugEnabled(1)) logMessage("choe3: args: ". $args);
 
// Convert to correct encoding
$data = mb_convert_encoding($args, "UTF-16LE");
if (isDebugEnabled(1)) logMessage("choe3: data: ". $data);
 

// Turn setting into public key (string -> key)
$pubkeyid = openssl_get_publickey('./csc_pub.pem');
 
// Get Result of verification of signature
$ok = openssl_verify($data, $signature, $pubkeyid);

if ($ok == 1) {
 
	// All good, signature is ok
} elseif ($ok == 0) {
	if (isDebugEnabled(1)) logMessage("choe3: Invalid Request From Club Website ");

	return false;
} else {
	// echo "ugly, error checking signature";
	if (isDebugEnabled(1)) logMessage("choe3: Problem checking signagure ");
	return;
}
?>
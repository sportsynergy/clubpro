<?php
 

include '../application.php';

// Handle users coming from a Jonas club site
//$u = $this->params['url'];
// time=2323&vendor=323232&userid=2323&target-page=2323&value=2323

$time = $_REQUEST["time"];
$vendor = $_REQUEST["vendor"];
$userid = $_REQUEST["userid"];
$page = $_REQUEST["page"];
$value = $_REQUEST["value"];

if (!isset($time) || !isset($vendor) || !isset($userid) ||  !isset($page) || !isset($value) ){
	die("invalid request");
} 

 
header( 'Location: http://www.sportsynergy.net/'.$_SESSION["CFG"]["wwwroot"]."clubs/cs-squash/index.php?username=."$userid".&password=bff23476a33b6bfe3b0353826940edd3" ) ; 
?>
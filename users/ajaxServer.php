<?php
include("../application.php");

session_start();
header('Content-Type: text/xml; charset=UTF-8');
$action=$_REQUEST['action'];
$action=$_GET['action'];

if(isDebugEnabled(1) ) {
	logMessage("ajaxServer - Starting Ajax Server with action: $action");
}

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';
?>
<ajax-response> <response> <?php 
include("./userlookup.php");
?> </response></ajax-response>
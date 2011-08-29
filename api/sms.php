<?php
include("../application.php");
session_start();

//Commands
$next = "NEXT";


//Parse the message body


$parts = explode(" ", $_REQUEST['Body'] );
$id = $parts[0];
$command = $parts[1];

//Handle Errors
if( count($parts)!=2  ){ ?>
	<Response>
    <Sms>Invalid Request: Usage: [sportsynergy  id] NEXT</Sms>
	</Response>
<?
}
if($parts[1]==$next){ 
	
	//Look up the ID
	$query = "";
	
	
 } else{ ?>
	<Response>
    <Sms>Invalid Request: Usage: [sportsynergy  id] NEXT</Sms>
	</Response>
<? }
header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Sms>To: <?php echo $_REQUEST['To'] ?>-----From: <?php echo $_REQUEST['From'] ?> -----Body:<?php echo $_REQUEST['Body'] ?></Sms>
</Response>
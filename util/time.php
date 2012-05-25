<?php
$time = $_REQUEST['time'];
if( empty($time) ){
	echo "please enter a time";
} else{
	echo date("M d Y H:i:s", $time);	
}

?>
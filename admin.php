<?php

$DOC_TITLE = "System Administration Console";


$_SESSION["wantsurl"] = qualified_mewithq();
$_SESSION["siteprefs"]["clubid"] = 0;
$_SESSION["siteprefs"]["siteid"] = 0;

//Set the footer message
if( !isset($_SESSION["footermessage"]) ){
	$footerMessage = getFooterMessage();
	$_SESSION["footermessage"] = $footerMessage;
}

include ($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include ($_SESSION["CFG"]["templatedir"]."/adminpage.php");
include ($_SESSION["CFG"]["templatedir"]."/footer_yui.php");


/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/


?>

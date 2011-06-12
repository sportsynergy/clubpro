<?
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/
$DOC_TITLE = "System Administration Console";
$_SESSION["wantsurl"] = qualified_mewithq();
$_SESSION["siteprefs"]["clubid"] = 0;

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

<?
/*
 * $LastChangedRevision: 643 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-30 08:51:17 -0800 (Tue, 30 Dec 2008) $

*/
$DOC_TITLE = "System Administration Console";
$_SESSION["wantsurl"] = qualified_mewithq();
$_SESSION["siteprefs"]["clubid"] = 0;

//Set the footer message
if( !isset($_SESSION["footermessage"]) ){
	$footerMessage = getFooterMessage();
	$_SESSION["footermessage"] = $footerMessage;
}

include ($_SESSION["CFG"]["templatedir"]."/header.php");
include ($_SESSION["CFG"]["templatedir"]."/adminpage.php");
include ($_SESSION["CFG"]["templatedir"]."/footer.php");


/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/


?>

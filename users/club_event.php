<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/


include("../application.php");
require($_SESSION["CFG"]["libdir"]."/clubadminlib.php");

$DOC_TITLE = "Club Event";

//Load in Date
$eventid = $_REQUEST["clubeventid"];

//put this in session
if( isset($eventid)){
	$_SESSION["clubeventid"] = $eventid;
}


if( isDebugEnabled(1) ) logMessage("calling club event");


if (match_referer() &&  isset($_POST['cmd']) )  {
	
	$frm = $_POST;
	
	// Add user to Club Event
     if($frm['cmd']=='addtoevent'){
       		$userid = $frm['userid'];
        	$clubeventid = $frm['clubeventid'];
        	addToClubEvent($userid, $clubeventid);
        	
        }
        
	// Remove user from Club Event
     if($frm['cmd']=='removefromevent'){
       		$userid = $frm['userid'];
        	$clubeventid = $frm['clubeventid'];
        	removeFromClubEvent($userid, $clubeventid);
        	
        }
	
}


$clubEventResult = loadClubEvent($_SESSION["clubeventid"]);
$clubEventParticipants = getClubEventParticipants($_SESSION["clubeventid"]);
$alreadySignedUp = isClubEventParticipant($clubEventParticipants);

include ($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include ($_SESSION["CFG"]["templatedir"]."/club_event_form.php");
include ($_SESSION["CFG"]["templatedir"]."/footer_yui.php");


/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/





?>
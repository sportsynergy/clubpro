<?

include("../application.php");
require($_SESSION["CFG"]["libdir"]."/clubadminlib.php");

$DOC_TITLE = "Club Event";

//Load in Date
$eventid = $_REQUEST["clubeventid"];



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




/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/





?>
<?


/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */

include ("../application.php");
$DOC_TITLE = "Club Events";
require_priv("2");


if (isset($_POST['clubeventid']) && isset($_POST['cmd'])) {
	
	  
	if($_POST['cmd']=="remove"){
		removeClubEvent($_POST['clubeventid']);
	}
	
}
	
	
if (match_referer() && isset($_POST['submit'])) {
	
	//Do Something
	
}


$clubEvents = loadClubEvents( get_clubid() );

include ($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include ($_SESSION["CFG"]["templatedir"]."/club_events_form.php");
include ($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($searchname) {
	

}


function removeClubEvent($eventid){
	
	logMessage("club_events.removeClubEvent: removed $eventid");
	
	$query = "Update tblClubEvents events
				SET events.enddate = NOW() 
				WHERE events.id = $eventid";
	
	return db_query($query);
	
}



/**
 * 
 * @param  $clubId
 */
function loadClubEvents($clubid){
	
	$query = "SELECT * from tblClubEvents events
				WHERE events.clubid = $clubid and events.enddate is null ORDER BY events.eventdate";
	
	return db_query($query);
	
	
}
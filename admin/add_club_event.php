<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/

include ("../application.php");
require($_SESSION["CFG"]["libdir"]."/clubadminlib.php");

$DOC_TITLE = "Club Event Setup";
require_priv("2");

$eventid = $_REQUEST["clubeventid"];

if( isset($eventid) ){
	$clubEventResult = loadClubEvent($eventid);
	$clubEventArray = mysql_fetch_array($clubEventResult);
	$frm = $clubEventArray;
	
}

if (match_referer() && isset($_POST['submit'])) {
	
	$frm = $_POST;
    $errormsg = validate_form($frm, $errors);
	$wwwroot = $_SESSION["CFG"]["wwwroot"];

     if (empty($errormsg)){
	  	saveClubEvent($frm);
	  	header ("Location: $wwwroot/admin/club_events.php");
        die;
	
     }
	
}

include ($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include ($_SESSION["CFG"]["templatedir"]."/add_club_event_form.php");
include ($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
	
	
        $errors = new Object;
        $msg = "";


		if(empty($frm["name"]) ){
			$errors->subject = true;
			$msg .= "You did not specify an event name";
		}

        
        elseif( empty($frm["description"])  ){
        	 $errors->description = true;
        	$msg .= "You did not specify an event description";
        	return $msg;
        	
        }
        
        // Make sure date is ok
        $datesArray = explode("/",$frm["eventdate"]);
        $month = $datesArray[2];
		$day = $datesArray[1];
		$year = $datesArray[0];
		
		 $gmttime = mktime();
         $thisyear = date("Y", $gmttime);
        
        		
		if ( empty($frm["eventdate"]) ) {
                $errors->eventdate = true;
                $msg .= "You did not specify an event date";
        } 
        elseif( count($datesArray)!=3 ){
        	logMessage("This is the number of dates elements: ". count($datesArray));
        	$errors->eventdate = true;
            $msg .= "The date is not properly formatted";
        }
        elseif ( !is_numeric($month) && $month > 12) {
         	$errors->eventdate = true;
            $msg .= "The month is not properly formatted";
         }
	
		 elseif ( !is_numeric($day) && $day > 31) {
         	$errors->eventdate = true;
            $msg .= "The day is not properly formatted";
         }
		elseif ( !is_numeric($year) && ($year > $thisyear + 2 || $year < $thisyear - 1 ) ) {
         	$errors->eventdate = true;
            $msg .= "The year is not properly formatted";
         }
	 

        return $msg;

}


/**
 * Save Club Events
 * @param  $frm
 */
function saveClubEvent(&$frm){
	
	
	logMessage("add_club_event.saveClubEvent");
	
	   // Parse 
	   $datearray = explode( "/", $frm['eventdate']);
	   $month = $datearray[0];
	   $day = $datearray[1];
	   $year = $datearray[2];
	   $mysqldateformat = $year."-".$month."-".$day;
	   
	   $eventid = $frm['id'];
	
	   
	   logMessage("add_club_event.saveClubEvent: this is the date $mysqldateformat");
	   
		// Strip Slashes
		if(get_magic_quotes_gpc()){
			$subject=stripslashes($frm['name']);
			$description=stripslashes($frm['description']);
		}else{
			
			$subject=addslashes($frm['name']);
			$description=addslashes($frm['description']);
		}
		
	//Insert the Club Event

		if( !empty( $eventid ) ){
			
			logMessage("add_club_event.saveClubEvent: updating club event $eventid ");
			
			$query = "
		        UPDATE tblClubEvents SET
						name = '$subject'
		                ,eventdate = '$mysqldateformat'
		                ,description = '$frm[description]'
		                ,lastmodifier = ".get_userid()."
		        WHERE id = '$eventid'";
			
			
		}else{
			
			logMessage("add_club_event.saveClubEvent: adding new club event ");
			
			$query = "INSERT INTO tblClubEvents (
                name, clubid, eventdate, description, creator, lastmodifier
                ) VALUES (
                          '$subject'
					  	  ,".get_clubid()."
                          ,'$mysqldateformat'
                          ,'$description'
                          ,".get_userid()."
                          ,".get_userid()."
                          )";
		}
	
	
	$result = db_query($query);
			
			
}

?>
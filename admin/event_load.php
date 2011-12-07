<?


include("../application.php");
$DOC_TITLE = "Add Events";
require_priv("2");

//Set the http variables
$courtid = $_REQUEST["courtid"];
$time = $_REQUEST["time"];

if (match_referer() && isset($_POST['submitme'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $wwwroot = $_SESSION["CFG"]["wwwroot"];       
        $backtopage = "$wwwroot/admin/event_load.php?courtid=$courtid&time=$time";

        if ($errormsg){
             include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
             die;
        }

         else{
              add_events($frm);
              $wwwroot = $_SESSION["CFG"]["wwwroot"];
              header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=$time");
              die;
       }

}

//Load up the window for which the events can be loaded.
$reservationWindowArray = getReservationWindow($courtid, $time);



include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/event_load_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

		

        $errors = new Object;
        $msg = "";
		
		//Make sure that they selected everything
         if (empty($frm["eventid"])) {

                $msg .= "You did not specify an event name.";
         }
         elseif (empty($frm["starttime"])) {

                $msg .= "You did not specify a start time.";
         }
         elseif ($frm["endtime"]=="") {

                $msg .= "You did not specify an end time.";
         }
 		//Validate that the start is before end
         elseif ($frm["starttime"] > $frm["endtime"] ){

                $msg .= "The start time needs to occur before the end time.";
         }
         //If Duration is set, the duration should be set too.
         elseif ( empty( $frm["repeat"] ) &&  ! empty($frm["reoccurringduration"] )){

                $msg .= "You did not specify how often you would like this reservation to occur. Set something in the Repeat field.";
         }
         //If Duration is set, the duration should be set too.
         elseif ( !empty( $frm["repeat"] ) && $frm["repeat"] != "norepeat" &&  empty($frm["reoccurringduration"] )){

                $msg .= "You did not specify how far in the future you would like this to go.   Set something in the Duration field.";
         }
         // The times can't be the same, otherwise its not a block entry
         elseif($frm["starttime"] == $frm["endtime"]){

			$msg .= "Please specify a valid window, your start time and end time are both the same.";
			
         }
   
        return $msg;
}

/**
 * This function adds events.
 * 
 * In cases where an existing reservation has been made, the event will not.
 * 
 * A Reoccuring Block Event is a group of reoccurring events.  A reoccuring event
 * can reoccur every hour for a day, or can reoccur every week for a month.  The event
 * interval is how frequently the event occurs.  From the event _log form this is defaulted
 * as the court duration.
 */
function add_events(&$frm) {


	$initialStartTime = $frm['starttime'];
	$initialEndTime = $frm['endtime'];
	$courtId = $frm['courtid'];
	$eventId = $frm['eventid'];
	$courtduration = $frm['courtduration'];
	$cancelConflicts = empty( $frm['cancelconflicts']) ? false : true;
	$locked =  empty( $frm['lock'] ) ? "n" : "y" ;
	
	
	if( isDebugEnabled(2) ) logMessage("event_load.add_events: \n\tinitialStartTime: $initialStartTime \n\tinitialEndTime: $initialEndTime \n\tcourtId: $courtId \n\teventId: $eventId \n\tcourtduration: $courtduration \n\tcancelConflicts: $cancelConflicts ");
	
	
	//If this is not a reoccuring reservation.  This can be specificaly noted or this can be left blank.
	if(  $frm['repeat'] == "norepeat" || empty ($frm['repeat']) ){
		
		if( isDebugEnabled(2) ) logMessage("event_load.add_events: This is not a reoccuring block event.");	
		// When this field comes back empty there
		// was only one reservation slot to be made, to see how this is
		// done see the notes in event_load_form.php above this hidden
		// form field declaration.
		
		makeReoccurringReservations($initialStartTime, $initialEndTime, $courtId, $eventId, $courtduration, $cancelConflicts, null, $locked);
    
    
	}
	// This is a reoccuring reservation
	else{
		
		if( isDebugEnabled(2) ) logMessage("event_load.add_events: This is a reoccuring block ". $frm['repeat'] ." event for a ".$frm['reoccurringduration'].".");	
		
		if ($frm['repeat']=="daily"){
	

				//Add as block event
			     $reoccuringQuery = "INSERT INTO tblReoccurringBlockEvent (
							 creator
							) VALUES (
								".get_userid()."
								)";
										
			    $reservationResult = db_query($reoccuringQuery);
				$blockId = db_insert_id();
				$initialHourstart = 0;
				
				if( isDebugEnabled(2) ) logMessage("\t-> Setting Block Event Id: $blockId");


		         //Set the occurance interval
		         if($frm['reoccurringduration']=="week")
		         $numdays = 7;
		         if($frm['reoccurringduration']=="month")
		         $numdays = 30;
		         if($frm['reoccurringduration']=="year")
		         $numdays = 365;
		         if($frm['reoccurringduration']=="twodays")
		         $numdays = 2;
		         if($frm['reoccurringduration']=="threedays")
		         $numdays = 3;
		         if($frm['reoccurringduration']=="fourdays")
		         $numdays = 4;
		         if($frm['reoccurringduration']=="fivedays")
		         $numdays = 5;
		         if($frm['reoccurringduration']=="sixdays")
		         $numdays = 6;
	
		         for ($i = 0; $i < $numdays; $i++) {
				  
		         $startTime = gmmktime (gmdate("H",$initialStartTime),
		         						gmdate("i",$initialStartTime),
		         						gmdate("s",$initialStartTime),
		         						gmdate("n",$initialStartTime),
		         						gmdate("j", $initialStartTime)+$i,
		         						gmdate("Y", $initialStartTime));
		         						
 		         $endTime = gmmktime (gmdate("H",$initialEndTime),
				 						gmdate("i",$initialEndTime),
				 						gmdate("s",$initialEndTime),
				 						gmdate("n",$initialEndTime),
				 						gmdate("j", $initialEndTime)+$i,
				 						gmdate("Y", $initialEndTime));						
					
				
				 // Set the event interval.  This will be the duration for the court for that day
		        $dayOfWeek = gmdate("w", $startTime);
		        $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtId";
		        $courtHourResult = db_query($courtHourQuery);
		        $courtHourArray = mysql_fetch_array($courtHourResult);
		        $eventinterval = 3600*$courtHourArray["duration"];

		        //Adjust the start and endtimes.  If this is the second reservation then compare the hourstarts
		        //to see if an adjustment needs to be made.  For example, if the hourstart of the first day is 15
		        //and the second day is 30, then 15* 60 should be added to both start and end times.  If the hourstart
		        // of the fist day is 30 and the hourstart of the second day is 0, then 30 should be subtracted from
		        //both start and enttimes.
		        if($i > 0){
		        	$hourstart = $initialHourstart - $courtHourArray["hourstart"];
		        	$startTime -= ($hourstart * 60);
		        	$endTime -= ($hourstart * 60);
		        }
		        else{
		        	$initialHourstart = $courtHourArray["hourstart"];
		        }
		        
        
		        
				makeReoccurringReservations($startTime, $endTime, $courtId, $eventId, $eventinterval, $cancelConflicts, $blockId, $locked);
				
				}
				   
	       
		}
		 //Add the weekly event
	       elseif ($frm['repeat']=="weekly"){

					
				//Add as block event
			     $reoccuringQuery = "INSERT INTO tblReoccurringBlockEvent (
							 creator
							) VALUES (
								".get_userid()."
								)";
										
			    $reservationResult = db_query($reoccuringQuery);
				$blockId = db_insert_id();
				$initialHourstart = 0;
				
				if( isDebugEnabled(2) ) logMessage("\t-> Setting Block Event Id: $blockId");


		         //Set the occurance interval
		         if($frm['reoccurringduration']=="week")
		         	$numdays = 7;
		         if($frm['reoccurringduration']=="month")
		         	$numdays = 30;
		         if($frm['reoccurringduration']=="year")
		         	$numdays = 365;
	
		         for ($i = 0; $i < $numdays; $i += 7) {
						
									
			          $startTime = gmmktime (gmdate("H",$initialStartTime),
		         						gmdate("i",$initialStartTime),
		         						gmdate("s",$initialStartTime),
		         						gmdate("n",$initialStartTime),
		         						gmdate("j", $initialStartTime)+$i,
		         						gmdate("Y", $initialStartTime));
		         						
	 		         $endTime = gmmktime (gmdate("H",$initialEndTime),
					 						gmdate("i",$initialEndTime),
					 						gmdate("s",$initialEndTime),
					 						gmdate("n",$initialEndTime),
					 						gmdate("j", $initialEndTime)+$i,
					 						gmdate("Y", $initialEndTime));		
			
			          
			        	// Set the event interval.  This will be the duration for the court for that day
				        $dayOfWeek = gmdate("w", $startTime);
				        $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtId";
				        $courtHourResult = db_query($courtHourQuery);
				        $courtHourArray = mysql_fetch_array($courtHourResult);
				        $eventinterval = 3600*$courtHourArray["duration"];
				       
				        //Adjust the start and endtimes.  If this is the second reservation then compare the hourstarts
				        //to see if an adjustment needs to be made.  For example, if the hourstart of the first day is 15
				        //and the second day is 30, then 15* 60 should be added to both start and end times.  If the hourstart
				        // of the fist day is 30 and the hourstart of the second day is 0, then 30 should be subtracted from
				        //both start and enttimes.
				        if($i > 0){
				        	$hourstart = $initialHourstart - $courtHourArray["hourstart"];
				        	$startTime -= ($hourstart * 60);
				        	$endTime -= ($hourstart * 60);
				        }
				        else{
				        	$initialHourstart = $courtHourArray["hourstart"];
				        }  
			          
			      	makeReoccurringReservations($startTime, $endTime, $courtId, $eventId, $eventinterval, $cancelConflicts, $blockId, $locked);
			      	
			      
		       }
		       
		        
	       }
	       
			//Add the weekly event
	       elseif ($frm['repeat']=="biweekly"){

					
				//Add as block event
			     $reoccuringQuery = "INSERT INTO tblReoccurringBlockEvent (
							 creator
							) VALUES (
								".get_userid()."
								)";
										
			    $reservationResult = db_query($reoccuringQuery);
				$blockId = db_insert_id();
				$initialHourstart = 0;
				
				if( isDebugEnabled(2) ) logMessage("\t-> Setting Block Event Id: $blockId");


		         //Set the occurance interval
		         if($frm['reoccurringduration']=="week")
		         	$numdays = 7;
		         if($frm['reoccurringduration']=="month")
		         	$numdays = 28;
		         if($frm['reoccurringduration']=="year")
		         	$numdays = 365;
	
		         for ($i = 0; $i < $numdays; $i += 14) {
						
									
			          $startTime = gmmktime (gmdate("H",$initialStartTime),
		         						gmdate("i",$initialStartTime),
		         						gmdate("s",$initialStartTime),
		         						gmdate("n",$initialStartTime),
		         						gmdate("j", $initialStartTime)+$i,
		         						gmdate("Y", $initialStartTime));
		         						
	 		         $endTime = gmmktime (gmdate("H",$initialEndTime),
					 						gmdate("i",$initialEndTime),
					 						gmdate("s",$initialEndTime),
					 						gmdate("n",$initialEndTime),
					 						gmdate("j", $initialEndTime)+$i,
					 						gmdate("Y", $initialEndTime));		
			
			          
			        	// Set the event interval.  This will be the duration for the court for that day
				        $dayOfWeek = gmdate("w", $startTime);
				        $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtId";
				        $courtHourResult = db_query($courtHourQuery);
				        $courtHourArray = mysql_fetch_array($courtHourResult);
				        $eventinterval = 3600*$courtHourArray["duration"];
				       
				        //Adjust the start and endtimes.  If this is the second reservation then compare the hourstarts
				        //to see if an adjustment needs to be made.  For example, if the hourstart of the first day is 15
				        //and the second day is 30, then 15* 60 should be added to both start and end times.  If the hourstart
				        // of the fist day is 30 and the hourstart of the second day is 0, then 30 should be subtracted from
				        //both start and enttimes.
				        if($i > 0){
				        	$hourstart = $initialHourstart - $courtHourArray["hourstart"];
				        	$startTime -= ($hourstart * 60);
				        	$endTime -= ($hourstart * 60);
				        }
				        else{
				        	$initialHourstart = $courtHourArray["hourstart"];
				        }  
			          
			      	makeReoccurringReservations($startTime, $endTime, $courtId, $eventId, $eventinterval, $cancelConflicts, $blockId, $locked);
			      	
			      
		       }
		       
		        
	       }
	       
	        //Add the monthly event
	       elseif ($frm['repeat']=="monthly"){
	
			
			//Add as block event
		     $reoccuringQuery = "INSERT INTO tblReoccurringBlockEvent (
						 creator
						) VALUES (
							".get_userid()."
							)";
									
		    $reservationResult = db_query($reoccuringQuery);
			$blockId = db_insert_id();
			$initialHourstart = 0;
			
			if( isDebugEnabled(2) ) logMessage("\t-> Setting Block Event Id: $blockId");
	
	
	         //Set the occurance interval
	         if($frm['reoccurringduration']=="week")
	         $numdays = 1;
	         if($frm['reoccurringduration']=="month")
	         $numdays = 1;
	         if($frm['reoccurringduration']=="year")
	         $numdays = 12;
	
			         for ($i = 0; $i < $numdays; $i++) {
			
			         	$startTime = gmmktime (gmdate("H",$initialStartTime),
			         						gmdate("i",$initialStartTime), 
			         						gmdate("s",$initialStartTime),
			         						gmdate("n",$initialStartTime)+$i,
			         						gmdate("j",$initialStartTime)+$i,
			         						gmdate("Y", $initialStartTime));
			         						
			            
			            $endTime = gmmktime (gmdate("H",$initialEndTime),
			         						gmdate("i",$initialEndTime), 
			         						gmdate("s",$initialEndTime),
			         						gmdate("n",$initialEndTime)+$i,
			         						gmdate("j", $initialEndTime)+$i,
			         						gmdate("Y", $initialEndTime));

				       // Set the event interval.  This will be the duration for the court for that day
				        $dayOfWeek = gmdate("w", $startTime);
				        $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtId";
				        $courtHourResult = db_query($courtHourQuery);
				        $courtHourArray = mysql_fetch_array($courtHourResult);
				        
				       
				        //Adjust the start and endtimes.  If this is the second reservation then compare the hourstarts
				        //to see if an adjustment needs to be made.  For example, if the hourstart of the first day is 15
				        //and the second day is 30, then 15* 60 should be added to both start and end times.  If the hourstart
				        // of the fist day is 30 and the hourstart of the second day is 0, then 30 should be subtracted from
				        //both start and enttimes.
				        if($i > 0){
				        	$hourstart = $initialHourstart - $courtHourArray["hourstart"];
				        	$startTime -= ($hourstart * 60);
				        	$endTime -= ($hourstart * 60);
				        }
				        else{
				        	$initialHourstart = $courtHourArray["hourstart"];
				        }
				        
				        
				        makeReoccurringReservations($startTime, $endTime, $courtId, $eventId, $eventinterval, $cancelConflicts, $blockId, $locked); 
			
			       }
			       
			       
			       
	}
	else{
		if( isDebugEnabled(2) ) logMessage("The Repeat HTTP Variable wasn't set correctly");
	}
	

		
	}
    
}


/**
 * This returns the window for which the reservation can be booked.
 * There are Two calls to the database here.  The first is to get 
 * more information about the court, such as the hour start and duration.
 * The second call is for getting information on the club hours.
 * 
 * This function will return an array of start times for which the 
 * event reservations can be made fot the current day.
 */
function getReservationWindow($courtid ,$time){

	  $current_time = $_SESSION["current_time"];
	
	  if( isDebugEnabled(2) ) logMessage("event_load.getReservationWindow:ntId: courtid $courtid and time: $time and current time is: ".$current_time);
	
	  //The Day of the week for the time
	  $currDOW = gmdate("w",$time);
	  
	
	  //Get the Open and close time for the club
	  $hoursquery =  "SELECT hours.opentime, hours.closetime, hours.duration, hours.hourstart from tblCourtHours hours WHERE courtid='$courtid' AND dayid ='$currDOW' ";
						
	 $hoursresult = db_query($hoursquery);
	 $hoursarray = db_fetch_array($hoursresult);
	 
	 $otimearray = explode (":", $hoursarray[0]);
     $ctimearray = explode (":", $hoursarray[1]);
     $openHour = $otimearray[0];
     $closeHour = $ctimearray[0];
     
     $hourStart = $hoursarray['hourstart'];
     $duration = $hoursarray['duration']*3600;
     
    
     //Build out dates
     $thisMonth = gmdate("n", $time);
     $thisDate = gmdate("j", $time);
     $thisYear = gmdate("Y", $time);
     
     $courtOpenTime = gmmktime ($openHour, $hourStart, 0, $thisMonth, $thisDate, $thisYear);
     $courtCloseTime = gmmktime ($closeHour, $hourStart, 0, $thisMonth, $thisDate, $thisYear);
     
     $timeListArray = array();
     
     for($i=$courtOpenTime; $i<$courtCloseTime ; $i=$i+$duration){
     	
	     	//Only add those slots that occur after the current time
	     	if($current_time<$i){
	     		
	     		array_push($timeListArray, $i);
	     	}
	     	
     }

	return $timeListArray;

}

/**
 * Make a block reservation.  The eventinterval is how often the event will reoccur.
 * 
 * 
 */
function makeReoccurringReservations($startTime, $endTime, $courtId, $eventId, $eventinterval, $cancelConflicts, $blockId, $locked){
	
	
	if( isDebugEnabled(2) ) logMessage("event_load.makeReoccurringReservations: \n\tstartTime: $startTime \n\tendTime: $endTime \n\tcourtId: $courtId \n\teventId: $eventId \n\teventinterval: $eventinterval \n\tcancelConflicts: $cancelConflicts \n\tblockId: $blockId");
	
	$resrvationQuery = "SELECT reservation.time 
						FROM tblReservations reservation 
						WHERE reservation.time >= $startTime
						AND reservation.time <= $endTime
						AND reservation.courtid = $courtId
						AND reservation.enddate IS NULL";
	
	$reservationResult = db_query($resrvationQuery);
	
	//Put results in an array
	$confirmedReservationArray = array();
	while($confirmedReservation = db_fetch_array($reservationResult)){
		array_push($confirmedReservationArray, $confirmedReservation[0]);
	}

	//Go through the window specified
	for($i=$startTime; $i<=$endTime; $i=$i+$eventinterval){
		
			//This function will not overwrite existing reservations
			if( !in_array($i,$confirmedReservationArray  )){
					
					if( isDebugEnabled(2) ) logMessage("\t-> adding event reservation for court $courtId on $i");
					
					$resquery = "INSERT INTO tblReservations (
				                 courtid, eventid, time, creator, lastmodifier, locked
				                 ) VALUES (
				                           '$courtId'
				                           ,'$eventId'
				                           ,'$i'
											, ".get_userid()."
											, ".get_userid()."
											, '$locked')";
				
				    $resresult =  db_query($resquery);
			}
			
			else{
				
				
				//If they choose to cancel conflicts, then do so (and send out an email)
				if($cancelConflicts){
					
					cancelReservation($i, $courtId);
					
					if( isDebugEnabled(2) ) logMessage("\t-> adding event reservation for court $courtId on $i");
					
					$resquery = "INSERT INTO tblReservations (
				                 courtid, eventid, time, creator, lastmodifier, locked
				                 ) VALUES (
				                           '$courtId'
				                           ,'$eventId'
				                           ,'$i'
											, ".get_userid()."
											, ".get_userid()."
											,'$locked')";
				
				    $resresult =  db_query($resquery);
					
				}
				
			}
		//Only make one reservation when only one slot is available
		if( empty($eventinterval))
			break;
	}
	

	
	//Add as reoccuring event
        $reoccuringQuery = "INSERT INTO tblReoccuringEvents (
				courtid, eventinterval, starttime, endtime
				) VALUES (
					$courtId,
					$eventinterval,
					$startTime,
					$endTime)";
							
    $reservationResult = db_query($reoccuringQuery);
    $reoccuringEventId = db_insert_id();
    
    if( isDebugEnabled(2) ) logMessage("\t-> adding tblReoccuringEvents $reoccuringEventId");
    
     if( isset ($blockId)){
    	 //Add a block event entry
	     $reoccuringQuery = "INSERT INTO tblReoccurringBlockEventEntry (
					reoccuringblockeventid, reoccuringentryid
					) VALUES (
						$blockId,
						$reoccuringEventId
						)";
								
	    $reservationResult = db_query($reoccuringQuery);
	    $reoccuringBlockEventId = db_insert_id();
	    
	    if( isDebugEnabled(2) ) logMessage("\t-> adding tblReoccurringBlockEventEntry $reoccuringBlockEventId for block $blockId");
    	
     }
	
	
}



/**
 * This will cancel the reservation and send out emails.
 * 
 */
function cancelReservation($time, $courtId){
	
	if( isDebugEnabled(2) ) logMessage("event_load.cancelReservation for $time, $courtid");
	
	//Get the type of reservation (singles, doubles, event)
	$reservationTypeQuery = "SELECT * from tblReservations where enddate IS NULL 
							AND time = $time AND courtId = $courtId";						
	
	$reservationResult = db_query($reservationTypeQuery);
	$reservationArray = mysql_fetch_array($reservationResult);	
	
			
	//End Date the Reservation
	$endDateRservationQuery = "UPDATE tblReservations SET enddate = NOW() 
								WHERE time = $time 
								AND courtid = $courtId
								AND enddate is NULL";
	
     $reservationResult = db_query($endDateRservationQuery);
     
	//Dont' send out emails for events
	if($reservationArray['eventid'] > 0 ){
		return;
	}
	
	//Send out the email
	if($reservationArray['usertype'] == 0){
		
		if( isDebugEnabled(2) ) logMessage("\t-> Sending out emails for singles cancelation");
		
		cancel_singles($reservationArray['reservationid']);
	} else if($reservationArray['usertype'] == 1){
		
		if( isDebugEnabled(2) ) logMessage("\t-> Sending out emails for doubles cancelation");
		cancel_doubles($reservationArray['reservationid']);
	}
	
	
}



?>
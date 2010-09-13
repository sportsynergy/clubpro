<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $

*/

include("../application.php");
require($_SESSION["CFG"]["libdir"]."/reservationlib.php");

//Set the http variables
$courtid = $_REQUEST["courtid"];
$time = $_REQUEST["time"];
$userid = $_REQUEST["userid"];
$cmd = $_REQUEST["cmd"];


$DOC_TITLE = "Update Court Reservation";
require_loginwq();

// Set some variables
$backtopage = $_SESSION["CFG"]["wwwroot"]."/users/court_cancelation.php?time=$time&courtid=$courtid";
$currentDay = $_SESSION["CFG"]["wwwroot"]."/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()));
$wwwroot = $_SESSION["CFG"]["wwwroot"];

// Not calling match_referer as court cancelation is the one exception to the rule
// that a form will submit to itself
if (isset($_POST['cancelall'])) {
        
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);

         if ( empty($errormsg ) ){
             cancel_court($frm);
             
            //For modifications send out the obligatory email.
	        if($frm["cancelall"]==4){
	              confirm_singles($frm["reservationid"], false);
	        }
	        if($frm["cancelall"]==8){
	              confirm_doubles($frm["reservationid"], false );
	        }
	
	        header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ."");
	        
        }

}


include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/form_header.php");

                  $getAuthDetailsQuery = "SELECT tblCourts.siteid, tblCourtType.courttypeid
                                         FROM (tblCourts INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid)
                                         WHERE (((tblCourts.courtid)=$courtid))";

                  $getAuthDetailsResult = db_query($getAuthDetailsQuery);
                  $authDetailsArray = mysql_fetch_array($getAuthDetailsResult);

                  $currentCourtCourtTypeID = $authDetailsArray['courttypeid'];
                  $currentCourtSiteID = $authDetailsArray['siteid'];

//Send the user to the cancel-all-page-no-matter-what if.....
if( (isset($cmd) && $cmd=="cancelall") ){
     include($_SESSION["CFG"]["templatedir"]."/court_cancelation_cancelall_only_form.php");
}
else{
     include($_SESSION["CFG"]["templatedir"]."/court_cancelation_form.php");
}
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

		$errors = new Object;
		
		//Ids
		$playerOneId = $frm["player1"];
		$playerTwoId = $frm["player2"];
		$playerThreeId = $frm["player3"];
		$playerFourId = $frm["player4"];
		
		//Names
		$playerOneName = $frm["name1"];
		$playerTwoName = $frm["name2"];
		$playerThreeName = $frm["name3"];
		$playerFourName = $frm["name4"];
		
		//When an admin is making a new reservation the player fields will be empty whereas when
		// the reservation is being updated, they will be set to an empty string.  In either case, this 
		//validation just makes sure that duplicate players are not submited, nopartner and empty
		//variables are allowed as they indicate players looking for matches.
		
        //Check the court modification for doubles
         if($frm['cancelall']==8 ){

				 if( !isSoloReservationEnabled() && !isAtLeastTwoPlayerSpecifiedForDoubles($playerOneId, $playerTwoId, $playerThreeId, $playerFourId) ){
                 	return "Please specify at least two players.";
                 	$errors->partner = true;
                 }
                 elseif(!isAtLeastOnePlayerSpecifiedForDoubles($playerOneId, $playerTwoId, $playerThreeId, $playerFourId) ){
                 	return "Please specify at least one player.";
                 }
                 
                 else if(isAtLeastOnePlayerDuplicatedForDoubles($playerOneId, $playerTwoId, $playerThreeId, $playerFourId)){
                 	return "Please specify different people in the reservation..";
                 }
                 
                 //If a plain old user is making the reservation, make sure that ids are specified.  In other words,
                 // a player can't just type in the name, they have to pick it from the dropdown
                 elseif( isGuestPlayer($playerOneId, $playerOneName) ){
                 	$errors->name1 = true;
                 	return "It appears that you did not select the first player from the dropdown menu.  Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
                 }
                  elseif( isGuestPlayer($playerTwoId, $playerTwoName)  ){
                 	$errors->name2 = true;
                 	return "It appears that you did not select the second player from the dropdown menu.  Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
                 }
                  elseif( isGuestPlayer($playerThreeId, $playerThreeName) ){
                 	$errors->name3 = true;
                 	return "It appears that you did not select the third player from the dropdown menu.  Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
                 }
                  elseif( isGuestPlayer($playerFourId, $playerFourName)  ){
                 	$errors->name4 = true;
                 	return "It appears that you did not select the fourth player from the dropdown menu.  Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
                 }
                 
                 

         }

 		//Check the court modification for singles
		if( $frm['cancelall']==4 ){
			
			 if( isGuestPlayer($playerOneId, $playerOneName) ){
			 	$errors->name1 = true;
			 	return "It appears that you did not select the first player from the dropdown menu.  Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
			 }
			 elseif( isGuestPlayer($playerTwoId, $playerTwoName)  ){
			 	$errors->name2 = true;
                 return "It appears that you did not select the second player from the dropdown menu.  Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
			 	
			 }
			 elseif(!isAtLeastOnePlayerSpecifiedForSingles($playerOneId, $playerTwoId) ){
                 	return "Please specify at least one player.";
              }
              elseif(isAtLeastOnePlayerDuplicatedForSingles($playerOneId, $playerTwoId)){
                 	return "Please specify different people in the reservation..";
              }
		}


	if(!canIcancel($frm['courtid'], $frm['time'])){
	           return "Sorry, you are not authorized to cancel this court.";
	}

}



function canIcancel($courtid, $time) {


/* Check to see if the person is either one of the two players or the
Club administrator */

        $canIcanel = FALSE;

        //check to see if the person can an event.

        $eventQuery = "SELECT reservations.eventid, courts.clubid, sites.allowselfcancel
                       FROM tblReservations reservations, tblCourts courts, tblClubSites sites
                       WHERE reservations.courtid = courts.courtid
                       AND reservations.time = $time
                       AND reservations.courtid = $courtid
                       AND sites.siteid = ".get_siteid()."
					   AND enddate IS NULL";



       $eventQueryResult = db_query($eventQuery);
       $eventTypeRow = mysql_fetch_array($eventQueryResult);

        //Right off the bat check to see if site policy allows this
        if(get_roleid()==1 && $eventTypeRow['allowselfcancel']=='n'){
              if( isDebugEnabled(1) ) logMessage("court_cancelation.canIcancel: site doesn't allow self cancel");
              return FALSE;
        }

        if($eventTypeRow['eventid'] > 0){

            //if the user is an admin of the current club, let them through
              if(get_roleid()>1 && get_clubid()==$eventTypeRow['clubid']){
                 if( isDebugEnabled(1) ) logMessage("court_cancelation.canIcancel: " .get_userid(). " is an admin, they can canel an event");
                 $canIcanel = TRUE;
              }

        }
        else{

                 //Find out if this is a guest reservation.
                 $guesttypequery = "SELECT reservation.creator
                                    FROM tblReservations reservation
                                    WHERE reservation.guesttype = 1
                                    AND reservation.courtid  = $courtid 
                                    AND reservation.time=$time
									AND reservation.enddate is NULL";

                 $guesttyperesult = db_query($guesttypequery);
                 $isGuestReservation = mysql_num_rows($guesttyperesult);


                 if($isGuestReservation>0){

					$guestcreator = mysql_result($guesttyperesult, 0);
                  
                  //if the user is an admin of the current club, let them through
                          if(get_roleid()>1 || get_userid() == $guestcreator){
                                 if( isDebugEnabled(1) ) logMessage("court_cancelation.canIcancel: User is the one who made this guest reservation, they can cancel");
                                 $canIcanel = TRUE;
                          }

                 //Find out if this is a doubles reservation
                 }else{

                                  $usertypequery = "SELECT tblkpUserReservations.usertype, tblCourts.clubid
                                                    FROM (tblkpUserReservations
                                                    INNER JOIN tblReservations ON tblkpUserReservations.reservationid = tblReservations.reservationid)
                                                    INNER JOIN tblCourts ON tblReservations.courtid = tblCourts.courtid
                                                    WHERE (((tblReservations.courtid)=$courtid)
                                                    AND ((tblReservations.time)=$time))
													AND tblReservations.enddate IS NULL";

                                  // run the query on the database
                                 $usertyperesult = db_query($usertypequery);
                                 while($usertypearray = db_fetch_array($usertyperesult)){

                                           //if the user is an admin of the current club, let them through
                                           if(get_roleid()>1 && get_clubid()==$usertypearray[1]){
                                                      if( isDebugEnabled(1) ) logMessage("court_cancelation.canIcancel: Users an admin and the reservation is not a guest reservation, they can cancel");
                                                      $canIcanel = TRUE;
                                           }

                                             //Check to see if this reservation is for singles
                                             if($usertypearray[0]==0){
                                                  //Get users of this court
                                                  $canidoitquery ="SELECT tblkpUserReservations.userid
                                                                 FROM tblReservations
                                                                 INNER JOIN tblkpUserReservations
                                                                 ON tblReservations.reservationid = tblkpUserReservations.reservationid
                                                                 WHERE (((tblReservations.courtid)=$courtid)
                                                                 AND ((tblReservations.time)=$time))
																 AND tblReservations.enddate IS NULL";

                                                   // run the query on the database
                                                  $canidoitresult = db_query($canidoitquery);
                                                  while ($canidoitarray = db_fetch_array($canidoitresult)){
                                                       if ($canidoitarray[0]== get_userid()){
                                                                if( isDebugEnabled(1) ) logMessage("court_cancelation.canIcancel: Allowing a player to cancel their own singles court");
                                                                $canIcanel = TRUE;
                                                       }
                                                   }
                                             }

                                                 //Otherwise check to see if this is a doubles reservation
                                                 elseif($usertypearray[0]==1) {
                                                              //get the teams of this court
                                                             $teamidquery ="SELECT tblkpUserReservations.userid, tblkpUserReservations.usertype
                                                                            FROM tblReservations
                                                                            INNER JOIN tblkpUserReservations
                                                                            ON tblReservations.reservationid = tblkpUserReservations.reservationid
                                                                            WHERE (((tblReservations.courtid)=$courtid)
                                                                            AND ((tblReservations.time)=$time))
																			AND tblReservations.enddate IS NULL";
																			
                                                              // run the query on the database
                                                              $teamidresult = db_query($teamidquery);

                                                              // Find out if the logged in user in on the team
                                                              while ($teamidarray = db_fetch_array($teamidresult)){
                                                                      
   																	  // Check if the user was in the reservation but didn't have a partner
                                                                      if($teamidarray[1]==0 && $teamidarray[0]==get_userid()){
                                                                      	if( isDebugEnabled(1) ) logMessage("court_cancelation.canIcancel: Allowing a player to cancel their own doubles court probably because they didn't have a partner.'");
                                                                      	$canIcanel = TRUE;
                                                                      }    
                                                                          
                                                                      //Check if the user in on the team
                                                                      if($teamidarray[1]==1){
	                                                                      	 
	                                                                      	$canidoitquery = "SELECT tblkpTeams.userid
	                                                                                        FROM tblkpTeams
	                                                                                        WHERE (((tblkpTeams.teamid)=$teamidarray[0]))";
	                                                                       
	                                                                        $canidoitresult = db_query($canidoitquery);
	                                                                        while ($canidoitarray = db_fetch_array($canidoitresult)){;
	                                                                            if ( $canidoitarray[0]== get_userid()){
	                                                                               if( isDebugEnabled(1) ) logMessage("court_cancelation.canIcancel: Allowing a player to cancel their own doubles court even though they had a partner.");
	                                                                               $canIcanel = TRUE;
	                                                                            }
	                                                                        }
                                                                      	
                                                                      }
                                                                     
                                                              }
                                                 }

                              }


                    }          }
             //endif


             return $canIcanel;

}

/*
  **********************************************************************************************************
  * 
  *  Mostly there are 10 basic types of cancelations which are each having a differnt code in the cancellall
  *  requeset parameter.
  * 
  * 1=DEPRECATED
  * 2=DEPRECATED
  * 3=Cancels entire reservation
  * 4=Modify the players in a singles reservation
  * 5=Remove Current Team
  * 6=Remove a User from a doubles reservation who was looking for a partner.
  * 7=Remove a users opponent from a doubles reservation.
  * 8=Modify Doubles Reservation players
  * 9= Cancel Reoccuring Event
  * 10= Update an event
  *
  *
*/
function cancel_court(&$frm) {


/* Delete the tblReservations */

        //Before we do anything we are going to have to get the reservationid and the usertype
        $residquery = "SELECT tblReservations.reservationid, tblReservations.usertype, tblReservations.eventid,tblReservations.guesttype,tblReservations.matchtype
                      FROM tblReservations
                      WHERE (((tblReservations.courtid)='$frm[courtid]')
                      AND ((tblReservations.time)='$frm[time]')) 
					  AND tblReservations.enddate IS NULL";


         // run the query on the database
        $residresult = db_query($residquery);
        $residarray =  mysql_fetch_array($residresult);


		//Update the event
        if($frm['cancelall']==10){
        	
        	$updateEventQuery = "UPDATE tblReservations 
									SET eventid = $frm[events], lastmodifier = ".get_userid()."
									WHERE time = $frm[time]
									AND courtid = $frm[courtid]
									AND enddate IS NULL";
									
			db_query($updateEventQuery);
        	
        }
        /*
         * Cancel the reoccuring event
         */
        elseif($frm['cancelall']==9){
        	
        	
        	if( isDebugEnabled(2) ) logMessage("court_cancelation.cancel_court: cancelall = 9");
        	
			//Get all of the reoccuring events made for this court that haven't expired
        	$reOccuringEventQuery = "SELECT reoccuringevents.eventinterval,reoccuringevents.starttime,reoccuringevents.endtime, reoccuringevents.id, entry.reoccuringblockeventid
    									FROM tblReoccuringEvents reoccuringevents
										LEFT OUTER JOIN tblReoccurringBlockEventEntry entry ON reoccuringevents.id = entry.reoccuringentryid   
								WHERE reoccuringevents.courtid = $frm[courtid]
								AND reoccuringevents.endtime >= $frm[time]";
								
	        $reOccuringEventResult = db_query($reOccuringEventQuery);
	        while($reOccuringEventsArray = mysql_fetch_array($reOccuringEventResult)){
		
			$workingEventsArray = array();
		
		        //Put all of these events in an array (just those that are in the future)
				for($i = $reOccuringEventsArray['starttime']; $i <= $reOccuringEventsArray['endtime']; $i = $i + $reOccuringEventsArray['eventinterval']){
					
					array_push($workingEventsArray, $i);
					
				}
				
				//If its found in the array, delete all of them
				if(in_array($frm['time'], $workingEventsArray)){
					
					for($i = 0 ; $i < sizeof($workingEventsArray) ; $i++){
						
						// We have the reoccurring event, now check to see if there are others
						$reoccuringeventblockid = $reOccuringEventsArray['reoccuringblockeventid'];
						
						/*
						 * Only delete the events that are of the same type. For example if an event or an
						 * an actual user reservation was made before the reoccuring event, this thing
						 * will not be delete previous reservations
						 */
						
						if($workingEventsArray[$i] >= $frm['time'] ){
							
							if( isDebugEnabled(2) ) logMessage("\tenddating reservation for court ".$frm['courtid'] . " and time ". $workingEventsArray[$i]);
						
							$deleteEventQuery = "UPDATE tblReservations 
												SET lastmodifier = ".get_userid().", enddate = NOW() 
												WHERE courtid = $frm[courtid] 
												AND time = $workingEventsArray[$i]
												AND tblReservations.eventid = $residarray[eventid]";
												
							db_query($deleteEventQuery);
						}
						
					}
					
					//Delete the reoccuring event entry
					$deleteReoccursionQuery = "DELETE FROM tblReoccuringEvents  
												WHERE courtid = $frm[courtid] 
												AND starttime = $reOccuringEventsArray[starttime]";
												
					db_query($deleteReoccursionQuery);
					
				
				}

				
	        } 
	       
	       //Check to see if this was a part of a reoccurring block
	        if( $reoccuringeventblockid != null ){
	        	
	    		if( isDebugEnabled(2) ) logMessage("\tThis was a part of reservation block $reoccuringeventblockid");
	        	
	        	mysql_data_seek($reOccuringEventResult,0);
	        	
	        	// Go through this and for any reoccuring entry with the same block id, remove.
	        	while($reOccuringEventsArray = mysql_fetch_array($reOccuringEventResult)){
	        		
	        		//Reinitialize this thing.
	        		$workingEventsArray = array();
	        		
	        		if( $reOccuringEventsArray['reoccuringblockeventid']==$reoccuringeventblockid){
	        			
	        			//Put all of these events in an array
						for($i = $reOccuringEventsArray['starttime']; $i <= $reOccuringEventsArray['endtime']; $i = $i + $reOccuringEventsArray['eventinterval']){
							array_push($workingEventsArray, $i);
						}
						
						
						for($i = 0 ; $i < sizeof($workingEventsArray) ; $i++){
							/*
							 * Only delete the events that are of the same type. For example if an event or an
							 * an actual user reservation was made before the reoccuring event, this thing
							 * will not be delete
							 */
							if( isDebugEnabled(2) ) logMessage("\tenddating reservation for court ".$frm[courtid] . " and time ". $workingEventsArray[$i]);
							
							$deleteEventQuery = "UPDATE tblReservations 
												SET lastmodifier = ".get_userid().", enddate = NOW() 
												WHERE courtid = $frm[courtid] 
												AND time = $workingEventsArray[$i]
												AND tblReservations.eventid = $residarray[eventid]";
												
							db_query($deleteEventQuery);
							
						}
						
		
	        		}
	        		
	        	}
	        	
    			// Clean up the tblReoccurringBlockEvent and tblReoccurringBlockEventEntry
    			$deleteReoccursionQuery = "DELETE FROM tblReoccurringBlockEventEntry  
												WHERE reoccuringblockeventid = $reoccuringeventblockid";
				
				if( isDebugEnabled(2) ) logMessage("\tDeleting tblReoccurringBlockEventEntry $reoccuringeventblockid");						
				db_query($deleteReoccursionQuery);
				
				$deleteReoccursionQuery = "DELETE FROM tblReoccurringBlockEvent  
												WHERE id = $reoccuringeventblockid";
				
				if( isDebugEnabled(2) ) logMessage("\tDeleting tblReoccurringBlockEvent $reoccuringeventblockid");						
				db_query($deleteReoccursionQuery);
	        	
	        }
        	
        }
        
        /* If this is a solo reservation or is a guest reservation, delete everything.
         * If this is an event cancel it, modifing an event will have been called above.
         */
        elseif( $residarray['eventid'] != 0 || $residarray['guesttype'] == 1 || $residarray['matchtype'] == 5){

        $qid1 = db_query("UPDATE tblReservations 
							SET lastmodifier = ".get_userid().", enddate = NOW() 
							WHERE reservationid = $residarray[0]");
        }
        else{
       // Now we need to clean up the tblkpReservations
       //if this is a singles reservation we just need to remove the user from the
       // tblkpReservations.  If on the other hand this is a doubles reservation, we
       // need to first find out what team they are referring to then get rid of that team
       // in the same table



                  /*If this was a box league match update the boxhistory table.  This is applicable for
                   any cancellation type.
                   */

                 if($residarray['matchtype']==1){

                     $qid1 = db_query("DELETE from tblBoxHistory WHERE reservationid = $residarray[0]");

                 }

                  //Check to see what type of cancellation we are talking about here
                if($frm["cancelall"]==3 && $residarray[1]==0) {
                  cancel_singles($residarray[0]);

                    $qid1 = db_query("UPDATE tblReservations
									  SET enddate = NOW(), lastmodifier = ".get_userid()."
                                      WHERE reservationid = $residarray[0]");



                 }
                 //Cancel 4 is a modification of the reservation, whose players 
                 // is just rearranged
               elseif($frm["cancelall"]==4) {

               		if( isDebugEnabled(1) ) logMessage("court_cancelation.cancel_court: Just rearranging the reservation cancelall =  ".$frm["cancelall"]." player1 = ".$frm['player1']." player2 = ". $frm['player2']);
	
				    $qid1 = db_query("UPDATE tblReservations
									  SET lastmodifier = ".get_userid()."
                                      WHERE reservationid = $residarray[0]");

                   $qid2 = db_query("DELETE FROM tblkpUserReservations
                                      WHERE reservationid = $residarray[0]");

					// An admin will be able to remove players from a 
					// singles reservation by replacing the players
					// name with a blank in the player drop down
					if($frm['name1'] == ''){
						
						 $qid2 = db_query("INSERT INTO tblkpUserReservations (
	                        reservationid,userid
	                                 ) VALUES (
	                                 '$frm[reservationid]',
	                                 '$frm[player2]')");
						
						$qid2 = db_query("INSERT INTO tblkpUserReservations (
                            reservationid,userid
                                     ) VALUES (
                                     '$frm[reservationid]',
                                     0)");
                                     
					}
					elseif($frm['name2'] == ''){
						
						$qid2 = db_query("INSERT INTO tblkpUserReservations (
                            reservationid,userid
                                     ) VALUES (
                                     '$frm[reservationid]',
                                     $frm[player1])");
                                     
                        $qid2 = db_query("INSERT INTO tblkpUserReservations (
                            reservationid,userid
                                     ) VALUES (
                                     '$frm[reservationid]',
                                     0)");
						
					}
					else{
					// Otherwise just insert the players 
				
						//Make sure that the player making the reservation always 
                        // is listed first (when possible)
                        if( get_userid() == $frm['player2'] ){
                        	$firstplayerlisted = $frm['player2'];
                        	$secondplayerlisted = $frm['player1'];
                        	
                        }else{
                        	$firstplayerlisted = $frm['player1'];
                        	$secondplayerlisted = $frm['player2'];
                        }
                        
				
                  $qid2 = db_query("INSERT INTO tblkpUserReservations (
                            reservationid,userid
                                     ) VALUES (
                                     '$frm[reservationid]',
                                     '$firstplayerlisted')");


                  $qid2 = db_query("INSERT INTO tblkpUserReservations (
                            reservationid,userid
                                     ) VALUES (
                                     '$frm[reservationid]',
                                     '$secondplayerlisted')");
					}  
                	
                	confirm_singles($frm[reservationid],false);

                 }


                   
        

        //If the reservation has a usertype of 1, then what we have here is a doubles
        //reservation.
        elseif( $residarray[1]==1){

                    if ($frm["cancelall"]==8){
                           //This type of court cancelation is for modifying the reservation.  With this we first remove entries in the
                           //  tblkpUserReservations for the reservation and then add the team of player1 and player2 with team player3
                           // and player4.

									if( isDebugEnabled(1) ) logMessage("court_cancelation.cancel_court: cancelall 8");
									
									 //Check to see if this is now a Guest Reservation
									 if( isFrontDeskGuestDoublesReservation(
									 		$frm['player1'], 
									 		$frm['name1'],
									 		$frm['player2'],
									 		$frm['name2'],
									 		$frm['player3'],
									 		$frm['name3'],
									 		$frm['player4'],
									 		$frm['name4'] ) ){
									 	
									 	if( isDebugEnabled(1) ) logMessage("\tUpdating to a Guest Reservation");
									 	
									 	//Delete the old reservation entries
									 	$qid1 = db_query("DELETE FROM tblkpUserReservations WHERE reservationid = $residarray[0]");
									 	
									 	//Set the guesttype
									 	$qid1 = db_query("UPDATE tblReservations
																  SET guesttype = 1
					                                              WHERE reservationid = $residarray[0]");
									 	
									 	//Add the first team
									 	$team1name = "$frm[name1] - $frm[name2]";
									 	$qid2 = db_query("INSERT INTO tblkpGuestReservations (
		                                                        reservationid, name
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         '$team1name')");
		                                                         
		                                //Add the second team
		                                $team2name = "$frm[name3] - $frm[name4]";
		                                $qid2 = db_query("INSERT INTO tblkpGuestReservations (
		                                                        reservationid, name
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         '$team2name')");
									 	
							
									 }
									 //A regular old doubles reservation
									 else{
		
											if( isDebugEnabled(1) ) logMessage("\tRearranging the doubles players");
											
		                                       //Get the tblCourtType id for this court
		                                     $courttypefordoublesquery = "SELECT tblCourtType.courttypeid
		                                                                  FROM (tblCourts
		                                                                  INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid)
		                                                                  WHERE (((tblCourts.courtid)='$frm[courtid]'))";
		                                     $courttypefordoublesresult = db_query($courttypefordoublesquery);
		                                     $courttypefordoublesarray = mysql_fetch_array($courttypefordoublesresult);
		
		                                      $qid2 = db_query("DELETE FROM tblkpUserReservations
		                                                          WHERE reservationid = $residarray[0]");
		
											 //Set up player 1 and 2
											 if($frm['player1']!="" && $frm['player2']!=""){
											 	$team1 = getTeamIDForPlayers($courttypefordoublesarray[0], $frm['player1'], $frm['player2']);
											 	
											 	$qid2 = db_query("INSERT INTO tblkpUserReservations (
		                                                        reservationid,userid,usertype
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         '$team1',
		                                                         1)");
											 	
											 }
											 //If Both are set to nopartner
											 elseif($frm['player1']=="" && $frm['player2']==""){
											 
											 $qid2 = db_query("INSERT INTO tblkpUserReservations (
		                                                        reservationid,userid,usertype
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         '0',
		                                                         0)");
											 
											 }
											 elseif($frm['player1']!="" && $frm['player2']==""){
											 	
											 	 $qid2 = db_query("INSERT INTO tblkpUserReservations (
		                                                        reservationid,userid,usertype
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         $frm[player1],
		                                                         0)");
											 }
											 else{
											 	$qid2 = db_query("INSERT INTO tblkpUserReservations (
		                                                        reservationid,userid,usertype
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         $frm[player2],
		                                                         0)");
											 }
											 
											 //Setup Player 3 and 4
											  if($frm['player3']!="" && $frm['player4']!=""){
											 	$team2 = getTeamIDForPlayers($courttypefordoublesarray[0], $frm['player3'], $frm['player4']);
											 	
											 	$qid2 = db_query("INSERT INTO tblkpUserReservations (
		                                                reservationid,userid,usertype
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         '$team2',
		                                                         1)");
											 }
											  elseif($frm['player3']=="" && $frm['player4']==""){
											 	
											 	$qid2 = db_query("INSERT INTO tblkpUserReservations (
		                                                        reservationid,userid,usertype
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         '0',
		                                                         0)");
		                                                         
											 }
											elseif($frm['player3']!="" && $frm['player4']==""){
											 	
											 	$qid2 = db_query("INSERT INTO tblkpUserReservations (
		                                                        reservationid,userid,usertype
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         $frm[player3],
		                                                         0)");
											 }
											 else{
											 	
											 	$qid2 = db_query("INSERT INTO tblkpUserReservations (
		                                                        reservationid,userid,usertype
		                                                         ) VALUES (
		                                                         '$residarray[0]',
		                                                         $frm[player4],
		                                                         0)");
											 }
		                                      
                                      
                    				}
                                 
                                 $qid1 = db_query("UPDATE tblReservations
																  SET lastmodifier = ".get_userid()."
					                                              WHERE reservationid = $residarray[0]");

                       // Where cancelall is two remove the whole reservation
                    } elseif ($frm["cancelall"]==3){

                                   //Find out if there are any openings for the court.  By the way this same code
                                   //can be found in court_reservation.php for basically doing the same thing.
									
									$needpartnerquery = "SELECT reservationdetails.userid, reservationdetails.usertype
									                     FROM tblReservations reservations, tblkpUserReservations reservationdetails
														 WHERE reservations.reservationid = reservationdetails.reservationid
									                     AND reservations.courtid='$frm[courtid]'
									                     AND reservations.time='$frm[time]'
														 AND reservations.enddate is NULL
														ORDER BY reservationdetails.usertype, reservationdetails.userid";
									
									// run the query on the database
									$needpartnerresult = db_query($needpartnerquery);
									$playerOneArray = mysql_fetch_array($needpartnerresult);
									$playerTwoArray = mysql_fetch_array($needpartnerresult);
									
                                    
                                    //If this is like, a doubles reservation that only 
                                    //has one person send out the notice using the 
                                    //singles function, because basically thats what it is.
                                     if( $playerOneArray['userid']=="0" && $playerOneArray['usertype']=="0" && $playerTwoArray['usertype']=="0"){
                                    	
                                    	if( isDebugEnabled(1) ) logMessage("\tSending out emails to confirm cancelation to individual(s) in reservation");
                                    	cancel_singles($residarray[0]);
                                    }
                                    else{
                                    	
                                    	if( isDebugEnabled(1) ) logMessage("\tSending out emails to confirm cancelation to team(s) in reservation");
                                    	cancel_doubles($residarray[0]);
                                    }
                                    

                                    $qid1 = db_query("UPDATE tblReservations
														  SET enddate = NOW(), lastmodifier = ".get_userid()."
			                                              WHERE reservationid = $residarray[0]");
                                   
                          }
                          
                        //When the cancelall type is 5, 6, or 7 we loop through the teams
				        else{
				
				
							  //find out what the useids for this match
					        $teamresquery = "SELECT tblkpTeams.userid, tblkpTeams.teamid
					                         FROM tblReservations INNER JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
					                         INNER JOIN tblkpTeams ON tblkpTeams.teamid = tblkpUserReservations.userid
					                         WHERE (((tblkpUserReservations.reservationid)=$residarray[0]))";
					
					        // run the query on the database
					        $teamresresult = db_query($teamresquery);
						        
				            while($teamresarray =  db_fetch_array($teamresresult)){
				
				                                      //Check to see what type of cancellation we are talking about here
				                                      //echo "This is my cancelall balue:". $frm["cancelall"]."";
				
				                                     //Where cancelall is one just remove the current team
				
				                                      if ($frm["cancelall"]==5){
				
				                                               if($teamresarray[0]==get_userid()){
										
																$qid1 = db_query("UPDATE tblReservations
																				  SET lastmodifier = ".get_userid()."
									                                              WHERE reservationid = $residarray[0]");
				
				                                                 $qid2 = db_query("Update tblkpUserReservations SET userid=0,usertype=0
				                                                                   WHERE reservationid = $residarray[0]
				                                                                   AND userid=$teamresarray[1]" );         
				                                                 email_players($residarray[0], 1);
				                                               }
				
				                                      }
					                                 elseif ($frm["cancelall"]==6){
					
							                                 //if this user is taking themselves out of a reservation where they were looking for a
							                                 //partner we will go in an remove them (this takes considerably less work)
							
							                                 $amilookingforpartner = "SELECT userid
							                                                          FROM tblkpUserReservations
							                                                          WHERE reservationid = $residarray[0]
							                                                          AND userid= ".get_userid()."
							                                                          AND usertype = 0";
							
							                                 $amilookingforpartnerresult = db_query($amilookingforpartner);
							                                 if(mysql_num_rows($amilookingforpartnerresult)==1){
							
							                                    
				                                                 $qid1 = db_query("UPDATE tblReservations
																					  SET lastmodifier = ".get_userid()."
										                                              WHERE reservationid = $residarray[0]");  
							                                    
							                                    
							                                    $qid2 = db_query("Update tblkpUserReservations set userid=0
						                                                           WHERE reservationid = $residarray[0]
						                                                           AND usertype = 0
						                                                           AND userid=".get_userid()."" );
							
							                                  	email_players($residarray[0], 1);
					                                 		 }
							                                 else{
							                                  //Where cancelall is three just remove the current user from whatever
							                                 // team they are in.  There are a couple of steps here. First we have to
							                                 // Set the usertype of the tblkpUserReservations to 0 to indicate that
							                                 // the reservation has changes from a team to a single user.  The next
							                                 // step is to replace the teamid with the partners userid. Finally we will
							                                 // send out an email to all of the players in the club.
							
							                                      // Once we bump across the current userid we reset the usertype to 0
							
							                                                  if($teamresarray[0]==get_userid()){
							                                                      $teamid = $teamresarray[1];
							                                                      
							                                                      $qid1 = db_query("UPDATE tblReservations
																									  SET lastmodifier = ".get_userid()."
														                                              WHERE reservationid = $residarray[0]");  
							                                                      
							                                                      $qid2 = db_query("Update tblkpUserReservations set usertype=0
							                                                                                  WHERE reservationid = $residarray[0]
							                                                                                  AND userid=$teamresarray[1]" );
							
							                                                   //Now Replace the team id with the players partner's userid who cancelled.
							
							                                                   $partneridquery = "SELECT tblkpTeams.userid
							                                                                      FROM tblkpTeams INNER JOIN tblkpUserReservations ON tblkpTeams.teamid = tblkpUserReservations.userid
							                                                                      INNER JOIN tblReservations ON tblkpUserReservations.reservationid = tblReservations.reservationid
							                                                                      WHERE (((tblkpTeams.userid)<>$teamresarray[0])
							                                                                      AND ((tblkpTeams.teamid)=$teamresarray[1])
							                                                                      AND ((tblkpUserReservations.reservationid)=$residarray[0]))  ";
							
							                                                   // run the query on the database
							                                                   $partneridresult = db_query($partneridquery);
							                                                   $partneridval = mysql_result($partneridresult,0);
							
							                                                   $qid2 = db_query("Update tblkpUserReservations set userid=$partneridval
							                                                                                  WHERE reservationid = $residarray[0]
							                                                                                  AND userid=$teamresarray[1]" );
							
							                                                   email_players($residarray[0], 1);
							                                                   
							
							                                                  }
							
							                                  }
							                         //end else for looking for partner query.
							                         }
									               //Remove the current users opponent from the reservation
									                elseif ($frm["cancelall"]==7){
									
									                   // Once we bump across the current userid we reset the usertype to 0
									
									                                   if($teamresarray[0]==get_userid()){
									                                       $teamid = $teamresarray[1];
									
									                                    $qid1 = db_query("UPDATE tblReservations
																					  SET lastmodifier = ".get_userid()."
										                                              WHERE reservationid = $residarray[0]");  
									                                    
									                                    
									                                    $qid2 = db_query("Update tblkpUserReservations set usertype=0
									                                                                   WHERE reservationid = $residarray[0]
									                                                                   AND userid=$teamresarray[1]" );
									
									                                    $qid2 = db_query("Update tblkpUserReservations set userid=".get_userid()."
									                                                                   WHERE reservationid = $residarray[0]
									                                                                   AND userid=$teamresarray[1]" );
									
									                                    email_players($residarray[0], 1);
									                                    
									
									                                   }

									               }
				
				
				          }  
				
				
				        }



        }

        }


}

/**
 * Checks to see if this event falls within a reoccuring event.  If this is the last reservation in 
 * a set, false is returned as that reservation is not reoccuring.
 * 
 * 1. Get all of the current (not already ended) reoccuring events for this court
 * 2.) For each one of these found determine the timestamps for all of the occurances
 * 3.) See if the timestamp of the 
 * 
 */
 function isReoccuringReservation($time, $courtid){
 	
 	//Get all of the reoccuring event (1)
 	$reOccuringEventQuery = "SELECT reoccuringevents.eventinterval,reoccuringevents.starttime,reoccuringevents.endtime 
								FROM tblReoccuringEvents reoccuringevents
								WHERE reoccuringevents.courtid = $courtid
								AND reoccuringevents.endtime > $time";
								
	$reOccuringEventResult = db_query($reOccuringEventQuery);
	while($reOccuringEventsArray = mysql_fetch_array($reOccuringEventResult)){
		
		$reoccuringEventsArray = array();
		
		//Calculate the timestamps for each event in the set (still 1)
		for($i = $reOccuringEventsArray['starttime']; $i < $reOccuringEventsArray['endtime']; $i = $i + $reOccuringEventsArray['eventinterval']){
			array_push($reoccuringEventsArray, $i);
		}
		
		if(in_array($time, $reoccuringEventsArray)){
			return true;
		}

	}
	
	return false;
 	
 }
 
 /**
  * 
  */
 function deleteReoccurringEvent(){
 	
 }

?>
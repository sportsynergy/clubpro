<?


/**
 * Validates a doubles reservation.  Checks that of the four players entered that one is an actual player.  The way that the form may
 * be populated an updated spot may be represented by a zero, so just check that too.
 */
function isAtLeastOnePlayerSpecifiedForDoubles($playerOne, $playerTwo, $playerThree, $playerFour){
	
	if( isDebugEnabled(1) ) logMessage("reservationlib.isAtLeastOnePlayerSpecifiedForDoubles. values $playerOne, $playerTwo, $playerThree, $playerFour");
	
	if($playerOne != "" && $playerOne != "0"){
		return true;
	}
	else if($playerTwo != "" && $playerTwo != "0"){
		return true;
	}
	else if($playerThree != "" && $playerThree != "0"){
		return true;
	}
	else if($playerFour != "" && $playerFour != "0"){
		return true;
	}
		return false;

}


/**
 * Validates a doubles reservation.  Checks that of the four players entered that two is an actual player
 */
function isAtLeastTwoPlayerSpecifiedForDoubles($playerOne, $playerTwo, $playerThree, $playerFour){
	
	$count = 0;
	
	if( isDebugEnabled(1) ) logMessage("reservationlib.isAtLeastTwoPlayerSpecifiedForDoubles()");
	
	if($playerOne != ""){
		if( isDebugEnabled(1) ) logMessage("\tplayerOne $playerOne");
		++$count;
	}
	if($playerTwo != ""){
		if( isDebugEnabled(1) ) logMessage("\tplayerTwo $playerTwo");
		++$count;
	}
	if($playerThree != ""){
		if( isDebugEnabled(1) ) logMessage("\tplayerThree $playerThree");
		++$count;
	}
	if($playerFour != ""){
		if( isDebugEnabled(1) ) logMessage("\tplayerFour $playerFour");
		++$count;
	}
	
	if( isDebugEnabled(1) ) logMessage("\tNumber of players found: $count");
		
	if($count> 1){
		return true;
	}else{
		return false;
	}
		

}

/**
 * Validates a singles reservation.  Checks that of the two players entered that one is an actual player
 */
function isAtLeastOnePlayerSpecifiedForSingles($playerOne, $playerTwo){
	
	
	
	if($playerOne != ""){
		if( isDebugEnabled(1) ) logMessage("reservationlib.isAtLeastOnePlayerSpecifiedForSingles: playerOne is specified");
		return true;
	}
	else if($playerTwo != ""){
		if( isDebugEnabled(1) ) logMessage("reservationlib.isAtLeastOnePlayerSpecifiedForSingles: playerTwo is specified");
		return true;
	}

		if( isDebugEnabled(1) ) logMessage("reservationlib.isAtLeastOnePlayerSpecifiedForSingles: playerOne nor playerTwo is specified");
		return false;

}


/**
 * Validates a doubles reservation.  Only club guest are allowed to be in the reservation more than once.
 */
function isAtLeastOnePlayerDuplicatedForDoubles($playerOne, $playerTwo, $playerThree, $playerFour){
	
	//Check Player One
	if(!isClubGuest($playerOne) 
	&& isPlayerSpecified($playerOne)
	&& ($playerOne == $playerTwo || $playerOne == $playerThree || $playerOne == $playerFour)){
		return true;
	}

	//Check Player Two
	else if(!isClubGuest($playerTwo) 
	&& isPlayerSpecified($playerTwo)
	&& ($playerTwo == $playerOne || $playerTwo == $playerThree || $playerTwo == $playerFour)){
		return true;
	}
	
	//Check Player Three
	else if(!isClubGuest($playerThree) 
	&& isPlayerSpecified($playerThree)
	&& ($playerThree == $playerOne || $playerThree == $playerTwo || $playerThree == $playerFour)){
		return true;
	}
	
	//Check Player Four
	else if(!isClubGuest($playerFour) 
	&& isPlayerSpecified($playerFour)
	&& ($playerFour == $playerOne || $playerFour == $playerTwo || $playerFour == $playerThree)){
		return true;
	}
	
	return false;
}

/**
 * Validates a doubles reservation.  Only club guest are allowed to be in the reservation more than once.
 */
function isAtLeastOnePlayerDuplicatedForSingles($playerOne, $playerTwo){
	
	//Check Player One
	if(!isClubGuest($playerOne) 
	&& isPlayerSpecified($playerOne)
	&& ($playerOne == $playerTwo )){
		return true;
	}

	
	return false;
}

/**
 * A Guest Player is one where the name is specified, but not the id (the person simply types the name in but doesn't
 * select them from the dropdown.)
 */
function isGuestPlayer($id, $name){
	
	if( isDebugEnabled(1) ) logMessage("reservatiolib.isGuestPlayer: $id, $name");
	 
	if( !empty($name) && ( empty($id) || $id==0) ){
		
		if( isDebugEnabled(1) ) logMessage("This is a Guest Reservation");
		return true;
	}
	
	return false;
}
/************************************************************************************************************************/
/*
     This function returns the email addresses of all players in the reservation 
     (except the current user)
*/
function getEmailAddressesForReservation($eservationId){
	
	$emailAddresses = array ();
	
	//Gather up email addresses for any singles players in the reservation.
	//This will include players not in a team in a doubles reservation
	$singlesQuery = "SELECT users.email 
					 FROM  tblReservations reservations, tblkpUserReservations reservationentry, tblUsers users 
					 WHERE reservations.reservationid = $eservationId
					 AND reservationentry.userid = users.userid
					 AND reservations.reservationid = reservationentry.reservationid
					 AND reservationentry.usertype = 0
					 AND users.email != ''
				     AND reservationentry.userid <> ".get_userid()."
					 AND reservations.enddate IS NULL";
					
	$singlesResult = db_query($singlesQuery);

	while ($user = mysql_fetch_row($singlesResult)) {
		array_push($emailAddresses, $user[0]);
	}
	
	//Now round them up for teams
	$dobulesQuery = "SELECT users.email
	            	FROM tblCourts courts, tblReservations reservations, tblUsers users, tblkpTeams teamdetails, tblkpUserReservations reservationdetails, tblMatchType matchtype
					WHERE reservationdetails.reservationid = reservations.reservationid
	 				AND teamdetails.teamid = reservationdetails.userid
	            	AND users.userid = teamdetails.userid
					AND reservationdetails.usertype = 1
	            	AND courts.courtid = reservations.courtid
					AND reservations.matchtype = matchtype.id
	            	AND reservationdetails.reservationid=$eservationId
					AND users.email != ''
					AND users.userid <> ".get_userid()."";

	$doublesResult = db_query($dobulesQuery);

	while ($user = mysql_fetch_row($doublesResult)) {
		array_push($emailAddresses, $user[0]);
	}

	return $emailAddresses;
	
}

/**
 * Returns a comma delimited list of email addresses
 */
function getBuddyEmailAddresses($userId){
	
	   if( isDebugEnabled(1) ) logMessage("reservationlib.getBuddyEmailAddresses: Starting for userid:".$userId);
    
	   $emailAddresses = array ();
	
	 //List out all of the players Buddies
       $query = "SELECT users.email
                 FROM tblUsers users, tblClubUser clubuser, tblBuddies buddies
                 WHERE users.userid = buddies.buddyid
				 AND clubuser.userid = users.userid
                 AND  buddies.userid=$userId
				 AND clubuser.enddate IS NULL
                 AND clubuser.enable = 'y'";

       // run the query on the database
       $result = db_query($query); 
	
		while ($user = mysql_fetch_row($result)) {
			array_push($emailAddresses, $user[0]);
		}

		if( isDebugEnabled(1) ) logMessage("reservationlib.getBuddyEmailAddresses: all done for userid:".$userId);
    
	return $emailAddresses;
}

/**
 * With the userid, print out the users first and last name
 */
function printPlayer($firstname, $lastname, $userId, $creator){
	
  echo "$firstname $lastname";
    
  if($creator == $userId){
    	echo "*";
   }
  	
}

/**
 * Prints the two players first and last names
 */
function printTeam($teamId, $creator){
	
	 $teamnamequery = "SELECT users.firstname, users.lastname, users.userid
					      FROM tblUsers users,  tblkpTeams teams
					   WHERE users.userid = teams.userid
					   AND teams.teamid=$teamId";
					
    $teamnameresult = db_query($teamnamequery);
    $teamnamearray = db_fetch_array($teamnameresult);
    
    echo "$teamnamearray[0] $teamnamearray[1]";
     
    if($creator == $teamnamearray[2]){
    	echo "*";
    }
	
	echo "-";
	$teamnamearray = db_fetch_array($teamnameresult);
	
	echo "$teamnamearray[0] $teamnamearray[1]"; 
	
	if($creator == $teamnamearray[2]){
    	echo "*";
    }
   
}
 
/**
 * Gets pretty much everything needed for the court reservation screen for a user.
 */
function getSinglesReservationUser($reservationid){
	
	$useridquery = "SELECT reservations.time, reservations.usertype, users.firstname, users.lastname,  users.userid, rankings.ranking, reservations.matchtype
                   FROM tblCourts courts, tblReservations reservations, tblUsers users, tblUserRankings rankings, tblCourtType courttype, tblkpUserReservations reservationdetails, tblClubUser clubuser
				   WHERE rankings.userid = users.userid
                   AND courttype.courttypeid = rankings.courttypeid
				   AND courts.courtid = reservations.courtid
                   AND reservationdetails.userid = users.userid
                   AND reservations.reservationid = reservationdetails.reservationid
                   AND courts.courttypeid = courttype.courttypeid
                   AND reservationdetails.reservationid=$reservationid
                   AND reservations.usertype=0
				   AND rankings.usertype=0
				   AND users.userid = clubuser.userid
				   AND clubuser.clubid = ".get_clubid()."
                   ORDER BY reservationdetails.id";

   return db_query($useridquery);

}


//Determines if this is a guest doubles reservation
function isFrontDeskGuestDoublesReservation($id1, $name1, $id2, $name2, $id3, $name3, $id4, $name4){
	
	 if( isDebugEnabled(1) ) logMessage("reservationlib.isFrontDeskGuestDoublesReservation: $id1, $name1, $id2, $name2, $id3, $name3, $id4, $name4");
	 
	if (
		empty($id1) && !empty($name1)
		|| empty($id2) && !empty($name2)
		|| empty($id3) && !empty($name3)
		|| empty($id4) && !empty($name4)
	){
		return true;
	}else{
		return false;
	}
}

//Determines if this is a guest doubles reservation
function isGuestDoublesReservation($id1, $name1, $id2, $name2, $id3, $name3){
	
	 if( isDebugEnabled(1) ) logMessage("reservationlib.isGuestDoublesReservation: $id1, $name1, $id2, $name2, $id3, $name3");
	
	if (
		empty($id1) && !empty($name1)
		|| empty($id2) && !empty($name2)
		|| empty($id3) && !empty($name3)

	){
		return true;
	}else{
		return false;
	}
}


?>

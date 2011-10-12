<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */





/**
 * 
 * @param $clubEventParticipants
 */
function isClubEventParticipant(&$clubEventParticipantsResult){
	
	$isSignedup = false;
	
	logMessage("clubadminlib.isClubEventParticipant: Checking to see if ". get_userid(). " is signed up");
	
	$numrows = mysql_num_rows($clubEventParticipantsResult);
	
	 while($participant = mysql_fetch_array($clubEventParticipantsResult)){
	 	
	 	if( $participant['userid']==get_userid()){
	 		$isSignedup = true;
	 	}
	 	
	 }
	
	 // Reset the results
	 if( mysql_num_rows($clubEventParticipantsResult) > 0){
	 	mysql_data_seek($clubEventParticipantsResult,0);
	 }
	 
	 
	 return $isSignedup;
	 
	
}

/**
 * 
 * @param unknown_type $userid
 * @param unknown_type $clubeventid
 */
function addToClubEvent($userid, $clubeventid){
	
	logMessage("clubadminlib.addToClubEvent: User $userid ClubEventId: $clubeventid");
	
	$check = "SELECT count(*) FROM tblClubEventParticipants participants WHERE participants.userid = $userid AND participants.clubeventid = $clubeventid";
	$checkResult = db_query($check);
	$num = mysql_result($checkResult,0);
	
	if($num == 0){
		
		$query = "INSERT INTO tblClubEventParticipants (
                userid, clubeventid
                ) VALUES (
                          '$userid'
					  	  ,'$clubeventid'
                          )";
	
	   $result = db_query($query);
		
	} else {
		logMessage("clubadminlib.addToClubEvent: User $userid is already in  $clubeventid not doing anything.");
	}
	
	
	
}

/**
 * 
 * @param unknown_type $userid
 * @param unknown_type $clubeventid
 */
function removeFromClubEvent($userid, $clubeventid){
	
	logMessage("clubadminlib.removeFromClubEvent: User $userid ClubEventId: $clubeventid");
	
	
	$query = "UPDATE tblClubEventParticipants SET enddate=NOW() 
				WHERE userid='$userid'
				AND clubeventid = '$clubeventid'";
	
	$result = db_query($query);
	
}


/**
 * 
 * @param $eventid
 */
function loadClubEvent($eventid){
	
	logMessage("clubadminlib.loadClubEvent: Eventid $eventid");
	
	
	$query = "SELECT events.id, events.name, events.eventdate, events.description
			   FROM tblClubEvents events 
				WHERE events.id = '$eventid' 
				AND enddate is NULL";
	
	return db_query($query);
	
}



?>
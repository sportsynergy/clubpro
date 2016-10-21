<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */
/**
* Class and Function List:
* Function list:
* - isAtLeastOnePlayerSpecifiedForDoubles()
* - isAtLeastTwoPlayerSpecifiedForDoubles()
* - isAtLeastOnePlayerSpecifiedForSingles()
* - isAtLeastOnePlayerDuplicatedForDoubles()
* - isAtLeastOnePlayerDuplicatedForSingles()
* - isGuestPlayer()
* - getEmailAddressesForReservation()
* - getBuddyEmailAddresses()
* - printPlayer()
* - printTeam()
* - getSinglesReservationUser()
* - isFrontDeskGuestDoublesReservation()
* - isGuestDoublesReservation()
* - isDoublesSpotAvailable()
* - isDoublesReservationHaveThreeOrMore()
* - isDoublesReservationFull()
* - isCourtAlreadyReserved()
* - isSinglesReservationNeedPlayers()
* - isDoublesReservationNeedPlayers()
* - isCourtEventParticipant()
* - addToCourtEvent()
* - removeFromCourtEvent()
* - getCourtEventParticipants()
* - confirmCourtEvent()
* - isReservationLocked()
* Classes list:
*/
/**
 * Validates a doubles reservation.  Checks that of the four players entered that one is an actual player.  The way that the form may
 * be populated an updated spot may be represented by a zero, so just check that too.
 */
function isAtLeastOnePlayerSpecifiedForDoubles($playerOne, $playerTwo, $playerThree, $playerFour) {
    
    if (isDebugEnabled(1)) logMessage("reservationlib.isAtLeastOnePlayerSpecifiedForDoubles. values $playerOne, $playerTwo, $playerThree, $playerFour");
    
    if ($playerOne != "" && $playerOne != "0") {
        return true;
    } else 
    if ($playerTwo != "" && $playerTwo != "0") {
        return true;
    } else 
    if ($playerThree != "" && $playerThree != "0") {
        return true;
    } else 
    if ($playerFour != "" && $playerFour != "0") {
        return true;
    }
    return false;
}
/**
 * Validates a doubles reservation.  Checks that of the four players entered that two is an actual player
 */
function isAtLeastTwoPlayerSpecifiedForDoubles($playerOne, $playerTwo, $playerThree, $playerFour) {
    $count = 0;
    
    if (isDebugEnabled(1)) logMessage("reservationlib.isAtLeastTwoPlayerSpecifiedForDoubles()");
    
    if ($playerOne != "") {
        
        if (isDebugEnabled(1)) logMessage("\tplayerOne $playerOne");
        ++$count;
    }
    
    if ($playerTwo != "") {
        
        if (isDebugEnabled(1)) logMessage("\tplayerTwo $playerTwo");
        ++$count;
    }
    
    if ($playerThree != "") {
        
        if (isDebugEnabled(1)) logMessage("\tplayerThree $playerThree");
        ++$count;
    }
    
    if ($playerFour != "") {
        
        if (isDebugEnabled(1)) logMessage("\tplayerFour $playerFour");
        ++$count;
    }
    
    if (isDebugEnabled(1)) logMessage("\tNumber of players found: $count");
    
    if ($count > 1) {
        return true;
    } else {
        return false;
    }
}
/**
 * Validates a singles reservation.  Checks that of the two players entered that one is an actual player
 */
function isAtLeastOnePlayerSpecifiedForSingles($playerOne, $playerTwo) {
    
    if (isDebugEnabled(1)) logMessage("reservationlib.isAtLeastOnePlayerSpecifiedForSingles: playerOne: $playerOne, playerTwo: $playerTwo");
    
    if ($playerOne != "") {
        return true;
    } else 
    if ($playerTwo != "") {
        return true;
    }
    return false;
}
/**
 * Validates a doubles reservation.  Only club guest are allowed to be in the reservation more than once.
 */
function isAtLeastOnePlayerDuplicatedForDoubles($playerOne, $playerTwo, $playerThree, $playerFour) {
    $playerOneTrimmed = rtrim($playerOne);
    $playerTwoTrimmed = rtrim($playerTwo);
    $playerThreeTrimmed = rtrim($playerThree);
    $playerFourTrimmed = rtrim($playerFour);
    
    if (isDebugEnabled(1)) logMessage("reservationlib.isAtLeastOnePlayerDuplicatedForDoubles: playerOne: $playerOneTrimmed, playerTwo: $playerTwoTrimmed, playerThree: $playerThreeTrimmed, playerFour: $playerFourTrimmed");

    //Check Player One
    
    if (!isClubGuest($playerOneTrimmed) && isPlayerSpecified($playerOneTrimmed) && ($playerOneTrimmed == $playerTwoTrimmed || $playerOneTrimmed == $playerThreeTrimmed || $playerOneTrimmed == $playerFourTrimmed)) {
        return true;
    }

    //Check Player Two
    else 
    if (!isClubGuest($playerTwoTrimmed) && isPlayerSpecified($playerTwoTrimmed) && ($playerTwo == $playerOneTrimmed || $playerTwoTrimmed == $playerThreeTrimmed || $playerTwoTrimmed == $playerFourTrimmed)) {
        return true;
    }

    //Check Player Three
    else 
    if (!isClubGuest($playerThreeTrimmed) && isPlayerSpecified($playerThreeTrimmed) && ($playerThree == $playerOneTrimmed || $playerThreeTrimmed == $playerTwoTrimmed || $playerThreeTrimmed == $playerFourTrimmed)) {
        return true;
    }

    //Check Player Four
    else 
    if (!isClubGuest($playerFourTrimmed) && isPlayerSpecified($playerFourTrimmed) && ($playerFourTrimmed == $playerOneTrimmed || $playerFourTrimmed == $playerTwoTrimmed || $playerFourTrimmed == $playerThreeTrimmed)) {
        return true;
    }
    return false;
}
/**
 * Validates a doubles reservation.  Only club guest are allowed to be in the reservation more than once.
 */
function isAtLeastOnePlayerDuplicatedForSingles($playerOne, $playerTwo) {

    //Check Player One
    
    if (!isClubGuest($playerOne) && isPlayerSpecified($playerOne) && ($playerOne == $playerTwo)) {
        return true;
    }
    return false;
}
/**
 * A Guest Player is one where the name is specified, but not the id (the person simply types the name in but doesn't
 * select them from the dropdown.)
 */
function isGuestPlayer($id, $name) {
    
    if (isDebugEnabled(1)) logMessage("reservatiolib.isGuestPlayer: $id, $name");
    
    if (!empty($name) && (empty($id) || $id == 0)) {
        
        if (isDebugEnabled(1)) logMessage("This is a Guest Reservation");
        return true;
    }
    return false;
}

/************************************************************************************************************************/

/*
     This function returns the email addresses of all players in the reservation 
     (except the current user)
*/
function getEmailAddressesForReservation($eservationId) {
    $emailAddresses = array();

    //Gather up email addresses for any singles players in the reservation.
    //This will include players not in a team in a doubles reservation

    $singlesQuery = "SELECT users.email 
					 FROM  tblReservations reservations, tblkpUserReservations reservationentry, tblUsers users 
					 WHERE reservations.reservationid = $eservationId
					 AND reservationentry.userid = users.userid
					 AND reservations.reservationid = reservationentry.reservationid
					 AND reservationentry.usertype = 0
					 AND users.email != ''
				     AND reservationentry.userid <> " . get_userid() . "
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
					AND users.userid <> " . get_userid() . "";
    $doublesResult = db_query($dobulesQuery);
    while ($user = mysql_fetch_row($doublesResult)) {
        array_push($emailAddresses, $user[0]);
    }
    return $emailAddresses;
}
/**
 * Returns a comma delimited list of email addresses
 */
function getBuddyEmailAddresses($userId) {
    
    if (isDebugEnabled(1)) logMessage("reservationlib.getBuddyEmailAddresses: Starting for userid:" . $userId);
    $emailAddresses = array();

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
    
    if (isDebugEnabled(1)) logMessage("reservationlib.getBuddyEmailAddresses: all done for userid:" . $userId);
    return $emailAddresses;
}
/**
 * With the userid, print out the users first and last name
 */
function printPlayer($firstname, $lastname, $userId, $creator) {
    
     if( is_logged_in() || isShowPlayerNames()){ 
        echo "$firstname $lastname";

        if ($creator == $userId) {
            echo "*";
        }
        echo "<br/>";
     }
    
    
}
/**
 * Prints the two players first and last names
 */
function printTeam($teamId, $creator) {
    

    if( is_logged_in() || isShowPlayerNames()){ 

            $teamnamequery = "SELECT users.firstname, users.lastname, users.userid
        					      FROM tblUsers users,  tblkpTeams teams
        					   WHERE users.userid = teams.userid
        					   AND teams.teamid=$teamId";
            $teamnameresult = db_query($teamnamequery);
            $teamnamearray = db_fetch_array($teamnameresult);
            echo "$teamnamearray[0] $teamnamearray[1]";
            
            if ($creator == $teamnamearray[2]) {
                echo "*";
            }
            echo "<br/>";
            $teamnamearray = db_fetch_array($teamnameresult);
            echo "$teamnamearray[0] $teamnamearray[1]";
            
            if ($creator == $teamnamearray[2]) {
                echo "*";
            }
            echo "<br/>";

      }
}
/**
 * Gets pretty much everything needed for the court reservation screen for a user.
 */
function getSinglesReservationUser($reservationid) {
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
				   AND clubuser.clubid = " . get_clubid() . "
                   ORDER BY reservationdetails.id";
    return db_query($useridquery);
}

//Determines if this is a guest doubles reservation
function isFrontDeskGuestDoublesReservation($id1, $name1, $id2, $name2, $id3, $name3, $id4, $name4) {
    
    if (isDebugEnabled(1)) logMessage("reservationlib.isFrontDeskGuestDoublesReservation: $id1, $name1, $id2, $name2, $id3, $name3, $id4, $name4");
    
    if (empty($id1) && !empty($name1) || empty($id2) && !empty($name2) || empty($id3) && !empty($name3) || empty($id4) && !empty($name4)) {
        return true;
    } else {
        return false;
    }
}

//Determines if this is a guest doubles reservation
function isGuestDoublesReservation($id1, $name1, $id2, $name2, $id3, $name3) {
    
    if (isDebugEnabled(1)) logMessage("reservationlib.isGuestDoublesReservation: $id1, $name1, $id2, $name2, $id3, $name3");
    
    if (empty($id1) && !empty($name1) || empty($id2) && !empty($name2) || empty($id3) && !empty($name3)) {
        return true;
    } else {
        return false;
    }
}
/**
 * Checks to see if any of the names are empty
 */
function isDoublesSpotAvailable($name1, $name2, $name3, $name4) {
    
    if (isDebugEnabled(1)) logMessage("reservationlib.isDoublesSpotAvailable:  $name1, $name2, $name3, $name4");
    
    if (empty($name1) || empty($name2) || empty($name3) || empty($name4)) {
        return true;
    } else {
        return false;
    }
}

/*
 * This just sees if one of these is empty
*/
function isDoublesReservationHaveThreeOrMore($playeroneid, $playertwoid, $playerthreeid, $playerfourid) {
    $counter = 0;
    
    if (!empty($playeroneid)) ++$counter;
    
    if (!empty($playertwoid)) ++$counter;
    
    if (!empty($playerthreeid)) ++$counter;
    
    if (!empty($playerfourid)) ++$counter;
    
    if ($counter < 3) return false;
    else return true;
}

/*
 * Determines if one of these is NOT set (meaning that its not a full reservation)
*/
function isDoublesReservationFull($playeroneid, $playertwoid, $playerthreeid, $playerfourid) {
    $counter = 0;
    
    if (!empty($playeroneid)) ++$counter;
    
    if (!empty($playertwoid)) ++$counter;
    
    if (!empty($playerthreeid)) ++$counter;
    
    if (!empty($playerfourid)) ++$counter;
    
    if ($counter < 4) return false;
    else return true;
}

/*
 * Does a real simple check to see if the court is already reserved.
*/
function isCourtAlreadyReserved($courtid, $time) {
    $notReservedQuery = "SELECT reservations.reservationid FROM tblReservations reservations 
							WHERE reservations.courtid = $courtid 
							AND reservations.time = $time
							AND reservations.enddate IS NULL";
    $notReservedResult = db_query($notReservedQuery);
    $numberOfReservations = mysqli_num_rows($notReservedResult);
    
    if ($numberOfReservations < 1) {
        return false;
    } else {
        return true;
    }
}
/*
 Checks to see if there are any straglers in a singles reservation
 */
function isSinglesReservationNeedPlayers($time, $courtid) {
    
    if (isDebugEnabled(1)) logMessage("court_reservation.isSinglesReservationFull: Checking to see if player looking for a match has already found partner");

    //Check for singles
    $query = "SELECT tblkpUserReservations.userid, tblkpUserReservations.usertype
                     FROM tblReservations INNER JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
                     WHERE tblReservations.courtid=$courtid
             AND tblReservations.time=$time
			 AND tblReservations.enddate IS NULL
			 AND tblkpUserReservations.userid = 0
			 ORDER BY tblkpUserReservations.usertype, tblkpUserReservations.userid";
    $result = db_query($query);
    
    if (mysqli_num_rows($result) > 0) {
        
        if (isDebugEnabled(1)) logMessage("court_reservation.isSinglesReservationFull: Yep, there are some openings for singles");
        return TRUE;
    } else {
        return FALSE;
    }
}
/**
 Checks to see if there are any straglers in a doubles reservation
 */
function isDoublesReservationNeedPlayers($time, $courtid) {
    
    if (isDebugEnabled(1)) logMessage("court_reservation.isDoublesReservationFull: Checking to see if player looking for a match has already found partner");

    //Check for doubles
    $query = "SELECT tblkpUserReservations.userid
                     FROM tblkpUserReservations, tblReservations
                     WHERE  tblReservations.reservationid = tblkpUserReservations.reservationid
                     AND tblReservations.courtid=$courtid
             AND tblReservations.time=$time
			 AND tblReservations.enddate IS NULL
			 AND tblReservations.usertype = 1
			 AND tblkpUserReservations.usertype = 1
			 ORDER BY tblkpUserReservations.usertype, tblkpUserReservations.userid ";
    $result = db_query($query);

    // if nobody is looking, display an error (sounds shady)
    
    if (mysqli_num_rows($result) < 2) {
        
        if (isDebugEnabled(1)) logMessage("court_reservation.isDoublesReservationFull: No, there are some openings for doubles");
        return TRUE;
    } else {
        return FALSE;
    }
}
/**
 *
 * @param $clubEventParticipantsResult
 */
function isCourtEventParticipant(&$courtEventParticipantsResult) {
    $isSignedup = false;
    logMessage("reservationlib.isCourtEventParticipant: Checking to see if " . get_userid() . " is signed up");
    $numrows = mysqli_num_rows($courtEventParticipantsResult);
    while ($participant = mysqli_fetch_array($courtEventParticipantsResult)) {
        
        if ($participant['userid'] == get_userid()) {
            $isSignedup = true;
        }
    }

    // Reset the results
    
    if (mysqli_num_rows($courtEventParticipantsResult) > 0) {
        mysql_data_seek($courtEventParticipantsResult, 0);
    }
    return $isSignedup;
}
/**
 *
 * @param unknown_type $userid
 * @param unknown_type $clubeventid
 */
function addToCourtEvent($userid, $reservationid) {
    logMessage("clubadminlib.addToCourtEvent: User $userid Reservationid: $reservationid");
    $check = "SELECT count(*) FROM tblCourtEventParticipants participants 
					WHERE participants.userid = $userid 
					AND participants.reservationid = $reservationid
					AND participants.enddate IS NULL";
    $checkResult = db_query($check);
    $num = mysql_result($checkResult, 0);
    
    if ($num == 0) {
        $query = "INSERT INTO tblCourtEventParticipants (
                userid, reservationid
                ) VALUES (
                          '$userid'
					  	  ,'$reservationid'
                          )";
        $result = db_query($query);
    } else {
        logMessage("reservationlib.addToCourtEvent: User $userid is already in reservation $reservationid. not doing anything.");
    }
}
/**
 *
 * @param unknown_type $userid
 * @param unknown_type $clubeventid
 */
function removeFromCourtEvent($userid, $reservationid) {
    logMessage("reservationlib.removeFromCourtEvent: User $userid reservationid: $reservationid");
    $query = "UPDATE tblCourtEventParticipants SET enddate=NOW() 
				WHERE userid='$userid'
				AND reservationid = '$reservationid'";
    $result = db_query($query);
}
/**
 *
 * @param unknown_type $clubeventid
 */
function getCourtEventParticipants($reservationid) {
    logMessage("reservationlib.getCourtEventParticipants: Reservationid: $reservationid");
    $query = "SELECT users.userid, users.firstname, users.lastname, users.email
			   FROM tblCourtEventParticipants participant, tblUsers users
				WHERE users.userid = participant.userid
				AND participant.enddate IS NULL
				AND participant.reservationid = $reservationid";
    return db_query($query);
}

//Send out an email to the player who signed up for this event, the creator of the event and the list
// and anyone that might have been signed up as well

// when admin action is set to true, an administrator is adding or removing players. in this case, they don't need to be notified

// because they are doing it.

function confirmCourtEvent($userid, $reservationid, $action, $adminaction) {

    //remove white space
    $userid = rtrim($userid);

    //Set variables
    $from_email = "Sportsynergy <player.mailer@sportsynergy.net>";
    $template = get_sitecode();
    $adminaction_var = $adminaction ? "true" : "false";
    
    if (isDebugEnabled(1)) logMessage("reservationlib.confirmCourtEvent: confirming reservation $reservationid for userid $userid with an action $action and adminaction is $adminaction_var");

    // from the reservation get, court name, time, event name
    //Obtain the court and matchtype information

    $timeQuery = "SELECT courts.courtname, reservations.time, events.eventname, reservations.creator
				FROM tblMatchType matchtype, tblCourts courts, tblReservations reservations, tblEvents events
				WHERE reservations.reservationid=$reservationid
				AND reservations.courtid = courts.courtid
				AND matchtype.id = reservations.matchtype
				AND events.eventid = reservations.eventid";
    $timeResult = db_query($timeQuery);
    $timeObject = mysql_fetch_object($timeResult);
    $var = new Object;
    $var->courtname = $timeObject->courtname;
    $var->time = gmdate("l F j g:i a", $timeObject->time);
    $var->eventname = $timeObject->eventname;
    $var->creator = $timeObject->creator;
    $userQuery = "SELECT users.firstname, users.lastname, users.email FROM tblUsers users WHERE users.userid = $userid";
    $userResult = db_query($userQuery);
    $userArray = mysqli_fetch_array($userResult);
    $var->firstname = $userArray['firstname'];
    $var->lastname = $userArray['lastname'];
    $var->fullname = "$var->firstname $var->lastname";
    $var->email = $userArray['email'];
    
    if ($action == "add") {
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/confirm_court_event.php", $var);
        $subject = get_clubname() . " - Court Event Reservation Notice";
    } else {
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/cancel_court_event.php", $var);
        $subject = get_clubname() . " - Court Event Cancellation Notice";
    }

    //send email to the person who signed up
    
    if (isDebugEnabled(1)) logMessage($emailbody);
    $to_email = array(
        $var->email => array(
            'name' => $var->firstname
        )
    );
    $content = new Object;
    $content->line1 = $emailbody;
    $content->clubname = get_clubname();
    sendgrid_email($subject, $to_email,  $content, $template);

    //send email to the person who created the reservation
    $creatorQuery = "SELECT users.firstname, users.lastname, users.email FROM tblUsers users WHERE users.userid = $var->creator";
    $creatorResult = db_query($creatorQuery);
    $creatorArray = mysqli_fetch_array($creatorResult);
    $var->adminfirstname = $creatorArray['firstname'];
    $var->adminlastname = $creatorArray['lastname'];
    $var->adminfullname = "$var->adminfirstname $var->adminlastname";
    $var->adminemail = $creatorArray['email'];
    
    if ($action == "add") {
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/confirm_others_court_event.php", $var);
        $subject = get_clubname() . " - Court Event Reservation Notice";
    } else {
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/cancel_others_court_event.php", $var);
        $subject = get_clubname() . " - Court Event Cancellation Notice";
    }

    //Set Email Variables
    $to_email = array(
        $var->adminemail => array(
            'name' => $var->adminfirstname
        )
    );
    $content = new Object;
    $content->line1 = $emailbody;
    $content->clubname = get_clubname();

    //Dont' send this email if the creator has put themselves in the reservaiton, its kind of redundatn
    
    if ($userid != $var->creator && !$adminaction) {
        
        if (isDebugEnabled(1)) logMessage("reservationlib.confirmCourtEvent: sending the  message to the guy who created this.");
        
        if (isDebugEnabled(1)) logMessage($emailbody);
        sendgrid_email($subject, $to_email,  $content, "Confirm Court Event");
    }
    $to_emails = array();
    $rresult = getCourtEventParticipants($reservationid);
    while ($player = mysqli_fetch_array($rresult)) {
        
        if (!empty($player['email']) && $player['userid'] != $userid) {
            
            if (isDebugEnabled(1)) logMessage("reservationlib.confirmCourtEvent: sending notice to fellow court event participant:" . $player['email']);
            $to_emails[$player['email'] = array(
                'name' => $player['firstname']
            ) ];
        }
    }

    //Configure email content
    $content = new Object;
    $content->line1 = $emailbody;
    $content->clubname = get_clubname();
    sendgrid_email($subject, $to_emails, $content, "Confirm Court Event");
}
/**
 * Determines if the reservation is locked or not
 */
function isReservationLocked($time, $courtid) {
    $query = "SELECT locked FROM tblReservations WHERE courtid = $courtid AND time = $time AND enddate IS NULL";
    $result = db_query($query);
    return mysql_result($result, 0) == "y" ? true : false;
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
function isReoccuringReservation($time, $courtid) {

    //Get all of the reoccuring event (1)
    $reOccuringEventQuery = "SELECT reoccuringevents.eventinterval,reoccuringevents.starttime,reoccuringevents.endtime 
                                FROM tblReoccuringEvents reoccuringevents
                                WHERE reoccuringevents.courtid = $courtid
                                AND reoccuringevents.endtime > $time";
    $reOccuringEventResult = db_query($reOccuringEventQuery);
    while ($reOccuringEventsArray = mysqli_fetch_array($reOccuringEventResult)) {
        $reoccuringEventsArray = array();

        //Calculate the timestamps for each event in the set (still 1)
        for ($i = $reOccuringEventsArray['starttime']; $i < $reOccuringEventsArray['endtime']; $i = $i + $reOccuringEventsArray['eventinterval']) {
            array_push($reoccuringEventsArray, $i);
        }
        
        if (in_array($time, $reoccuringEventsArray)) {
            return true;
        }
    }
    return false;
}

?>

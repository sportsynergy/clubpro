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
* - isClubEventParticipant()
* - addToClubEvent()
* - removeFromClubEvent()
* - loadClubEvent()
* Classes list:
*/
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/
/**
 *
 * @param $clubEventParticipants
 */
function isClubEventParticipant(&$clubEventParticipantsResult) {
    $isSignedup = false;
    logMessage("clubadminlib.isClubEventParticipant: Checking to see if " . get_userid() . " is signed up");
    $numrows = mysqli_num_rows($clubEventParticipantsResult);
    while ($participant = mysqli_fetch_array($clubEventParticipantsResult)) {
        
        if ($participant['userid'] == get_userid()) {
            $isSignedup = true;
        }
    }

    // Reset the results
    
    if (mysqli_num_rows($clubEventParticipantsResult) > 0) {
        mysqli_data_seek($clubEventParticipantsResult, 0);
    }
    return $isSignedup;
}
/**
 *
 * @param unknown_type $userid
 * @param unknown_type $clubeventid
 */
function addToClubEvent($userid, $clubeventid) {
    logMessage("clubadminlib.addToClubEvent: User $userid ClubEventId: $clubeventid");
    $check = "SELECT count(*) FROM tblClubEventParticipants participants 
					WHERE participants.userid = $userid 
					AND participants.clubeventid = $clubeventid
					AND participants.enddate IS NULL";
    $checkResult = db_query($check);
    

    $numArray = mysqli_fetch_array($checkResult);
    $num = $numArray[0];

    if ($num == 0) {

        $query = "INSERT INTO tblClubEventParticipants (
                userid, clubeventid, guests, extra,comments
                ) VALUES (
                          '$userid'
					  	  ,'$clubeventid'
                          ,'','',''
                          )";

                          logMessage($query);
        $result = db_query($query);

    } else {
        logMessage("clubadminlib.addToClubEvent: User $userid is already in  $clubeventid not doing anything.");
    }
}
/**
 *
 * @param unknown_type $userid
 * @param unknown_type $clubeventid
 * @return void
 */
function removeFromClubEvent($userid, $clubeventid) {
    logMessage("clubadminlib.removeFromClubEvent: User $userid ClubEventId: $clubeventid");
    $query = "UPDATE tblClubEventParticipants SET enddate=NOW() 
				WHERE userid='$userid'
				AND clubeventid = '$clubeventid'";
    $result = db_query($query);
}
/**
 *
 * @param $eventid
 * @return Resource
 */
function loadClubEvent($eventid) {
    logMessage("clubadminlib.loadClubEvent: Eventid $eventid");
    $query = "SELECT events.id, events.name, events.eventdate, events.description
			   FROM tblClubEvents events 
				WHERE events.id = '$eventid' 
				AND enddate is NULL";
    return db_query($query);
}
?>
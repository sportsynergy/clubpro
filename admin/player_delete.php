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
* - delete_player()
* Classes list:
*/
include ("../application.php");
$DOC_TITLE = "Delete Player";
require_loginwq();
require_priv("2");

//Set the http variables
$searchname = $_REQUEST["searchname"];
$userid = $_REQUEST["userid"];

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
    $delplayer = delete_player($frm);
    include ($_SESSION["CFG"]["includedir"] . "/include_userdelsuc.php");
    include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
    die;
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_delete_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
/**
 *
 * @param unknown_type $frm
 */
function delete_player(&$frm) {
    
    if (isDebugEnabled(1)) logMessage("player_delete.delete_player: Deleting userid " . $frm[userid]);

    //If this player belongs to more than one club, be sure to only remove
    // the club authorization not the entire user

    $clubUserQuery = "SELECT clubuser.clubid FROM tblClubUser clubuser WHERE clubuser.userid = '$frm[userid]'";
    $clubUserResult = db_query($clubUserQuery);
    
    if (isDebugEnabled(2)) logMessage("player_delete.delete_player: Deleting user: '$frm[userid]' from " . get_clubname() . " by " . get_userfullname());
    $qid1 = db_query("UPDATE tblUsers SET enddate = NOW() WHERE userid = '$frm[userid]'");
    
    if (isDebugEnabled(2)) logMessage("player_delete.delete_player: Deleting club user: '$frm[userid]' from " . get_clubname() . " by " . get_userfullname());
    $qid1 = db_query("UPDATE tblClubUser SET enddate = NOW() WHERE userid = '$frm[userid]' AND clubid = " . get_clubid());

    // Now we need to get rid of all of the teams they were on
    $teamidquery = "SELECT teamid
                       FROM tblkpTeams
                       WHERE userid='$frm[userid]'";

    // run the query on the database
    $teamidresult = db_query($teamidquery);
    while ($teamidrow = db_fetch_row($teamidresult)) {

        //Get rid of the teams in the first teams table
        $qid1 = db_query("UPDATE tblTeams SET enable = 0 WHERE teamid = $teamidrow[0]");

        //Get rid of the teams in the second teams table
        $qid1 = db_query("UPDATE tblkpTeams SET enable = 0 WHERE teamid = $teamidrow[0]");

        //Get all reservations where the persons team was playing
        $teamresidquery = "SELECT reservations.reservationid
                              FROM tblReservations reservations, tblkpUserReservations reservationdetails, tblCourts courts
							  WHERE reservations.reservationid = reservationdetails.reservationid
							  AND reservations.courtid = courts.courtid
                              AND courts.clubid = " . get_clubid() . "
                              AND reservations.usertype=1
							  AND reservationdetails.usertype=1
                              AND reservationdetails.userid=$teamidrow[0]";

        // run the query on the database
        $teamresidresult = db_query($teamresidquery);
        while ($teamresidrow = db_fetch_row($teamresidresult)) {
            $qid1 = db_query("UPDATE tblReservations 
							SET lastmodifier = " . get_userid() . ", enddate = NOW() 
							WHERE reservationid = $teamresidrow[0]");
            
            if (isDebugEnabled(2)) logMessage("-> End dating doubles reservation: $teamresidrow[0]");
        }
    }

    // Finally get rid of their reservations and scores.  We first get all reservationid
    // where this user was playing.  This will delete any doubles reservations that include

    // the player as a single person looking for a partner.

    $userresidquery = "SELECT reservations.reservationid
                          FROM tblReservations reservations, tblkpUserReservations reservationdetails, tblCourts courts
                          WHERE reservations.reservationid = reservationdetails.reservationid
                          AND reservations.courtid = courts.courtid
                          AND courts.clubid = " . get_clubid() . "
						  AND reservationdetails.userid='$frm[userid]'
                          AND reservationdetails.usertype=0";

    // For xome reason the userid is being (re)set to 0 which ends up enddating all
    //reservations where someone was looking for a match.  Do a quick check here to see

    //if this is the case if it is, just exit

    
    if ($frm['userid'] == 0) {
        
        if (isDebugEnabled(2)) logMessage("-> Exiting because userid = 0: Here is the query that was doing to jack everything all up: $userresidquery");
        return;
    }

    // run the query on the database
    $userresidresult = db_query($userresidquery);

    /* Get rid of all singles reservations.  The reason why this is done
     * like this is it is presumed that if a player is being deleted, they will not
     * be in any current reservations, this is a little harse I do admit.
    */
    while ($userresidrow = db_fetch_row($userresidresult)) {
        $qid1 = db_query("UPDATE tblReservations 
							SET lastmodifier = " . get_userid() . ", enddate = NOW() 
							WHERE reservationid = $userresidrow[0]");
        
        if (isDebugEnabled(2)) logMessage("-> End dating singles reservation: $userresidrow[0]");
    }

    //Remove them from any club ladder
    $userid = $frm['userid'];
    $clubid = get_clubid();
    $query = "SELECT ladder.* FROM tblClubLadder ladder WHERE userid = $userid AND enddate IS NULL";
    $result = db_query($query);

    //For each ladder they were in, remove them and move everyone up.
    while ($array = db_fetch_array($result)) {
        $courttypeid = $array['courttypeid'];
        $clubid = $array['clubid'];
        $position = $array['position'];

        //end date the player
        $enddatequery = "UPDATE tblClubLadder 
					SET enddate = NOW() 
					WHERE userid = $userid 
					AND  courttypeid = $courttypeid 
					AND clubid = $clubid";
        db_query($enddatequery);

        //Move everybody else up
        moveEveryOneInClubLadderUp($courttypeid, $clubid, $position + 1);
        
        if (isDebugEnabled(2)) logMessage("player_delete: removing user $userid from club ladder for club $clubid for courttypeid $courttypeid");
    }
    return 1;
}
?>
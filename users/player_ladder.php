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
* - createChallengematch()
* - getLadder()
* - isPlayingInLadder()
* - sendEmailsForLadderMatch()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
require "../vendor/autoload.php";
$DOC_TITLE = "Player Ladder";
require_loginwq();


// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

if (!empty($_POST['ladderid'])) {
    $_SESSION["ladder_id"] = $_POST['ladderid'];
}
$ladderid = $_SESSION["ladder_id"];

/* form has been submitted */
if (isset($_POST['submit']) || isset($_POST['cmd'])) {
    $frm = $_POST;
    $userid = $frm['userid'];
    $clubid = get_clubid();

    if ($frm['cmd'] == 'removematch') {

        $laddermatchid = $frm['laddermatchid'];
        if (isDebugEnabled(1)) logMessage("player_ladder: removing this ladder match $laddermatchid");
        $query = "UPDATE tblLadderMatch SET enddate = CURRENT_TIMESTAMP WHERE id = $laddermatchid";
        $result = db_query($query);
        
    }

    // Add User to Ladder
    if ($frm['cmd'] == 'addtoladder') {
        
        //Check to see if player is already in ladder
        $check = "SELECT count(*) from tblClubLadder 
        				WHERE userid = $userid 
        				AND ladderid = $ladderid 
        				AND enddate IS NULL";
        $checkResult = db_query($check);

        if (isDebugEnabled(2)) logMessage("player_ladder: addtoladder $check");

        $exists = mysqli_result($checkResult, 0);
        
        if ($exists == 0) {
            $position = $frm['placement'];
            moveEveryOneInClubLadderDown($ladderid, $position);
            
            if (isDebugEnabled(2)) logMessage("player_rankings: adding user $userid to club ladder for club $clubid for ladder $ladderid in position $position");
            $query = "INSERT INTO tblClubLadder (
		                userid, ladderid, ladderposition
		                ) VALUES (
		                          $userid
                                  ,$ladderid
		                          ,$position)";
            db_query($query);
        } else {
            
            if (isDebugEnabled(2)) logMessage("player_ladder: user $userid is already playing in this ladder with an id $ladderid ");
        }
    } else 
    if ($frm['cmd'] == 'moveupinladder') {
        
        if (isDebugEnabled(1)) logMessage("player_ladder: moving user $userid up in ladder $ladderid ");
        moveUpOneInClubLadder($ladderid, $clubid, $userid);
    } 

    else if ($frm['cmd'] == 'removefromladder') {

        //get current position
        $query = "SELECT ladderposition from tblClubLadder where ladderid = $ladderid AND userid = $userid AND enddate IS NULL";
        $result = db_query($query);
        $position = mysqli_result($result, 0);
        
        if (isDebugEnabled(1)) logMessage("player_ladder: removing user $userid to club ladder for ladderid $ladderid");
        $query = "UPDATE tblClubLadder SET enddate = NOW() WHERE userid = $userid AND  ladderid = $ladderid";
        db_query($query);

        //Move everybody else up
        moveEveryOneInClubLadderUp($ladderid, $position + 1);
    } 

    // Challenge Player
    else if ($frm['cmd'] == 'challengeplayer') {
        $challengeeid = $frm['challengeeid'];
        $challengerid = get_userid();
        $message = $frm['textarea'];
        
        if (isDebugEnabled(2)) logMessage("player_ladder: challengeplayer $challengerid has challenged $challengeeid");

        //Create the challenge match
        createChallengematch($challengerid, $challengeeid, $courttypeid);

        //lock the two players
        lockLadderPlayers($challengerid, $challengeeid, $ladderid);

        //send the email
        sendEmailsForLadderMatch($challengerid, $challengeeid, $message);
    
    } else if ($frm['cmd'] == 'removechallenge') {
        $challengematchid = $frm['challengematchid'];
        $challengerid = $frm['challengerid'];
        $challengeeid = $frm['challengeeid'];
        
        if (isDebugEnabled(2)) logMessage("player_ladder: removing challenge match $challengematchid");

        //enddate the challenge match
        $query = "UPDATE tblChallengeMatch SET enddate = NOW() WHERE id = $challengematchid";
        db_query($query);

        //unlock the players
        unlockLadderPlayers($challengerid, $challengeeid, $courttypeid);

        //send emails
        //TODO create emails for removing challenge ladder

    } else if ($frm['cmd'] == 'reportladderscore') {

        $hourplayed = $frm['hourplayed'];
        $score = $frm['score'];
        $minuteofday = $frm['minuteofday'];
        $timeofday = $frm['timeofday'];

        if (isDebugEnabled(1)) logMessage("player_ladder: Reporting a ladder score: hourplayed: $hourplayed, score: $score, minuteofday: $minuteofday, timeofday: $timeofday");

    
        $score = $frm['score'];
        $winnerid = $frm['rsuserid'];
        $loserid = $frm['rsuserid2'];

        // Set the match time
        if ( $timeofday == "PM"){
            $hourplayed = $hourplayed + 12;
        }
        $curtime = $_SESSION["current_time"];
        $currYear = gmdate("Y", $curtime);
        $currMonth = gmdate("n", $curtime);
        $currDay = gmdate("j", $curtime);
        $hourplayed = str_pad($hourplayed, 2, "0", STR_PAD_LEFT);
        $matchtime = "$currYear-$currMonth-$currDay $hourplayed:$minuteofday:00";

        $query = "INSERT INTO tblLadderMatch (
            ladderid, score, winnerid, loserid, match_time
            ) VALUES (
                      $ladderid
                      ,'$score'
                      ,$winnerid
                      ,$loserid
                      ,'$matchtime'
                      )";
        
        db_query($query);

    }
}

// Initialize view with data
$availbleSports = load_avail_sports();
$ladderplayers = getLadder($ladderid);
$ladderdetails = getLadderDetails($ladderid);

$playingInLadder = isPlayingInLadder(get_userid() , $ladderid);
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_ladder_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
/**
 * Puts an entry in the challenge match table
 *
 * @param $challengerid
 * @param $challengeeid
 * @param $courttypeid
 */
function createChallengematch($challengerid, $challengeeid, $courttypeid) {
    
    if (isDebugEnabled(1)) logMessage("player_ladder.createChallengematch: creating a challenge match for challenger: $challengerid and challengee: $challengeeid with courttype $courttypeid");
    $query = "INSERT INTO tblChallengeMatch (
		                challengerid, challengeeid, courttypeid, siteid
		                ) VALUES (
		                          $challengerid
		                          ,$challengeeid
		                          ,$courttypeid
		                          ," . get_siteid() . ")";
    db_query($query);
}

?>
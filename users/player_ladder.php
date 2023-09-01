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

// Include jQuery
//define("_JQUERY_", true);



if (!empty($_POST['courttypeid'])) {
    $_SESSION["ladder_courttype"] = $_POST['courttypeid'];
}
$courttypeid = $_SESSION["ladder_courttype"];

/* form has been submitted */

if (isset($_POST['submit']) || isset($_POST['cmd'])) {
    $frm = $_POST;
    $userid = $frm['userid'];
    $clubid = get_clubid();

    // Add User to Ladder
    
    if ($frm['cmd'] == 'addtoladder') {
        
        if (isDebugEnabled(2)) logMessage("player_ladder: addtoladder");

        //Check to see if player is already in ladder
        $check = "SELECT count(*) from tblClubLadder 
        				WHERE userid = $userid 
        				AND clubid = $clubid 
        				AND courttypeid = $courttypeid 
        				AND enddate IS NULL";
        $checkResult = db_query($check);
        $exists = mysqli_result($checkResult, 0);
        
        if ($exists == 0) {
            $position = $frm['placement'];
            moveEveryOneInClubLadderDown($courttypeid, $clubid, $position);
            
            if (isDebugEnabled(2)) logMessage("player_rankings: adding user $userid to club ladder for club $clubid for courttypeid $courttypeid in position $position");
            $query = "INSERT INTO tblClubLadder (
		                userid, courttypeid, ladderposition, clubid
		                ) VALUES (
		                          $userid
		                          ,$courttypeid
		                          ,$position
		                          ,$clubid)";
            db_query($query);
        } else {
            
            if (isDebugEnabled(2)) logMessage("player_ladder: user $userid is already playing in this ladder with court typeid $courttypeid ");
        }
    } else 
    if ($frm['cmd'] == 'moveupinladder') {
        
        if (isDebugEnabled(1)) logMessage("player_ladder: moving user $userid up in ladder $courttypeid ");
        moveUpOneInClubLadder($courttypeid, $clubid, $userid);
    } else 
    if ($frm['cmd'] == 'removefromladder') {

        //get current position
        $query = "SELECT ladderposition from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND userid = $userid AND enddate IS NULL";
        $result = db_query($query);
        $position = mysqli_result($result, 0);
        
        if (isDebugEnabled(1)) logMessage("player_ladder: removing user $userid to club ladder for club $clubid for courttypeid $courttypeid");
        $query = "UPDATE tblClubLadder SET enddate = NOW() WHERE userid = $userid AND  courttypeid = $courttypeid AND clubid = $clubid";
        db_query($query);

        //Move everybody else up
        moveEveryOneInClubLadderUp($courttypeid, $clubid, $position + 1);
    } else 
    if ($frm['cmd'] == 'challengeplayer') {
        $challengeeid = $frm['challengeeid'];
        $challengerid = get_userid();
        $message = $frm['textarea'];
        
        if (isDebugEnabled(2)) logMessage("player_ladder: challengeplayer $challengerid has challenged $challengeeid");

        //Create the challenge match
        createChallengematch($challengerid, $challengeeid, $courttypeid);

        //lock the two players
        lockLadderPlayers($challengerid, $challengeeid, $courttypeid);

        //send the email
        sendEmailsForLadderMatch($challengerid, $challengeeid, $message);
    } else 
    if ($frm['cmd'] == 'removechallenge') {
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

        
    }
}

// Initialize view with data
$availbleSports = load_avail_sports();
$ladderplayers = getLadder($courttypeid);
$playingInLadder = isPlayingInLadder(get_userid() , $courttypeid);
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
/**
 * Gets the ladder for the given court type
 *
 * @param unknown_type $courttypeid
 */
function getLadder($courttypeid) {
    
    if (isDebugEnabled(1)) logMessage("player_ladder.getLadder: getting the players in the ladder for courttype $courttypeid");
    $rankquery = "SELECT 
						users.userid,
						ladder.ladderposition,
						ladder.going,
						users.firstname, 
						users.lastname,
						concat_ws(' ', users.firstname, users.lastname) as fullname,
						users.email,
						ladder.locked
                    FROM 
						tblUsers users, 
						tblClubLadder ladder
                    WHERE 
						users.userid = ladder.userid
                    AND ladder.clubid=" . get_clubid() . "
                    AND ladder.courttypeid=$courttypeid
					AND ladder.enddate IS NULL
                    ORDER BY ladder.ladderposition";
    return db_query($rankquery);
}
/**
 * True is user is, false if player isn't
 * @param $userid
 */
function isPlayingInLadder($userid, $courttypeid) {
    $query = "SELECT 1 FROM tblClubLadder WHERE userid = $userid AND courttypeid = $courttypeid AND clubid = " . get_clubid() . " AND enddate IS NULL";
    $result = db_query($query);
    $rows = mysqli_num_rows($result);
    
    if ($rows > 0) {
        return true;
    } else {
        return false;
    }
}
/**
 * Send off an email to the challenger and the challengee
 *
 * @param unknown_type $challengerid
 * @param unknown_type $challengeeid
 */
function sendEmailsForLadderMatch($challengerid, $challengeeid, $message) {
    
    if (isDebugEnabled(1)) logMessage("player_ladder.sendEmailsForLadderMatch: sending out emails to challenger $challengerid and challengee $challengeeid ");

    //Set some variables
    $subject = get_clubname() . " - Ladder Match Confirmation";

    /* load up the user record for the winner */
    $query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$challengerid'";
    $qid = db_query($query);
    $challenger = db_fetch_object($qid);

    /* load up the user record for the winner */
    $query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$challengeeid'";
    $qid = db_query($query);
    $challengee = db_fetch_object($qid);

    /* email the user with the new account information */
    $var = new clubpro_obj;
    $var->challenger_firstname = $challenger->firstname;
    $var->challenger_fullname = $challenger->firstname . " " . $challenger->lastname;
    $var->challengee_firstname = $challengee->firstname;
    $var->challengee_fullname = $challengee->firstname . " " . $challengee->lastname;
    $var->support = $_SESSION["CFG"]["support"];
    $template = get_sitecode();
    $challenger_emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/confirm_ladder_match_challenger.php", $var);
    $challenger_emailbody = nl2br($challenger_emailbody);

    // Provide Content for Challenger
    $challenger_email = array(
        $challenger->email => array(
            'name' => $challenger->firstname
        )
    );
    $content = new clubpro_obj;
    $content->line1 = $challenger_emailbody;
    $content->clubname = get_clubname();


    //Send the email
    send_email($subject, $challenger_email, $content, "Ladder Match");

    // Provide Content for Challengee
    $challengee_email = array(
        $challengee->email => array(
            'name' => $challengee->firstname
        )
    );
    $content = new clubpro_obj;
    $message = nl2br($message);
    $content->line1 = $message;
    $content->clubname = get_clubname();
    $from_email = "$var->challenger_fullname <$challenger->email>";
    $template = get_sitecode() . "-blank";
    $subject = get_clubname() . " - You've been challenged in a ladder match";

    //Send the email
    send_email($subject, $challengee_email, $content, "Ladder Match");
}
?>
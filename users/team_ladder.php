<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
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
* - getLadderTeam()
* - isPlayingInLadder()
* - confirmChallengerTeam()
* - confirmChallengeeTeam()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
require ($_SESSION["CFG"]["libdir"] . "/sendgrid-php/SendGrid_loader.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");


// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

$DOC_TITLE = "Team Ladder";
require_loginwq();

if (!empty($_POST['courttypeid'])) {
    $_SESSION["ladder_courttype"] = $_POST['courttypeid'];
}
$courttypeid = $_SESSION["ladder_courttype"];
$errormsg = "";

/* form has been submitted */

if (isset($_POST['submit']) || isset($_POST['cmd'])) {
    $frm = $_POST;
    $clubid = get_clubid();

    // Add User to Ladder
    
    if ($frm['cmd'] == 'addtoladder') {
        $userid = rtrim($frm['userid']);
        $userid2 = rtrim($frm['userid2']);
        
        if (isDebugEnabled(2)) logMessage("team_ladder: addtoladder with users $userid and $userid2 with courttypeid: $courttypeid");
        $playerOnePlaying = isPlayingInLadder($userid2, $courttypeid);
        $playerTwoPlaying = isPlayingInLadder($userid2, $courttypeid);
        $teamid = getTeamIDForPlayers($courttypeid, $userid, $userid2);
        
        if (isDebugEnabled(1)) logMessage("team_ladder: adding team: $teamid");

        //Check to see if player is already in ladder
        $check = "SELECT count(*) from tblClubLadder
        				WHERE userid = $teamid 
        				AND clubid = $clubid 
        				AND courttypeid = $courttypeid 
        				AND enddate IS NULL";
        $checkResult = db_query($check);
        $exists = mysql_result($checkResult, 0);
        
        if ($exists == 0 && !$playerOnePlaying && !$playerTwoPlaying) {
            $position = $frm['placement'];
            moveEveryOneInClubLadderDown($courttypeid, $clubid, $position);
            
            if (isDebugEnabled(2)) logMessage("team_ladder: adding user $teamid to club ladder for club $clubid for courttypeid $courttypeid in position $position");
            $query = "INSERT INTO tblClubLadder (
		                userid, courttypeid, ladderposition, clubid
		                ) VALUES ($teamid
		                          ,$courttypeid
		                          ,$position
		                          ,$clubid)";
            db_query($query);
        } else {
            
            if (isDebugEnabled(2)) logMessage("team_ladder: user $teamid is already playing in this ladder with court typeid $courttypeid ");
        }
        
        if ($playerOnePlaying || $playerTwoPlaying) {
            $errormsg = "A player can only play on one team. ";
            
            if (isDebugEnabled(1)) logMessage("team_ladder: one of the individual players selected is already playing in the ladder $courttypeid ");
        }
    } else 
    if ($frm['cmd'] == 'moveupinladder') {
        $userid = $frm['userid'];
        
        if (isDebugEnabled(1)) logMessage("team_ladder: moving user $userid up in ladder $courttypeid ");
        moveUpOneInClubLadder($courttypeid, $clubid, $userid);
    } else 
    if ($frm['cmd'] == 'removefromladder') {
        $userid = $frm['userid'];

        //get current position
        $query = "SELECT ladderposition from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND userid = $userid AND enddate IS NULL";
        $result = db_query($query);
        $position = mysql_result($result, 0);
        
        if (isDebugEnabled(1)) logMessage("player_ladder: removing user $userid to club ladder for club $clubid for courttypeid $courttypeid");
        $query = "UPDATE tblClubLadder SET enddate = NOW() WHERE userid = $userid AND  courttypeid = $courttypeid AND clubid = $clubid";
        db_query($query);

        //Move everybody else up
        moveEveryOneInClubLadderUp($courttypeid, $clubid, $position + 1);
    } else 
    if ($frm['cmd'] == 'challengeplayer') {
        $challengeeid = $frm['challengeeid'];
        $challengerid = $frm['challengerid'];
        $challengees = getFullnameForTeamPlayers($challengeeid);
        $challengers = getFullnameForTeamPlayers($challengerid);
        $message = $frm['textarea'];
        
        if (isDebugEnabled(2)) logMessage("player_ladder: challengeplayer $challengerid has challenged $challengeeid");

        //Create the challenge match
        createChallengematch($challengerid, $challengeeid, $courttypeid);

        //lock the two players
        lockLadderPlayers($challengerid, $challengeeid, $courttypeid);

        //Set up the partner array
        for ($i = 0; $i < count($challengers); ++$i) {
            
            if ($challengers[$i]['userid'] != get_userid()) {
                
                if (isDebugEnabled(1)) logMessage("team_ladder.challengerplayer: setting the challengers partner");
                $my_partner = array(
                    'firstname' => $challengers[$i]['firstname'],
                    'lastname' => $challengers[$i]['lastname'],
                    'email' => $challengers[$i]['email']
                );
                break;
            }
        }

        //send the emails to the various teams
        confirmChallengerTeam($my_partner, $challengees);
        confirmChallengeeTeam($challengees, $message);
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

if (isDebugEnabled(1)) logMessage("team_ladder: initializing the view");
$availbleSports = load_avail_sports();
$ladderplayers = getLadderTeam($courttypeid, get_clubid());
$playingInLadder = isPlayingInLadder(get_userid() , $courttypeid);
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/team_ladder_form.php");
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
    
    if (isDebugEnabled(1)) logMessage("team_ladder.createChallengematch: creating a challenge match for challenger: $challengerid and challengee: $challengeeid with courttype $courttypeid");
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
 * This is used for getting the ladder team
 *
 * @param $courttypeid
 */
function getLadderTeam($courttypeid, $clubid) {
    
    if (isDebugEnabled(1)) logMessage(sprintf("team_ladder.getLadderTeam: " . "getting the players in the ladder for " . "courttype %s in club %s", $courttypeid, $clubid));
    $query = "SELECT ladder.userid,
					 ladder.ladderposition,
					 ladder.going,
					 ladder.locked
				FROM tblClubLadder ladder
				WHERE ladder.courttypeid=$courttypeid
				AND ladder.enddate IS NULL
				AND ladder.clubid = $clubid
				ORDER BY ladder.ladderposition";
    $result = db_query($query);
    $array = array();
    while ($player = mysql_fetch_array($result)) {

        //get users for team id
        $playerarray = getFullnameForTeamPlayers($player['userid']);
        $firstplayer = $playerarray[0]['firstname'] . " " . $playerarray[0]['lastname'];
        $firstemail = $playerarray[0]['email'];
        $secondplayer = $playerarray[1]['firstname'] . " " . $playerarray[1]['lastname'];
        $secondemail = $playerarray[1]['email'];
        $item = array(
            'userid' => $player['userid'],
            'ladderposition' => $player['ladderposition'],
            'going' => $player['going'],
            'firstplayer' => $firstplayer,
            'firstemail' => $firstemail,
            'secondplayer' => $secondplayer,
            'secondemail' => $secondemail,
            'locked' => $player['locked']
        );
        $array[] = $item;
    }
    return $array;
}
/**
 * True is user is, false if player isn't
 *
 * @param $userid
 * @param $courttypeid
 */
function isPlayingInLadder($userid, $courttypeid) {
    
    if (isDebugEnabled(1)) logMessage("team_ladder.isPlayingInLadder checking userid $userid and courttypeid $courttypeid");
    $teams = getTeamsForUser($userid);
    $teamrows = mysql_num_rows($teams);
    $teamINClause = "";

    //if they aren't on any teams, they sure as hell aren't on the ladder
    
    if ($teamrows == 0) {
        return false;
    }

    //build in clause
    for ($i = 0; $i < $teamrows; ++$i) {
        $team = mysql_fetch_array($teams);
        
        if ($i != 0) {
            $teamINClause.= ",";
        }
        $teamINClause.= "$team[teamid]";
    }
    $query = "SELECT 1 FROM tblClubLadder WHERE userid IN ($teamINClause) AND courttypeid = $courttypeid AND clubid = " . get_clubid() . " AND enddate IS NULL";
    $result = db_query($query);
    $rows = mysql_num_rows($result);
    $value = $rows > 0 ? "is" : "is not";
    
    if (isDebugEnabled(1)) logMessage("team_ladder.isPlayingInLadder: $userid  $value in the ladder for courttype $courttypeid.");
    
    if ($rows > 0) {
        return true;
    } else {
        return false;
    }
}
/**
 * Sends out the emails to the team that initiated the challenge
 *
 * @param $my_partner mypartner->firstname,lastname,email
 * @param $chalengees arrays of the team being challenges (use getFullnameForTeamPlayers)
 */
function confirmChallengerTeam($my_partner, $challengees) {
    
    if (isDebugEnabled(1)) logMessage("team_ladder.confirmChallengerTeam: sending out emails to challenger and my partner " . $my_partner['firstname']);
    $from_email = "Sportsynergy <player.mailer@sportsynergy.net>";
    $template = get_sitecode();
    $subject = get_clubname() . " - Ladder Match Confirmation";
    $var = new Object;
    $var->yourpartner = $my_partner['firstname'] . " " . $my_partner['lastname'];
    $var->challengee1_fullname = $challengees[0]['firstname'] . " " . $challengees[0]['lastname'];
    $var->challengee2_fullname = $challengees[1]['firstname'] . " " . $challengees[1]['lastname'];
    $challenger_emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/confirm_doubles_ladder_match_challenger.php", $var);
    $challenger_email_s = get_userfullname() . " <" . get_email() . ">";
    $challenger_email = array(
        $challenger_email_s => array(
            'name' => get_userfirstname()
        )
    );
    $content = new Object;
    $content->line1 = $challenger_emailbody;
    $content->clubname = get_clubname();

    //Send email to the person that make the challenge
    sendgrid_email($subject, $challenger_email, $content, "Team Ladder");
    $var->yourpartner = get_userfullname();
    $challenger_emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/confirm_doubles_ladder_match_challenger.php", $var);
    $challenger_email_s = $my_partner['firstname'] . " " . $my_partner['lastname'] . " <" . $my_partner['email'] . ">";
    $my_partner_first_name = $my_partner['firstname'];
    $challenger_email = array(
        $challenger_email_s => array(
            'name' => $my_partner_first_name
        )
    );
    $content = new Object;
    $content->line1 = $challenger_emailbody;
    $content->clubname = get_clubname();

    //Send email to the partner of the person that make the challenge
    sendgrid_email($subject, $challenger_email, $content, "Team Ladder");
}
/**
 * Sends out the emails to the team that has been challenged.  This function uses the get_user() so this should only be called by the current user.
 *
 * @param $challengers
 * @param $chalengees
 * @param $message
 */
function confirmChallengeeTeam($challengees, $message) {
    
    if (isDebugEnabled(1)) logMessage("team_ladder.confirmChallengeeTeam: sending out emails to challengees " . $challengees[0]['firstname'] . " and " . $challengees[1]['firstname']);
    $template = get_sitecode();
    $subject = get_clubname() . " - You've been challenged in a ladder match";
    $challengee_email = array();
    for ($i = 0; $i < count($challengees); ++$i) {
        $to_email = $challengees[$i]['firstname'] . " " . $challengees[$i]['lastname'] . " <" . $challengees[$i]['email'] . ">";
        $challengee_email[$to_email] = array(
            'name' => $challengees[$i]['firstname']
        );
    }
    $content = new Object;
    $content->line1 = nl2br($message);
    $content->clubname = get_clubname();
    $from_email = get_userfullname() . " <" . get_email() . ">";
    $template = get_sitecode() . "-blank";

    //Send the email
    sendgrid_email($subject, $challengee_email, $content, "Team Ladder");
}
?>
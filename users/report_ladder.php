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
* - validate_form()
* - scoreChallengeMatch()
* - emailDoublesLadderMatch()
* - emailLadderMatch()
* - unlockPlayers()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
require ($_SESSION["CFG"]["libdir"] . "/sendgrid-php/SendGrid_loader.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
require_loginwq();

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

$DOC_TITLE = "Report Ladder Match";

//Set the http variables
$challengematchid = $_REQUEST["challengematchid"];
$laddertype = $_REQUEST["laddertype"];
$source = $_REQUEST["source"];

if ($laddertype == "player") {
    $ladderMatchArray = loadLadderMatch($challengematchid);
} else {
    $ladderMatchArray = loadDoublesLadderMatch($challengematchid);
}

/* form has been submitted, now reserve court */

if (match_referer() && isset($_POST["submitme"])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    
    if (empty($errormsg)) {
        
        if ($frm['winner'] == "challenger") {
            $winneruserid = $frm['challengerid'];
            $loseruserid = $frm['challengeeid'];
            $score = $frm['score'];
        } else {
            $winneruserid = $frm['challengeeid'];
            $loseruserid = $frm['challengerid'];
            $score = - $frm['score'];
        }

        // Mark the score on the challenge match
        scoreChallengeMatch($score, $frm['challengematchid']);

        // Update the ladder
        $details = adjustClubLadder($winneruserid, $loseruserid, $frm['courttypeid'], get_clubid());

        //Unlock the players
        unlockPlayers($winneruserid, $loseruserid, $frm['courttypeid']);

        //redirect the person back to the club ladder
        
        if ($frm['laddertype'] == "player") {
            emailLadderMatch($winneruserid, $loseruserid, abs($score) , $details, $frm['challengeeid'], $frm['laddertype']);
            header("Location: $wwwroot/users/player_ladder.php");
        } else {
            emailDoublesLadderMatch($winneruserid, $loseruserid, abs($score) , $details, $frm['challengeeid'], $frm['laddertype']);
            header("Location: $wwwroot/users/team_ladder.php");
        }
    }
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/report_ladder_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $msg = "";
    return $msg;
}
/**
 *
 * @param unknown_type $score
 * @param unknown_type $challengematchid
 */
function scoreChallengeMatch($score, $challengematchid) {
    logMessage("report_ladder.scoreChallengeMatch: Scoring the challenge match $score and challengematchid $challengematchid");
    $query = "UPDATE tblChallengeMatch challenge SET challenge.score = '$score' WHERE id = '$challengematchid'";
    db_query($query);
}
/**
 *
 * For doubles
 *
 * @param unknown_type $winnerid
 * @param unknown_type $loserid
 * @param unknown_type $score
 * @param unknown_type $details
 * @param unknown_type $challengeeid
 */
function emailDoublesLadderMatch($winnerid, $loserid, $score, $details, $challengeeid) {
    
    if (isDebugEnabled(1)) logMessage("report_ladder.emailDoublesLadderMatch: sending out emails to winner $winnerid and loser $loserid about the score $score and challengeeid $challengeeid");
    $winner = getFullnameForTeamPlayers($winnerid);
    $loser = getFullnameForTeamPlayers($loserid);

    /* email the user with the new account information */
    $var = new Object;
    $var->w_fullname = $winner[0]['fullname'] . " and " . $winner[1]['fullname'];
    $var->l_fullname = $loser[0]['fullname'] . " and " . $loser[1]['fullname'];
    $var->support = $_SESSION["CFG"]["support"];
    $var->w_oldspot = $details->winneroldspot;
    $var->w_newspot = $details->winnernewspot;
    $var->l_oldspot = $details->loseroldspot;
    $var->l_newspot = $details->losernewspot;
    $clubfullname = get_clubname();
    $var->clubfullname = $clubfullname;
    $var->score = 3 - $score;
    $var->verb = "were";

    // If the guy who got challenged won, then no change in the ladder
    
    if ($challengeeid == $winnerid) {
        
        if (isDebugEnabled(1)) logMessage("report_ladder.emailDoublesmatch: the guy who got challenged won");
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/report_ladder_match_nochange.php", $var);
    } else {
        
        if (isDebugEnabled(1)) logMessage("report_ladder.emailDoublesmatch: variables the guy who got challenged lost");
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/report_ladder_match.php", $var);
    }
    $emailbody = nl2br($emailbody);

    // Provide Content
    $content = new Object;
    $content->line1 = $emailbody;
    $content->clubname = get_clubname();
    $template = get_sitecode();
    $subject = get_clubname() . " - Ladder Match Report";
    $from_email = "Sportsynergy <player.mailer@sportsynergy.net>";
    $to_emails = array();

    // Put all of the email addresses in an array
    for ($i = 0; $i < count($winner); ++$i) {
        $to_email = $winner[$i]['firstname'] . " " . $winner[$i]['lastname'] . " <" . $winner[$i]['email'] . ">";
        $to_emails[$to_email] = array(
            'name' => $winner[$i]['firstname']
        );
    }
    for ($i = 0; $i < count($loser); ++$i) {
        $to_email = $loser[$i]['firstname'] . " " . $loser[$i]['lastname'] . " <" . $loser[$i]['email'] . ">";
        $to_emails[$to_email] = array(
            'name' => $loser[$i]['firstname']
        );
    }

    //Send the email to the loser
    sendgrid_email($subject, $to_emails, $content, "Ladder Match");
}
/**
 * Emails the users the results.  If the challengee wins that means that the ladder stays the same.
 *
 * laddertype can be player or team
 *
 * @param $winnerid
 * @param $loserid
 * @param $score
 */
function emailLadderMatch($winnerid, $loserid, $score, $details, $challengeeid, $laddertype) {
    
    if (isDebugEnabled(1)) logMessage("report_ladder.emailLadderMatch: sending out emails to winner $winnerid and loser $loserid about the score $score and challengeeid $challengeeid");

    /* load up the user record for the winner */
    $query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$winnerid'";
    $qid = db_query($query);
    $winner = db_fetch_object($qid);

    /* load up the user record for the winner */
    $query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$loserid'";
    $qid = db_query($query);
    $loser = db_fetch_object($qid);

    /* email the user with the new account information */
    $var = new Object;
    $var->w_firstname = $winner->firstname;
    $var->w_fullname = $winner->firstname . " " . $winner->lastname;
    $var->l_firstname = $loser->firstname;
    $var->l_fullname = $loser->firstname . " " . $loser->lastname;
    $var->support = $_SESSION["CFG"]["support"];
    $var->w_oldspot = $details->winneroldspot;
    $var->w_newspot = $details->winnernewspot;
    $var->l_oldspot = $details->loseroldspot;
    $var->l_newspot = $details->losernewspot;
    $clubfullname = get_clubname();
    $var->clubfullname = $clubfullname;
    $var->score = 3 - $score;
    $var->verb = "was";

    // If the guy who got challenged won, then no change in the ladder
    
    if ($challengeeid == $winnerid) {
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/report_ladder_match_nochange.php", $var);
    } else {
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/report_ladder_match.php", $var);
    }
    $emailbody = nl2br($emailbody);

    // Provide Content
    $content = new Object;
    $content->line1 = $emailbody;
    $content->clubname = get_clubname();
    $template = get_sitecode();
    $subject = get_clubname() . " - Ladder Match Report";
    $from_email = "Sportsynergy <player.mailer@sportsynergy.net>";

    //Send the email to the winner
    $to_email = array(
        $winner->email => array(
            'name' => $winner->firstname
        )
    );
    sendgrid_email($subject, $to_email, $content, "Ladder Match");

    //Send the email to the loser
    $to_email = array(
        $loser->email => array(
            'name' => $loser->firstname
        )
    );
    sendgrid_email($subject, $to_email, $content, "Ladder Match");
}
/**
 * Unlocks the players
 *
 * @param $winneruserid
 * @param $loseruserid
 * @param $courttypeid
 */
function unlockPlayers($winneruserid, $loseruserid, $courttypeid) {
    
    if (isDebugEnabled(1)) logMessage("report_ladder.unlockPlayers: unlocking players $winneruserid and loseruserid $loseruserid for courttypeid $courttypeid");
    $query = "UPDATE tblClubLadder ladder SET locked = 'n' WHERE ladder.userid = '$winneruserid' OR ladder.userid = '$loseruserid' 
					AND ladder.courttypeid = '$courttypeid' ";
    db_query($query);
}
?>
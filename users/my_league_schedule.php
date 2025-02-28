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
* - password_valid()
* - update_password()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
require_login();

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST)) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
   
    
    if (empty($errormsg)) {
        
        $noticemsg = "";
    }
}
$DOC_TITLE = "League Schedule";

//$league_schedule = load_league_schedule();

include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/my_league_schedule_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the forgot password form, and return the error messages in a string.
     * if the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = ""; // put whatever validation message here
    
    
    return $msg;
}

/*
    Returns a mysql result
*/

function getRecentLadderMatches( $opponent, $boxid ){

    $recent_matches = array("-","-");
    $current = get_userid();

    // TODO make sure to only get the last two
    $query = "SELECT tLM.score, tLM.winnerid
            FROM tblLadderMatch tLM
                    INNER JOIN tblBoxLeagues tBL ON tLM.ladderid = tBL.ladderid
            WHERE
                (winnerid = $current and loserid = $opponent
            OR
                winnerid = $opponent and loserid = $current)
            AND tBL.boxid = $boxid
            AND match_time > tBL.startdate
            AND tLM.enddate IS NULL
            AND tLM.league = TRUE
            ORDER BY tLM.match_time";

    $result = db_query($query);
    $index = 0;
    while ($ladderresult = db_fetch_array($result)) {
        
        //Switch around if opponent won
        $score = $ladderresult['score'];

        if($opponent == $ladderresult['winnerid']){
            if (isDebugEnabled(1)) logMessage("my_league_schedule:opponent won!");
            $score = reverse_scores( $ladderresult['score'] );

        }
        $recent_matches[$index] = $score;
        $index = $index + 1;
    }

    if (isDebugEnabled(1)) logMessage("my_league_schedule:first result for $opponent: ".$recent_matches[0] );
    if (isDebugEnabled(1)) logMessage("my_league_schedule:second result for $opponent: ".$recent_matches[1] );

    return $recent_matches;
    
}

/**
 * Does what you think it will do
 * 
 */
function reverse_scores($score){

    $pieces = explode("-", $score);
    return $pieces[1]."-".$pieces[0];
}

/*
    Returns a mysql result
*/
function load_league_schedule( $boxid){

    if (isDebugEnabled(1)) logMessage("my_league_schedule: loading up load_league_schedule for $boxid");

    $query = "SELECT concat(tU.firstname,' ',tU.lastname) as fullname , tBL.boxname, tCLT.name, tU.userid
        FROM tblBoxLeagues tBL
                INNER JOIN tblkpBoxLeagues tBLu ON tBLu.boxid = tBL.boxid
                INNER JOIN tblUsers tU ON tBLu.userid = tU.userid
                INNER JOIN tblClubLadderTeamMember tCLTM ON tBLu.userid = tCLTM.userid
                INNER JOIN tblClubLadderTeam tCLT ON tCLTM.teamid = tCLT.id AND tCLT.ladderid = tBL.ladderid
        WHERE tCLT.enddate IS NULL AND tBL.boxid = $boxid
        AND tCLTM.enddate IS NULL
        AND tU.userid <> ".get_userid();

    // Get box id for player

    return db_query($query);
}


?>
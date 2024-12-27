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
* - insert_boxuser()
* Classes list:
*/
include ("../application.php");
require_login();
require_priv("2");

//Set the http variables
$action = $_REQUEST["action"];
$teamid = $_REQUEST["teamid"];

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;

    

    if (empty($errormsg) && $frm['action']=='remove') {
        remove_teamplayer($frm);

    } else {

        $errormsg = validate_form($frm);
        if (empty($errormsg) && empty($action)) {
            insert_teamplayer($frm);
            
            $noticemsg = "Club team Updated.  Good Job!<br/><br/>";
        }
    }
}

if(!isset($teamid) ){
    header("Location: $wwwroot/admin/club_teams.php");
}

$teamquery = "SELECT tCLT.name AS teamname, tCSL.name AS laddername, tCLT.id,  tCSL.id AS ladderid
                FROM tblClubLadderTeam tCLT
                INNER JOIN clubpro_main.tblClubSiteLadders tCSL ON tCLT.ladderid = tCSL.id
                WHERe tCLT.id =$teamid";

// run the query on the database
$result = db_query($teamquery);
$teamarray = mysqli_fetch_array($result);
$ladderid = $teamarray[ladderid];



//Set some variables for the form
$DOC_TITLE = "Team - $teamarray[teamname]";

include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/club_team_manage_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($frm) {

       
    $errors = new clubpro_obj;
    $msg = "";


    // Make sure that they aren't on another team for the same ladder
    $query = "SELECT count(*) FROM tblClubLadderTeamMember tCLTm
                INNER JOIN tblClubLadderTeam tCLT ON tCLTm.teamid = tCLT.id
                WHERE tCLT.ladderid=$frm[ladderid]
                AND tCLTm.userid = $frm[teamplayer]";
    $result = db_query($query);
    $alreadyonteam = mysqli_result($result, 0);

    if ( $alreadyonteam > 0) {
        $errors->clubteam = true;
        $msg.= "This player is already on another team for the ladder ";
    } 

 
    // Make sure that they aren't already on this team
    $query = "SELECT count(*) from tblClubLadderTeamMember
                WHERE teamid = $frm[teamid] and userid= $frm[teamplayer]";
    $result = db_query($query);

    $alreadyonteam = mysqli_result($result, 0);

    if ( $alreadyonteam > 0) {
        $errors->clubteam = true;
        $msg.= "This player is already on the team ";
    } 

    return $msg;
}


function insert_teamplayer(&$frm) {

    // First thing we need to do is find out how many players are
    // in the league.  This will determine what place the user will start with.

    if ( empty($frm['teamplayer']) ){
        return;
    } 

    /* add the new user into the database */
    $query = "INSERT INTO tblClubLadderTeamMember (
                teamid, userid
                ) VALUES (
                           '$frm[teamid]'
                          ,'$frm[teamplayer]')";

    // run the query on the database
    $result = db_query($query);
}

function remove_teamplayer(&$frm) {

    if (isDebugEnabled(1)) logMessage("club_team_manage: deleting a team player now");
        
    $query = "UPDATE tblClubLadderTeamMember SET enddate = NOW() WHERE teamid = $frm[teamid] AND userid = $frm[userid]";
    $result = db_query($query);

}

?>





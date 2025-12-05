<?php


include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/clubadminlib.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

$DOC_TITLE = "Club Teams";


if (match_referer() && isset($_POST['cmd'])) {
    $frm = $_POST;

   // Do stuff here
}



include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/club_teams_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
    $errors = new clubpro_obj;


}

function getClubTeams($siteid) {

    $query = "SELECT tCLT.id, tCLT.name, tCLT.score, tCLT.games, tCLT.lastUpdated 
                FROM tblClubLadderTeam tCLT
                INNER JOIN tblClubSiteLadders tCSL ON tCLT.ladderid = tCSL.id
                WHERE tCLT.enddate IS NULL
                AND tCSL.siteid =  $siteid
                ORDER BY tCLT.ladderid DESC,tCLT.score DESC";
    
    // run the query on the database
    return db_query($query);

}

function getClubTeamMembers($clubteamid) {

    $query = "SELECT concat(tU.firstname, ' ', tU.lastname) AS teamplayername, tCLT.id
                FROM tblClubLadderTeam tCLT
                INNER JOIN tblClubLadderTeamMember tCLTm ON tCLT.id = tCLTm.teamid
                INNER JOIN tblUsers tU ON tCLTm.userid = tU.userid
                WHERe tCLT.id = $clubteamid
                AND tCLTm.enddate IS NULL;";
    
    // run the query on the database
    return db_query($query);

}


?>


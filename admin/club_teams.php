<?php

include ("../application.php");
require_login();
require_priv("2");

/* form has been submitted, check if it the user login information is correct */


if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    
    if (empty($errormsg) && $frm['action']=='remove') {
        delete_club_team($frm);
    } else {

        $errormsg = validate_form($frm, $errors);
        if (empty($errormsg) && empty($action)) {
            insert_clubteam($frm);
        }
    }
    
}

$DOC_TITLE = "Club Teams Setup";
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/club_teams_registration_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    if (empty($frm["clubteam"])) {
        $errors->clubteam = true;
        $msg.= "You did not specify a club team ";
    } 
    
    if ( empty($frm["ladder"]) ){
            
        $errors->ladder = true;
        $msg.= "Please specifiy the ladder."; 
    }

    

    return $msg;
}
function insert_clubteam(&$frm) {

    $clubteam = addslashes($frm['clubteam']);
    $query = "INSERT INTO tblClubLadderTeam (
                name, ladderid
                ) VALUES (
                          '$clubteam'
                          ,$frm[ladder])";

    // run the query on the database
    $result = db_query($query);
}

function delete_club_team(&$frm) {

    if (isDebugEnabled(1)) logMessage("club_team_manage: deleting a team now");
    
    $query = "UPDATE tblClubLadderTeam SET enddate = NOW() where id = $frm[teamid]";
    $result = db_query($query);
    

}

?>



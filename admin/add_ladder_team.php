<?php

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
$DOC_TITLE = "Team Ladder";
require_loginwq();
require_priv("2");

// load ladder info
if (!empty($_POST['ladderid'])) {
    $_SESSION["ladder_id"] = $_POST['ladderid'];
}
$ladderid = $_SESSION["ladder_id"];
$ladderplayers = getLadder($ladderid);
$ladderdetails = getLadderDetails($ladderid);

/* form has been submitted, try to create the new user account */

if (match_referer() && isset($_POST['playeronename'])  ) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    
    if (empty($errormsg)) {
        insert_ladder_player($frm);
        redirect($_SESSION["CFG"]["wwwroot"] . "/users/player_ladder.php");
        die;
    }
}



include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/add_ladder_team_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    return $msg;
}

function insert_ladder_player(&$frm) {

    global $dbh;

    $userid = $frm['userid'];
    $ladderid = $frm['ladderid'];
    $position = $frm['placement'];
    
    /* add the new user into the database */
    if (isDebugEnabled(1)) logMessage("add_ladder_player.insert_ladder_player ");

        //Check to see if player is already in ladder
        $check = "SELECT count(*) from tblClubLadder 
        				WHERE userid = $userid 
        				AND ladderid = $ladderid 
        				AND enddate IS NULL";
        $checkResult = db_query($check);

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
}


/**
 * Make sure that nobody else has this same email address...
 * @param $frm
 * @param $errors
 */
function validate_email(&$frm, &$errors) {
    
    return;
}
?>
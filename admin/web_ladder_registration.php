<?php

include ("../application.php");
require_login();
require_priv("2");

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    
    if (empty($errormsg) && empty($action)) {
        insert_box($frm);
    }
}
$DOC_TITLE = "Box League Setup";
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/web_ladder_registration_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    if (empty($frm["boxname"])) {
        $errors->boxname = true;
        $msg.= "You did not specify a box name ";
    } elseif (empty($frm["courttypeid"])) {
        $errors->courttypeid = true;
        $msg.= "You did not specify a court type.";
    }

    if( isJumpLadderRankingScheme() ) {
        if ( empty($frm["ladder"]) ){
            $errors->ladder = true;
            $msg.= "Please specifiy the ladder."; 
        }
    }

    return $msg;
}
function insert_box(&$frm) {

    /* add the new user into the database */
    $clubquery = "SELECT timezone from tblClubs WHERE clubid=" . get_clubid() . "";
    $clubresult = db_query($clubquery);
    $clubobj = db_fetch_object($clubresult);
    $tzdelta = $clubobj->timezone * 3600;

    //Pad Zeros where necessary
    $daystring = "$frm[enddateday]";
    $monthstring = "$frm[enddatemonth]";
    
    if ($frm['enddateday'] < 10) {
        $daystring = "0$frm[enddateday]";
    }
    
    if ($frm['enddatemonth'] < 10) {
        $monthstring = "0$frm[enddatemonth]";
    }
    $datestring = "$frm[enddateyear]$monthstring$daystring";
    $timestamp = gmmktime(0, 0, 0, $frm['enddatemonth'], $frm['enddateday'], $frm['enddateyear']);
    $startdate = date("Ymd");
    //Get the number of Boxes at the club
    $useridquery = "SELECT boxrank
                          FROM tblBoxLeagues
                          WHERE siteid=" . get_siteid() . "";
    $useridresult = db_query($useridquery);
    $numberofrows = mysqli_num_rows($useridresult);
    $lastboxrank = $numberofrows + 1;
    $ladderid = isset($frm['ladder']) ? $frm['ladder'] : "NULL";
    $boxname = addslashes($frm['boxname']);
    $autoschedule =  $frm['autoschedule'] == 'yes' ? 'TRUE':'FALSE';
    $ladder_type = $frm['ladder_type'];

    $query = "INSERT INTO tblBoxLeagues (
                boxname, siteid, courttypeid, boxrank, startdate, enddate, enddatestamp,ladderid,autoschedule,ladder_type
                ) VALUES (
                          '$boxname'
                          ,'" . get_siteid() . "'
                          ,$frm[courttypeid]
                          ,$lastboxrank
                          ,$startdate
                          ,$datestring
                          ,$timestamp
                          ,$ladderid
                          ,$autoschedule
                          ,'$ladder_type')";

    
                          // run the query on the database
    $result = db_query($query);
}
?>



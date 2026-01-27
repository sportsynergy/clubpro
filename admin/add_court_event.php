<?php

include ("../application.php");
require_login();
require_priv("2");
$DOC_TITLE = "Add Court Event";

//This puppy will be set when editing a policy
$eventid = $_REQUEST["eventid"];

if (!empty($eventid)) {
    $courtEvent = load_court_event($eventid);
    $courtEventName = htmlentities($courtEvent['eventname']);
    $DOC_TITLE = "Update Court Event";
}

/* form has been submitted, try to create the new role */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    
    if (empty($errormsg)) {
        insert_court_event($frm);
        $successmsg = "Court Event has been successfully saved.";
       
    }
} elseif (isset($_POST['back'])) {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    header("Location: $wwwroot/admin/policy_preferences.php");
}
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/add_court_event_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {
    $errors = new clubpro_obj;
    $msg = "";

    //Make sure that they selected everything
    
    if (empty($frm["name"])) {
        $msg.= "You did not specify an event name.";
        $errors->name = true;
    }
    return $msg;
}
function insert_court_event(&$frm) {

    // Strip Slashes
    $name = addslashes($frm['name']);

    //If this is the case, then we're updating an existing policy
    
    if (!empty($frm['policyid'])) {
        $query = "UPDATE tblEvents SET
					eventname = '$name'
	                ,playerlimit = '$frm[playerlimit]'
	        		WHERE eventid = '$frm[policyid]'";
    } else {
        $query = "INSERT INTO tblEvents (
	                eventname, siteid, playerlimit
	                ) VALUES (
	                           '$name'
	                          ," . get_siteid() . "
	                          ,$frm[playerlimit])";
    }

    // run the query on the database
    $result = db_query($query);
}
?>
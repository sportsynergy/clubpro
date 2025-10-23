<?php

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
require_login();

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

//Set the http variables
// Sources-> player_lookup.php

$userid = $_GET["userid"];
$searchname = $_GET["searchname"];
$extraParametersResult = load_site_parameters();

// Player information can be pulled up from multiple places
// in the application

$origin = $_GET["origin"];
$courttypeid = $_GET["courttypeid"];
$sortoption = $_GET["sortoption"];
$displayoption = $_GET["displayoption"];

if (isset($userid)) {
    $frm = load_user_profile($userid);
    $registeredSports = load_registered_sports($userid);
} else {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    header(sprintf("Location:  %s/users/player_lookup.php", $wwwroot));
}
$DOC_TITLE = "Player Info";
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_info_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
/**
 * validate the forgot password form, and return the error messages in a string.
 * if the string is empty, then there are no errors
 *
 * @param unknown_type $frm
 * @param unknown_type $errors
 */
function validate_form(&$frm, &$errors) {
    $errors = new clubpro_obj;
    $msg = "";
    
    if (empty($frm["email"])) {
        $errors->email = true;
        $msg.= "<li>You did not specify your email address";
    } else 
    if (empty($frm["firstname"])) {
        $errors->firstname = true;
        $msg.= "<li>You did not specify your first name";
    } else 
    if (empty($frm["lastname"])) {
        $errors->lastname = true;
        $msg.= "<li>You did not specify your last name";

        //Not a required Feild
        //} elseif (empty($frm["homephone"])) {

        //        $errors->homephone = true;

        //        $msg .= "<li>You did not specify your home phone number";

        //Not a required Feild

        //} elseif (empty($frm["workphone"])) {

        //        $errors->workphone = true;

        //        $msg .= "<li>You did not specify your work phone number";

        
    } else 
    if (empty($frm["useraddress"])) {
        $errors->useraddress = true;
        $msg.= "<li>You did not specify your address";
    }
    return $msg;
}
?>
<?php

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

// Security Issue Resolved, by always enforcing
// the session ID.
$userid = $_SESSION['user']['userid'];

$DOC_TITLE = "Edit Account";
unset($authSites);
unset($registeredSports);
$registeredSports = load_registered_sports($userid);
$authSites = load_auth_sites($userid);
$availbleSports = load_avail_sports();
$availableSites = load_avail_sites();
$extraParametersResult = load_site_parameters();


// Make sure the form actually has been posted.  
// An error would occur if you pressed "Account Setttings" link twice
// Extra parsing of the POST special variable resolves this issue 
if (match_referer() && isset($_POST['submitme']) ) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);

    if (isset($userid)) {
        $useridstring = sprintf("?userid=%s",$userid);
    }
	
    $backtopage = $_SESSION["CFG"]["wwwroot"] . sprintf("/users/change_settings.php%s",$useridstring);
    
    if (empty($errormsg)) {
        update_settings($frm, $extraParametersResult);
        
        if (mysqli_num_rows($extraParametersResult) > 0) {
            mysqli_data_seek($extraParametersResult, 0);
        }
        $successmsg = "Your profile was saved.  Good Job!";
    }
}

$frm = load_user_profile($userid);

include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/change_settings_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the forgot password form, and return the error messages in a string.
     * if the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    if (empty($frm["email"])) {
        $errors->email = true;
        $msg.= "You did not specify your email address";
    }
    $otherClubUser = verifyEmailUniqueAtClub($frm["email"], $frm["userid"], get_clubid());
    
    if (isset($otherClubUser)) {
        $errors->email = true;
        $msg.= "The email address <b>" . ov($frm["email"]) . "</b> already exists";
    } elseif (!is_email_valid($frm["email"])) {
        $errors->email = true;
        $msg.= "Please enter a valid email address";
    } elseif (empty($frm["firstname"])) {
        $errors->firstname = true;
        $msg.= "You did not specify your first name";
    } elseif (empty($frm["lastname"])) {
        $errors->lastname = true;
        $msg.= "You did not specify your last name";
    }
    
    return $msg;
}
function update_settings(&$frm, $extraParametersResult) {

    /* set the user's password to the new one */
    
    if (isDebugEnabled(1)) logMessage("change_settings.update_settings: saving profile for " . $frm['firstname'] . " " . $frm['lastname']);

    //If userid is not set this is being run by a player who is updating
    //their own accoutn information.  If this is the case we will get the userid

    //out of the session.

    
    if (!isset($frm['userid'])) {
        
        if (isDebugEnabled(1)) logMessage("change_settings.update_settings: getting userid from session");
        $userid = get_userid();
    } else {
        $userid = $frm['userid'];
    }
    
    $firstName = addslashes($frm['firstname']);
    $lastName = addslashes($frm['lastname']);

    $available_5pm = 'false';
    $available_6pm = 'false';
    $available_7pm = 'false';

    if( $frm['available_5pm'] == 'on' ){
        $available_5pm = 'true';
    } 
    if( $frm['available_6pm'] == 'on' ){
        $available_6pm = 'true';
    }
    if( $frm['available_7pm'] == 'on' ){
        $available_7pm = 'true';
    }

    //Update User
    $updateQuery = "UPDATE tblUsers SET
                 email = '$frm[email]'
                ,firstname = '$firstName'
                ,lastname = '$lastName'
                ,homephone = '$frm[homephone]'
                ,workphone = '$frm[workphone]'
                ,cellphone = '$frm[cellphone]'
                ,useraddress = '$frm[useraddress]'
        WHERE userid = '$userid'";
    db_query($updateQuery);

    //Update Club user
    $qid = db_query("
        UPDATE tblClubUser SET
			recemail = '$frm[recemail]'
            ,recsms = '$frm[recsms]'
            ,available_at_5 = $available_5pm
            ,available_at_6 = $available_6pm
            ,available_at_7 = $available_7pm
            ,recleaguematchnotifications = '$frm[recleaguematchnotifications]'
        WHERE userid = '$userid'");

    // Update the Custom Parameters
    while ($parameterArray = mysqli_fetch_array($extraParametersResult)) {
        $parameterId = $parameterArray['parameterid'];
        
        if ($frm["parameter-$parameterId"]) {
            
            if (isDebugEnabled(1)) logMessage("change_settings.update_settings: adding custom parameter:  " . $frm["parameter-$parameterId"]);
            $parameterValue = $frm["parameter-$parameterId"];
            $query = "INSERT INTO tblParameterValue (userid,parameterid,parametervalue) 
                			VALUES ('$userid', '$parameterId','$parameterValue') 
                			ON DUPLICATE KEY UPDATE parametervalue = '$parameterValue'";
            db_query($query);
        }
    }
    unset($userid);
}
?>
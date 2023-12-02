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
* - update_settings()
* Classes list:
*/
/*
 * $LastChangedRevision: 858 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:29:16 -0500 (Mon, 14 Mar 2011) $

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
        $noticemsg = "Your profile was saved.  Good Job!<br/><br/>";
    }
}

$frm = load_user_profile($userid);
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/change_settings_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

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
    } elseif (empty($frm["homephone"])) {
        $errors->homephone = true;
        $msg.= "You did not specify a home phone number";
    } elseif (empty($frm["workphone"])) {
        $errors->workphone = true;
        $msg.= "You did not specify a work phone number";
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
    
    if (get_magic_quotes_gpc()) {
        $firstName = $frm['firstname'];
        $lastName = $frm['lastname'];
    } else {
        $firstName = addslashes($frm['firstname']);
        $lastName = addslashes($frm['lastname']);
    }

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
            ,available_at_5 = $available_5pm
            ,available_at_6 = $available_6pm
            ,available_at_7 = $available_7pm
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
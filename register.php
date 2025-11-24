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
* - isClubCodeRightLength()
* - doesClubCodeAlreadyExist()
* - registerClub()
* Classes list:
*/
include ("./application.php");
require './vendor/autoload.php';
$DOC_TITLE = "Register Club";

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_REQUEST['submitme'])) {
    $frm = $_REQUEST;
    $errormsg = validate_form($frm, $errors);
    
    if (empty($errormsg)) {
        registerClub($frm['clubname'], $frm['clubcode'], $frm['courtnumber'], $frm['courttype'], $frm['timezone'], $frm['adminuser'], $frm['adminpass1'], $frm['adminfirstname'], $frm['adminlastname'], $frm['adminemail']);
        include ($_SESSION["CFG"]["templatedir"] . "/header.php");
        include ($_SESSION["CFG"]["includedir"] . "/include_club_registration_success.php");
        include ($_SESSION["CFG"]["templatedir"] . "/footer.php");
        die;
    }
}
$availbleTimezones = load_avail_timezones();
include ($_SESSION["CFG"]["templatedir"] . "/register_club_form.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
/**
 *
 * @param $frm
 * @param $errors
 * @return unknown_type
 */
function validate_form(&$frm, &$errors) {

    /* validate the register password form, and return the error messages in a string.
     * if the string is empty, then there are no errors **/

    //make sure that the club code is the proper length and type
    $errors = new clubpro_obj;
    $msg = "";
    
    if (isDebugEnabled(1)) logMessage("Validating registration: clubname: " . $frm['clubname'] . " clubcode " . $frm['clubcode']);

    // Make sure that everything has been filled out
    
    if (empty($frm['clubname'])) {
        $errors->clubname = true;
        $msg.= "You did not specify your club name.";
    } elseif (empty($frm['clubcode'])) {
        $errors->clubcode = true;
        $msg.= "You did not specify your club code.";
    }

    //Make sure that the club code is formatted correctly
    elseif (strstr($frm['clubcode'], " ") == false ? false : true) {
        $errors->clubcode = true;
        $msg.= "Uhhh yeeaahhhh, you can't have spaces in the club code.";
    } elseif (!isClubCodeRightLength($frm['clubcode'])) {
        $errors->clubcode = true;
        $msg.= "The club code needs to be at least 6 characters, but not more than 10.";
    } elseif (doesClubCodeAlreadyExist($frm['clubcode'])) {
        $errors->clubcode = true;
        $msg.= "Sorry somebody already beat you to this code.  Pick again.";
    } elseif (empty($frm['adminuser'])) {
        $errors->adminuser = true;
        $msg.= "You did not specify your administrator user.";
    } elseif (empty($frm['adminpass1'])) {
        $errors->adminpass1 = true;
        $msg.= "You did not specify your admin password.";
    }

    //Make sure that the passwords match
    elseif (($frm['adminpass1']) != ($frm['adminpass2'])) {
        $errors->adminpass1 = true;
        $msg.= "Dude, your admin passwords don't match.  Try again.";
    } elseif (empty($frm["adminemail"])) {
        $errors->adminemail = true;
        $msg.= "You totally forgot to even enter an email address...duh.";
    } elseif (!empty($frm["adminemail"]) && !is_email_valid($frm["adminemail"])) {
        $errors->adminemail = true;
        $msg.= "Please enter a valid email address. C'mon you've done this before.";
    } elseif (empty($frm['adminfirstname'])) {
        $errors->adminfirstname = true;
        $msg.= "You did not specify your first name.";
    } elseif (email_exists($frm["adminemail"])) {
        $errors->adminemail = true;
        $msg.= "Whooops!  It looks like this email addres is already in the system.";
    } elseif (empty($frm['adminlastname'])) {
        $errors->adminlastname = true;
        $msg.= "You did not specify your last name.";
    }
    return $msg;
}
/**
 * Makes sure that this is at least 6 characters, but not more than 10 characters.
 *
 * @param $clubcode
 * @return unknown_type
 */
function isClubCodeRightLength($clubcode) {
    $length = strlen($clubcode);
    
    if ($length > 1 && $length < 20) {
        return true;
    } else {
        
        if (isDebugEnabled(1)) logMessage("register.isClubCodeRightLength: clubcode length is $length, not right.");
        return false;
    }
}
/**
 *  Looks in the database for this club code. The clubcode has to be unique.
 *
 * @param $clubcode
 * @return unknown_type
 */
function doesClubCodeAlreadyExist($clubcode) {
    $query = "SELECT 1 from tblClubSites where sitecode = '$clubcode'";
    $result = db_query($query);
    
    if (mysqli_num_rows($result) == 0) {
        return false;
    } else {
        return true;
    }
}
/**
 * There is quite of bit going on here.  Here is how many calls to the database
 * we have here (where n is the number of courts)
 *
 * inserts: 6 + n + 7n
 * selects: 3 + n
 *
 * So given the most is 6 courts, the maximum number of db operation is
 * 6+6+7(6) = 54
 * 3+ 6     = 9
 * -----------------
 *            63
 *
 *
 *
 * @param $clubName
 * @param $clubCode
 * @param $numberOfCourts
 * @param $courtType
 * @param $adminUser
 * @param $adminPass
 * @param $adminEmail
 * @return unknown_type
 */
function registerClub($clubName, $clubCode, $numberOfCourts, $courtType, $timezone, $adminUser, $adminPass, $adminFirstName, $adminLastName, $adminEmail) {

    //Make some adjustments
    $clubCode = strtolower($clubCode);
    $password = md5($adminPass);

    // Default Hours
    $defaultOpenTime = "06:00:00";
    $defaultCloseTime = "22:00:00";
    $defaultAdminRanking = "5";

    if (isDebugEnabled(1)) logMessage("register.registerClub: Club Name: $clubName\nClub Code: $clubCode\n# of Courts:  $numberOfCourts\nCourt Type: $courtType\nAdmin User: $adminUser\nAdmin Pass:$adminPass\nAdmin Email:$adminEmail");

    //Get the clubid.  These are not set up as auto increment because
    // of the way things were done before.

    $query = "SELECT max(clubid) FROM tblClubs";
    $result = db_query($query);
    $maxclubid = mysqli_result($result, 0);
    $clubid = $maxclubid + 1;

    //Add the stuff to the database
    // Add Club

    $query = "INSERT INTO tblClubs (
	                clubid, clubname, clubaddress, clubphone, timezone
	                ) VALUES (
	                          $clubid
	                          ,'$clubName'
							  ,''
	                          ,''
	                          ,'$timezone'
	                          )";
    db_query($query);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: Insert Club...Done");

    // Add Site (we call sitecode clubcode)
    $query = "INSERT INTO tblClubSites (
	                clubid, sitename, sitecode,daysahead,isLiteVersion
	                ) VALUES (
	                          '$clubid'
							  ,'$clubName'
	                          ,'$clubCode'
	                          ,'7'
	                          ,'y'
	                          )";
    db_query($query);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: Insert Site...Done");

    // Get the site id
    $query = "SELECT max(siteid) FROM tblClubSites";
    $result = db_query($query);
    $siteid = mysqli_result($result, 0);

    // Add Courts
    for ($i = 1; $i <= $numberOfCourts; ++$i) {

        // Add Site (we call sitecode clubcode)
        $query = "INSERT INTO tblCourts (
		                courttypeid, clubid, courtname ,siteid, displayorder
		                ) VALUES (
		                          '$courtType'
								  ,'$clubid'
		                          ,'Court $i'
		                          ,'$siteid'
		                          ,'$i'
		                          )";
        db_query($query);
        
        if (isDebugEnabled(1)) logMessage("register.registerClub: Insert Court $i...Done");
        $query = "SELECT max(courtid) FROM tblCourts";
        $result = db_query($query);
        $courtid = mysqli_result($result, 0);

        // Now add Court Hours for each day of the week
        for ($j = 0; $j < 7; ++$j) {

            // Add Site (we call sitecode clubcode)
            $query = "INSERT INTO tblCourtHours (
			                dayid, courtid, opentime ,closetime, hourstart, duration
			                ) VALUES (
			                          '$j'
									  ,'$courtid'
			                          ,'$defaultOpenTime'
			                          ,'$defaultCloseTime'
			                          ,'0'
			                          ,'1.0'
			                          )";
            db_query($query);
        }
        
        if (isDebugEnabled(1)) logMessage("register.registerClub: Insert Court $i...Done");
    }

    // Add Admin User
    $query = "INSERT INTO tblUsers (
	                username, password, firstname, lastname, email, homephone, workphone, cellphone, pager, useraddress, gender
	                ) VALUES (
	                          '$adminUser'
	                          ,'$password'
	                          ,'$adminFirstName'
	                          ,'$adminLastName'
	                          ,'$adminEmail'
	                          ,''
	                          ,''
	                          ,''
	                          ,''
	                          ,''
	                          ,'1'
	                          )";
    db_query($query);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: Insert Admin User...Done");
    $query = "SELECT max(userid) FROM tblUsers";
    $result = db_query($query);
    $adminid = mysqli_result($result, 0);
    $query = "INSERT INTO tblClubUser (
	                userid, clubid, msince, roleid, memberid
	                ) VALUES (
	                          '$adminid'
							  ,'$clubid'
	                          ,'" . gmdate("l F j") . "'
	                          ,'2'
	                          ,'')";
    db_query($query);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: Insert Admin Club User...Done");

    // Add Admin User Authorizations
    $query = "INSERT INTO `tblkupSiteAuth` ( 
		 			userid , siteid 
		 			) VALUES (
		 					'$adminid', 
		 					'$siteid')";
    db_query($query);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: Insert Admin Authorizations...Done");

    // Add Admin User Rankings
    $query = "INSERT INTO `tblUserRankings` (
		 			userid , courttypeid , ranking , hot , usertype  
		 			) VALUES (
		 						'$adminid', 
		 						'$courtType', 
		 						'$defaultAdminRanking', 
		 						'0', 
		 						'0')";
    db_query($query);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: Insert Admin Ranking...Done");

    // Get things ready on the file system
    // Create folder

    mkdir($_SESSION["CFG"]["dirroot"] . "/clubs/$clubCode", 0755);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: Created Club Configuration Folder...Done");

    // Create the Style Sheet
    $fp0 = fopen($_SESSION["CFG"]["dirroot"] . "/clubs/$clubCode/main.css", "w");
    fputs($fp0, ".ct2cl$clubid    { \n");
    fputs($fp0, "font-family: Arial, sans-serif;\n");
    fputs($fp0, "font-size: 10pt;\n");
    fputs($fp0, "font-weight: bold;\n");
    fputs($fp0, "color: #FFFFFF;\n");
    fputs($fp0, "text-align: center;\n");
    fputs($fp0, "background : Gray\n");
    fputs($fp0, "}\n");
    fputs($fp0, ".ct3cl$clubid    { \n");
    fputs($fp0, "font-family: Arial, sans-serif;\n");
    fputs($fp0, "font-size: 10pt;\n");
    fputs($fp0, "font-weight: bold;\n");
    fputs($fp0, "color: #FFFFFF;\n");
    fputs($fp0, "text-align: center;\n");
    fputs($fp0, "background : Gray\n");
    fputs($fp0, "}\n");
    fputs($fp0, ".ct4cl$clubid    { \n");
    fputs($fp0, "font-family: Arial, sans-serif;\n");
    fputs($fp0, "font-size: 10pt;\n");
    fputs($fp0, "font-weight: bold;\n");
    fputs($fp0, "color: #FFFFFF;\n");
    fputs($fp0, "text-align: center;\n");
    fputs($fp0, "background : Gray\n");
    fputs($fp0, "}\n");
    fputs($fp0, ".ct6cl$clubid    { \n");
    fputs($fp0, "font-family: Arial, sans-serif;\n");
    fputs($fp0, "font-size: 10pt;\n");
    fputs($fp0, "font-weight: bold;\n");
    fputs($fp0, "color: #FFFFFF;\n");
    fputs($fp0, "text-align: center;\n");
    fputs($fp0, "background : Gray\n");
    fputs($fp0, "}\n");
    fputs($fp0, ".ct7cl$clubid    { \n");
    fputs($fp0, "font-family: Arial, sans-serif;\n");
    fputs($fp0, "font-size: 10pt;\n");
    fputs($fp0, "font-weight: bold;\n");
    fputs($fp0, "color: #FFFFFF;\n");
    fputs($fp0, "text-align: center;\n");
    fputs($fp0, "background : Gray\n");
    fputs($fp0, "}\n");
    fputs($fp0, ".preopencourtcl$clubid { \n");
    fputs($fp0, "background-color : #DBDDDD; \n");
    fputs($fp0, "}\n");
    fputs($fp0, ".reportscorecl$clubid {\n");
    fputs($fp0, "background-color : #C6BBC0;\n");
    fputs($fp0, "}\n");
    fputs($fp0, ".reservecourtcl$clubid {\n");
    fputs($fp0, "background-color : #C6BBC0;\n");
    fputs($fp0, "}\n");
    fputs($fp0, ".clubid$clubid" . "th {\n");
    fputs($fp0, "background-color : Gray;\n");
    fputs($fp0, "}\n");
    fputs($fp0, ".reportedcourtcl1 { \n");
    fputs($fp0, "background-color :   #C6BBC0;\n");
    fputs($fp0, "}\n");
    fputs($fp0, ".seekingmatchcl1 { \n");
    fputs($fp0, "background-color :   #C6BBC0;\n");
    fputs($fp0, "}\n");
    fclose($fp0);

    // Copy standard logo
    
    if (!copy($_SESSION["CFG"]["dirroot"] . "/images/0.png", $_SESSION["CFG"]["dirroot"] . "/clubs/" . $clubCode . "/logo.png")) {
        logMessage("Register.registerClub: We ran into a problem copying the default logo");
    }

    // Create index and web_ladder initialization files
    $var = new clubpro_obj;
    $var->siteid = $siteid;
    $var->clubid = $clubid;
    $var->clubcode = $clubCode;
    $var->firstname = $adminFirstName;
    $var->email = $adminEmail;
    $var->username = $adminUser;
    $var->password = $adminPass;
    $var->url = "http://" . $_SESSION["CFG"]["dns"] . "/" . $_SESSION["CFG"]["wwwroot"] . "/clubs/" . $clubCode;

    // Tried to use the read temaplate, but it didn't work right away so
    // now doing the "templating" manually.

    $fp1 = fopen($_SESSION["CFG"]["dirroot"] . "/clubs/$clubCode/web_ladder.php", "w");
    fputs($fp1, "<?\n");
    fputs($fp1, '$clubid' . " = $clubid;\n");
    fputs($fp1, '$siteid' . " = $siteid;\n");
    fputs($fp1, "include(\"../../application.php\");\n");
    fputs($fp1, "include(" . '$_SESSION' . "[\"CFG\"][\"dirroot\"].\"/ladder_content.php\");\n");
    fputs($fp1, "?>");
    fclose($fp1);
    $fp2 = fopen($_SESSION["CFG"]["dirroot"] . "/clubs/$clubCode/index.php", "w");
    fputs($fp2, "<?\n");
    fputs($fp2, '$clubid' . " = $clubid;\n");
    fputs($fp2, '$siteid' . " = $siteid;\n");
    fputs($fp2, "include(\"../../application.php\");\n");
    fputs($fp2, "include(" . '$_SESSION' . "[\"CFG\"][\"dirroot\"].\"/scheduler_content.php\");\n");
    fputs($fp2, "?>");
    fclose($fp2);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: Copied Club Configuration Files...Done");

    //Send out the email
    $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/registration_complete.php", $var);
    $emailbody = nl2br($emailbody);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: Sent out Confirmation Email to ". $var->email);
    
    if (isDebugEnabled(1)) logMessage("register.registerClub: $emailbody");

    // Provide Content
    $content = new clubpro_obj;
    $content->line1 = $emailbody;
    $content->clubname = get_clubname();

    //Use default template
    $subject = get_clubname() . " - Welcome to Sportsynergy";
	$to_email = array(
        $var->email => array(
            'name' => $var->firstname
        )
    );

    //Send the email
    send_email($subject, $to_email, $content, "Registration");
}
?>
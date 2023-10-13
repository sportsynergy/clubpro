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
include ("../application.php");
require_login();
require_priv("2");

//Set the http variables
$userid = $_REQUEST["userid"];
$searchname = $_REQUEST["searchname"];


$DOC_TITLE = "Player Administration";

if ( !isset($userid) ){
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    header("Location: $wwwroot/clubs/" . get_sitecode() . "/index.php?daysahead=$time");
}

// Load up data for view
$registeredSports = load_registered_sports($userid);
$authSites = load_auth_sites($userid);
$availbleSports = load_avail_sports();
$availableSites = load_avail_sites();
$extraParametersResult = load_site_parameters();

/* form has been submitted, check if it the user login information is correct */

if (isset($_POST['formname']) && $_POST['formname'] == "photoform") {
    $frm = $_POST;

    if(!empty($_FILES["image"]["name"])) { 
       

        $fileName = basename($_FILES["image"]["name"]); 
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 

        if (isDebugEnabled(1)) logMessage("change_settings: photo size is: $fileName");


        $image_info = getimagesize($_FILES["image"]["tmp_name"]);
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        if (isDebugEnabled(1)) logMessage("change_settings: photo size is: $image_width and $image_height");

        if($image_width <= 180 && $image_height <= 180 ){
            // Allow certain file formats 
            $allowTypes = array('jpg','png','jpeg','gif'); 
            if(in_array($fileType, $allowTypes)){ 
                $image = $_FILES['image']['tmp_name']; 
                $imgContent = addslashes(file_get_contents($image)); 
                // Insert image content into database 
                $query = "UPDATE tblUsers set photo = '$imgContent' WHERE userid = $userid";
                $result = db_query($query);
                $noticemsg = "Your profile was saved.  Good Job!<br/><br/>"; 
            }
        } else {
            $errormsg = "This photo is too big, please resize to 180 x 180"; 
        }
        

    }
} 


if (isset($_POST['formname']) && $_POST['formname'] == "entryform") {
    $frm = $_POST;

    // Do a special check for duplicate email addresses.
    if (isset($userid)) {
        $useridstring = "?userid=$userid";
    }
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    $backtopage = "$wwwroot/admin/change_settings.php$useridstring";
    $errormsg = validate_form($frm, $errors);
    
    if (empty($errormsg)) {
        
        update_settings($frm, $availableSites, $availbleSports, $extraParametersResult);

        //Refresh the data
        $registeredSports = load_registered_sports($userid);
        $authSites = load_auth_sites($userid);

        // Reset these pointers
        
        if (mysqli_num_rows($extraParametersResult) > 0) {
            mysqli_data_seek($extraParametersResult, 0);
        }
        
        if (mysqli_num_rows($availbleSports) > 0) {
            mysqli_data_seek($availbleSports, 0);
        }
        
        if (mysqli_num_rows($availableSites) > 0) {
            mysqli_data_seek($availableSites, 0);
        }

        //Save email address
        $query = "UPDATE tblUsers set email = '" . $frm['email'] . "' where userid = " . $frm['userid'];
        db_query($query);

        // Display this, their email validates
        $noticemsg = "Your profile was saved.  Good Job!<br/><br/>";
    }
}
$frm = load_user_profile($userid);
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/change_settings_admin_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the forgot password form, and return the error messages in a string.
     * if the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";

    // Make sure that the login id is set
    
    if (isSiteAutoLogin() && empty($frm["memberid"])) {
        $errors->memberid = true;
        $msg.= "You did not specify a member id";
    } elseif (!isSiteAutoLogin() && empty($frm["username"])) {
        $errors->username = true;
        $msg.= "You did not specify a username";
    } elseif (!isSiteAutoLogin() && username_already_exists($frm["username"], $frm["userid"])) {
        $errors->username = true;
        $msg.= "The username <b>" . ov($frm["username"]) . "</b> already exists";
    } elseif (!empty($frm["email"]) && !is_email_valid($frm["email"])) {
        $errors->email = true;
        $msg.= "Please enter a valid email address";
    } elseif (empty($frm["firstname"])) {
        $errors->firstname = true;
        $msg.= "You did not specify a first name";
    } elseif (empty($frm["lastname"])) {
        $errors->lastname = true;
        $msg.= "You did not specify a last name";
    } elseif (!empty($frm["email"])) {
        $otherUser = verifyEmailUniqueAtClub($frm["email"], $frm["userid"], get_clubid());
        
		if($frm["roleid"]=="6") return;

        if (isset($otherUser)) {
            $errors->email = true;
            $msg.= "The email address <b>" . ov($frm["email"]) . "</b> already exists";
        }
    }
    return $msg;
}
/**
 *
 * @param $frm
 * @param $availableSites
 * @param $availbleSports
 * @param $extraParametersResult
 */
function update_settings(&$frm, $availableSites, $availbleSports, $extraParametersResult) {
    
    if (isDebugEnabled(1)) logMessage("Updating the account for: " . $frm['userid']);

    //If userid is not set this is being run by a player who is updating
    //their own accoutn information.  If this is the case we will get the userid

    //out of the session.

    
    if (!$frm['userid']) {
        $userid = get_userid();
    } else {
        $userid = $frm['userid'];
        $mycourtTypes = explode(",", $frm['mycourttypes']);
        $mySites = explode(",", $frm['mysites']);

        //if the courttype post var is set we need to either update or insert depending on if it was set before.

        //Now set the sites

        for ($i = 0; $i < mysqli_num_rows($availbleSports); ++$i) {
            $courtTypeArray = mysqli_fetch_array($availbleSports);
            
            if ($frm["courttype$courtTypeArray[courttypeid]"]) {
                
                if (in_array($courtTypeArray['courttypeid'], $mycourtTypes)) {
                    $query = "UPDATE `tblUserRankings` SET ranking = '" . $frm["courttype$courtTypeArray[courttypeid]"] . "' WHERE courttypeid='$courtTypeArray[courttypeid]' AND userid ='$userid' AND usertype ='0'";
                    
                db_query($query);

                
                } else {
                    $query = "INSERT INTO `tblUserRankings`
                                        (`userid` , `courttypeid` , `ranking` , `hot` , `usertype`  )
                                        VALUES ('$userid', '$courtTypeArray[courttypeid]', '" . $frm["courttype$courtTypeArray[courttypeid]"] . "', '0', '0')";
                    db_query($query);
                }
            }

            //if courttype post var is not set and it was set before we will delete it.
            else {
                db_query("DELETE from `tblUserRankings`WHERE userid=$userid AND courttypeid='$courtTypeArray[courttypeid]' AND usertype=0");
            }
        }

        //Now set the sites
        for ($i = 0; $i < mysqli_num_rows($availableSites); ++$i) {
            
            if (isDebugEnabled(1)) logMessage("Going through the available sites");
            $siteArray = mysqli_fetch_array($availableSites);
            
            if ($frm["clubsite$siteArray[siteid]"]) {
                
                if (isDebugEnabled(1)) logMessage("Checking on Site " . $siteArray[siteid]);

                //If the site isn't in the list insert it
                
                if (!in_array($siteArray['siteid'], $mySites)) {
                    
                    if (isDebugEnabled(1)) logMessage("Adding a new site autorization " . $siteArray['siteid']);
                    $addSiteQuery = "INSERT INTO `tblkupSiteAuth` ( `userid` , `siteid` ) VALUES ($userid, " . $siteArray['siteid'] . ")";
                    db_query($addSiteQuery);
                }
            }

            //if siteid post var is not set and it was set before we will delete it.
            else {
                
                if (isDebugEnabled(1)) logMessage("Deleting a site autorization " . $siteArray['siteid'] . " and user: $userid");
                $removeSiteQuery = "DELETE from tblkupSiteAuth WHERE userid=$userid AND siteid=" . $siteArray['siteid'];
                db_query($removeSiteQuery);
            }
        }
    }

    //Set the enable variable
    if (isset($frm['enable'])) {
        $enable = 'y';
    } else {
        $enable = 'n';
    }

    
    if (isSiteAutoLogin()) {
        $username = $frm['memberid'];
    } else {
        $username = $frm['username'];
    }
    
    if (get_magic_quotes_gpc()) {
        $firstName = $frm['firstname'];
        $lastName = $frm['lastname'];
    } else {
        $firstName = addslashes($frm['firstname']);
        $lastName = addslashes($frm['lastname']);
    }
    $updateUserQuery = "
        UPDATE tblUsers SET
				username = '$username'
                ,email = '$frm[email]'
                ,firstname = '$firstName'
                ,lastname = '$lastName'
                ,homephone = '$frm[homephone]'
                ,workphone = '$frm[workphone]'
                ,cellphone = '$frm[cellphone]'
                ,pager = '$frm[pager]'
                ,useraddress = '$frm[useraddress]'
		        ,gender = '$frm[gender]'
        WHERE userid = '$userid'";
    $qid = db_query($updateUserQuery);
    $updateClubUserQuery = "
        UPDATE tblClubUser SET
                recemail = '$frm[recemail]'
                ,enable = '$enable'
                ,memberid = '$frm[memberid]'
				,roleid 	  =  '$frm[roleid]'
				,msince  =  '$frm[msince]'
        WHERE userid = '$userid'";
    $qid = db_query($updateClubUserQuery);

    //Set the password
    
    if (!empty($frm["password"])) {
        $updateUserQuery = "UPDATE tblUsers SET password = '" . md5($frm["password"]) . "' WHERE userid = '$userid'";
        $qid = db_query($updateUserQuery);
    }

    // Update the Custom Parameters
    

    while ($parameterArray = mysqli_fetch_array($extraParametersResult)) {
        
        $parameterId = $parameterArray['parameterid'];
        
        if (isDebugEnabled(1)) logMessage("Updating the custom parameters $parameterId"  );
        
        if ($frm["parameter-$parameterId"]) {
            
            if (isDebugEnabled(1)) logMessage("(admin)change_settings.update_settings: adding custom parameter:  " . $frm["parameter-$parameterId"]);
            
            $parameterValue = $frm["parameter-$parameterId"];
            
            $query = "INSERT INTO tblParameterValue (userid,parameterid,parametervalue) 
                			VALUES ('$userid', '$parameterId','$parameterValue') 
                			ON DUPLICATE KEY UPDATE parametervalue = '$parameterValue'";
            
            if (isDebugEnabled(1)) logMessage($query);
            
            db_query($query);
        }
    }
    unset($userid);
}
?>
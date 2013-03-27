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
* - password_valid()
* - update_password()
* Classes list:
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

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST)) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    $backtopage = $_SESSION["CFG"]["wwwroot"] . "/users/change_password.php";
    
    if (empty($errormsg)) {
        update_password($frm["newpassword"]);
        $noticemsg = "Your password was updated.";
    }
}
$DOC_TITLE = "Change Password";
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/change_password_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the forgot password form, and return the error messages in a string.
     * if the string is empty, then there are no errors */
    $errors = new Object;
    $msg = "";
    
    if (empty($frm["oldpassword"])) {
        $errors->oldpassword = true;
        $msg.= "You did not specify your old password";
    } elseif (!password_valid($frm["oldpassword"])) {
        $errors->oldpassword = true;
        $msg.= "Your old password is invalid";
    } elseif (empty($frm["newpassword"])) {
        $errors->newpassword = true;
        $msg.= "You did not specify your new password";
    } elseif (empty($frm["newpassword2"])) {
        $errors->newpassword2 = true;
        $msg.= "You did not confirm your new password";
    } elseif ($frm["newpassword"] != $frm["newpassword2"]) {
        $errors->newpassword = true;
        $errors->newpassword2 = true;
        $msg.= "Your new passwords do not match";
    }
    return $msg;
}

function password_valid($password) {
    /* return true if the user's password is valid */
    $username = $_SESSION["user"]["username"];
    $password = md5($password);
    $qid = db_query("SELECT 1 FROM tblUsers WHERE username = '$username' AND password = '$password' AND tblUsers.enddate IS NULL");
    return db_num_rows($qid);
}

function update_password($newpassword) {
    /* set the user's password to the new one */
    $username = $_SESSION["user"]["username"];
    $newpassword = md5($newpassword);
    $qid = db_query("UPDATE tblUsers SET password = '$newpassword' WHERE username = '$username'");
}
?>
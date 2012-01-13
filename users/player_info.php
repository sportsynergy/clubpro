<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
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
* Classes list:
*/
/*
 * $LastChangedRevision: 857 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:08:03 -0500 (Mon, 14 Mar 2011) $

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
    $errors = new Object;
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
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
* - insert_buddy()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/reservationlib.php");
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
    
    if (empty($errormsg)) {
        
        if (isDebugEnabled(1)) logMessage("my_buddylist: inserting buddy");
        insert_buddy($frm);
    } else {
        
        if (isDebugEnabled(1)) logMessage("my_buddylist: there was a problem adding the buddy becuase of $errormsg");
    }
}

if (isDebugEnabled(1)) logMessage("my_buddylist");
$DOC_TITLE = "My Buddy List";
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/my_buddylist_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the forgot password form, and return the error messages in a string.
     * if the string is empty, then there are no errors */
    $errors = new Object;
    $msg = "";
    
    if (empty($frm["buddy"])) {
        $errors->buddy = true;
        $msg.= "You did not specify a buddy.";
    } else 
    if (isABuddyOfMine($frm["buddy"])) {
        $errors->buddy = true;
        $msg.= "I am sorry but this person is already a buddy.";
    }
    return $msg;
}
function insert_buddy(&$frm) {

    /* add the new user into the database */
    
    if (isDebugEnabled(1)) {
		logMessage("my_buddylist: insert_buddy" . $frm['buddy'] . " for user " . get_userid());
	}
    $query = "INSERT INTO tblBuddies (
                userid, buddyid
                ) VALUES (
                          '" . get_userid() . "'
                          ,'$frm[buddy]')";

    // run the query on the database
    $result = db_query($query);
}
?>
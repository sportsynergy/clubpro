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
* - insert_court_event()
* Classes list:
*/
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
        $wwwroot = $_SESSION["CFG"]["wwwroot"];
        header("Location: $wwwroot/admin/policy_preferences.php#court_events");
    }
} elseif (isset($_POST['back'])) {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    header("Location: $wwwroot/admin/policy_preferences.php");
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/add_court_event_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

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
    
    if (get_magic_quotes_gpc()) {
        $name = stripslashes($frm['name']);
    } else {
        $name = addslashes($frm['name']);
    }

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
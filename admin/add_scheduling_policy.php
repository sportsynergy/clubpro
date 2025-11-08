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
* - get_sitecourts_dropdown()
* - get_dow_dropdown()
* - get_scheduling_policy_types()
* - insert_hours_policy()
* Classes list:
*/
include ("../application.php");
require_login();
require_priv("2");

// Include jQuery
define("_JQUERY_", true);


$DOC_TITLE = "Scheduling Policy Setup";
$buttonLabel = "Add Scheduling Policy";
$policyid = $_REQUEST["policyid"];

//If a policy id was passed in, then load it up.

if (!empty($policyid)) {
    $schedulePolicy = load_reservation_policy($policyid);
    $buttonLabel = "Update Scheduling Policy";
}

/* form has been submitted, try to create the new role */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    
    if (empty($errormsg)) {
        insert_hours_policy($frm);
        header("Location: $wwwroot/admin/policy_preferences.php#schedule");
        die;
    }
} elseif (isset($_POST['back'])) {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    header("Location: $wwwroot/admin/policy_preferences.php");
}
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/add_scheduling_policy_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {
    $errors = new clubpro_obj;
    $msg = "";
    
    if (isset($frm["starttime"])) {
        $startTimeArray = explode(":", $frm["starttime"]);
    }
    
    if (isset($frm["endtime"])) {
        $endTimeArray = explode(":", $frm["endtime"]);
    }

    //Make sure that they selected everything
    
    if (empty($frm["name"])) {
        $msg.= "You did not specify a policy name.";
        $errors->name = true;
    } elseif (empty($frm["courtid"])) {
        $msg.= "You did not specify a court.";
        $errors->courtid = true;
    } elseif ($frm["dow"] == "") {
        $msg.= "You did not specify a day of the week.";
        $errors->dow = true;
    } elseif (!empty($frm["reservationwindow"]) && empty($frm["starttime"])) {
        $msg.= "You did not specify a start time.";
        $errors->starttime = true;
    } elseif (!empty($frm["reservationwindow"]) && empty($frm["endtime"])) {
        $msg.= "You did not specify an end time.";
        $errors->endtime = true;
    }

    //Validate that the start is before end
    elseif (isset($startTimeArray) && isset($endTimeArray) && $startTimeArray[0] > $endTimeArray[0]) {
        $msg.= "The start time needs to occur before the end time.";
        $errors->startime = true;
    }
    return $msg;
}
function get_sitecourts_dropdown($siteid) {
    $query = "SELECT courtid, courtname
                   FROM tblCourts
                   WHERE siteid = $siteid
                   AND enable =1";
    return db_query($query);
}
function get_dow_dropdown() {
    $query = "SELECT dayid, name
                   FROM tblDays";
    return db_query($query);
}
function get_scheduling_policy_types() {
    $query = "SELECT id, policytypename
                   FROM tblSchedulingPolicyType ";
    return db_query($query);
}
function insert_hours_policy(&$frm) {

    /* add the new user into the database */

    // Strip Slashes
    $description = addslashes($frm['description']);
    
    if ($frm['courtid'] == "all") {
        $courtid = "NULL";
    } else {
        $courtid = $frm['courtid'];
    }
    
    if ($frm['dow'] == "all") {
        $dayid = "NULL";
    } else {
        $dayid = $frm['dow'];
    }
    
    if ($frm['allowlooking'] == "yes") {
        $allowlooking = 'y';
    } else {
        $allowlooking = 'n';
    }

    // Back to Back
    
    if ($frm['back2back'] == "yes") {
        $allowback2back = 'y';
    } else {
        $allowback2back = 'n';
    }
    
    if (!isset($frm['reservationwindow'])) {
        $starttime = "NULL";
        $endtime = "NULL";
    } else {
        $starttime = "'$frm[starttime]'";
        $endtime = "'$frm[endtime]'";
    }

    //If this is the case, then we're updating an existing policy
    
    if (!empty($frm['policyid'])) {
        
        if (isDebugEnabled(1)) logMessage("add_scheduling_policy.insert_hours_policy: Updating scheduling policy " . $frm['policyid']);
        $query = "UPDATE tblSchedulingPolicy SET
				policyname = '$frm[name]'
                ,description = '$description'
                ,schedulelimit = '$frm[limit]'
                ,dayid = $dayid
                ,courtid = $courtid
                ,siteid = " . get_siteid() . "
                ,allowlooking = '$allowlooking'
                 ,allowback2back = '$allowback2back'
                ,starttime = $starttime
                ,endtime = $endtime
        WHERE policyid = '$frm[policyid]'";
    } else {
        
        if (isDebugEnabled(1)) logMessage("add_scheduling_policy.insert_hours_policy: Adding new scheduling policy ");
        $query = "INSERT INTO tblSchedulingPolicy (
	                policyname, description,schedulelimit,dayid,courtid,siteid,allowlooking,allowback2back,starttime,endtime
	                ) VALUES (
	                           '$frm[name]'
	                          ,'$frm[description]'
	                          ,$frm[limit]
	                          ,$dayid
	                          ,$courtid
	                          ," . get_siteid() . "
	                          ,'$allowlooking'
	                          ,'$allowback2back'
	                          ,$starttime
	                          ,$endtime)";
    }

    // run the query on the database
    $result = db_query($query);
}
?>
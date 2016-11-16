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
* - saveClubEvent()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/clubadminlib.php");
$DOC_TITLE = "Club Event Setup";
require_priv("2");
$eventid = $_REQUEST["clubeventid"];

if (isset($eventid)) {
    $clubEventResult = loadClubEvent($eventid);
    $clubEventArray = mysqli_fetch_array($clubEventResult);
    $frm = $clubEventArray;
}

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    
    if (empty($errormsg)) {
        saveClubEvent($frm);
        header("Location: $wwwroot/admin/club_events.php");
        die;
    }
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/add_club_event_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {
    $errors = new Object;
    $msg = "";
    
    if (empty($frm["name"])) {
        $errors->subject = true;
        $msg.= "You did not specify an event name";
        return $msg;
    } else 
    if (empty($frm["eventdate"])) {
        $errors->eventdate = true;
        $msg.= "You did not specify an event date";
        return $msg;
    } elseif (empty($frm["description"])) {
        $errors->description = true;
        $msg.= "You did not specify an event description";
        return $msg;
    }

    // Make sure date is ok
    $datesArray = explode("/", $frm["eventdate"]);
    $month = $datesArray[2];
    $day = $datesArray[1];
    $year = $datesArray[0];
    $gmttime = mktime();
    $thisyear = date("Y", $gmttime);
    
    if (count($datesArray) != 3) {
        logMessage("This is the number of dates elements: " . count($datesArray));
        $errors->eventdate = true;
        $msg.= "The date is not properly formatted";
        return $msg;
    } elseif (!is_numeric($month) && $month > 12) {
        $errors->eventdate = true;
        $msg.= "The month is not properly formatted";
        return $msg;
    } elseif (!is_numeric($day) && $day > 31) {
        $errors->eventdate = true;
        $msg.= "The day is not properly formatted";
        return $msg;
    } elseif (!is_numeric($year) && ($year > $thisyear + 2 || $year < $thisyear - 1)) {
        $errors->eventdate = true;
        $msg.= "The year is not properly formatted";
        return $msg;
    }
}
/**
 * Save Club Events
 * @param  $frm
 */
function saveClubEvent(&$frm) {
    logMessage("add_club_event.saveClubEvent");

    // Parse
    $datearray = explode("/", $frm['eventdate']);
    $month = $datearray[0];
    $day = $datearray[1];
    $year = $datearray[2];
    $mysqldateformat = $year . "-" . $month . "-" . $day;
    $eventid = $frm['id'];
    

    // Strip Slashes
    
    if (get_magic_quotes_gpc()) {
        $subject = stripslashes($frm['name']);
        $description = stripslashes($frm['description']);
    } else {
        $subject = addslashes($frm['name']);
        $description = addslashes($frm['description']);
    }

    logMessage("add_club_event.saveClubEvent: this is the date $description");

    //Insert the Club Event
    
    if (!empty($eventid)) {
        logMessage("add_club_event.saveClubEvent: updating club event $eventid ");
        $query = "
		        UPDATE tblClubEvents SET
						name = '$subject'
		                ,eventdate = '$mysqldateformat'
		                ,description = '$description'
		                ,lastmodifier = " . get_userid() . "
		        WHERE id = '$eventid'";
    } else {
        logMessage("add_club_event.saveClubEvent: adding new club event ");
        $query = "INSERT INTO tblClubEvents (
                name, clubid, eventdate, description, creator, lastmodifier, eventenddate
                ) VALUES (
                          '$subject'
					  	  ," . get_clubid() . "
                          ,'$mysqldateformat'
                          ,'$description'
                          ," . get_userid() . "
                          ," . get_userid() . "
                          ,null
                          )";
    }
    $result = db_query($query);
}
?>
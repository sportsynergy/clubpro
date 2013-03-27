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
* Classes list:
*/

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/clubadminlib.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

$DOC_TITLE = "Club Event";

//Load in Date
$eventid = $_REQUEST["clubeventid"];

//put this in session

if (isset($eventid)) {
    $_SESSION["clubeventid"] = $eventid;
}

if (isDebugEnabled(1)) logMessage("calling club event");

if (match_referer() && isset($_POST['cmd'])) {
    $frm = $_POST;

    // Add user to Club Event
    
    if ($frm['cmd'] == 'addtoevent') {
        $userid = $frm['userid'];
        $clubeventid = $frm['clubeventid'];
        addToClubEvent($userid, $clubeventid);
    }

    // Remove user from Club Event
    
    if ($frm['cmd'] == 'removefromevent') {
        $userid = $frm['userid'];
        $clubeventid = $frm['clubeventid'];
        removeFromClubEvent($userid, $clubeventid);
    }
}
$clubEventResult = loadClubEvent($_SESSION["clubeventid"]);
$clubEventParticipants = getClubEventParticipants($_SESSION["clubeventid"]);
$alreadySignedUp = isClubEventParticipant($clubEventParticipants);
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/club_event_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
?>
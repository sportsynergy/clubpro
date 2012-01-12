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
* - removeClubEvent()
* - loadClubEvents()
* Classes list:
*/
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/
include ("../application.php");
$DOC_TITLE = "Club Events";
require_priv("2");

if (isset($_POST['clubeventid']) && isset($_POST['cmd'])) {
    
    if ($_POST['cmd'] == "remove") {
        removeClubEvent($_POST['clubeventid']);
    }
}

if (match_referer() && isset($_POST['submit'])) {

    //Do Something
    
}
$clubEvents = loadClubEvents(get_clubid());
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/club_events_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($searchname) {
}
function removeClubEvent($eventid) {
    logMessage("club_events.removeClubEvent: removed $eventid");
    $query = "Update tblClubEvents events
				SET events.enddate = NOW() 
				WHERE events.id = $eventid";
    return db_query($query);
}
/**
 *
 * @param  $clubId
 */
function loadClubEvents($clubid) {
    $query = "SELECT * from tblClubEvents events
				WHERE events.clubid = $clubid and events.enddate is null ORDER BY events.eventdate";
    return db_query($query);
}

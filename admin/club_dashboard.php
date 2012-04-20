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
* - countClubMembers()
* - countSiteMembers()
* - loadClubSites()
* - countSiteReservations()
* Classes list:
*/

include ("../application.php");
require_login();
$DOC_TITLE = "Club Dashboard";
require_priv("3");
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/club_dashboard_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

/**
 * Return the number of members for a site.
 */
function countSiteMembers($siteid) {
    $getMemberCountQuery = "SELECT count(*) FROM tblkupSiteAuth auth, tblUsers users WHERE auth.siteid=$siteid 
			AND users.userid = auth.userid
			AND users.enddate IS NOT NULL";
			
    $getAllClubSitesResult = db_query($getMemberCountQuery);
    return mysql_result($getAllClubSitesResult, 0);
}
/**
 *  Loads Clubsites
 */
function loadClubSites($clubid) {
    $getAllClubSitesQuery = "SELECT * from  tblClubSites WHERE clubid = $clubid AND enable = 'y'";
    $getAllClubSitesResult = db_query($getAllClubSitesQuery);
    return $getAllClubSitesResult;
}
/**
 * Returns the number of reservations made for a site.
 */
function countSiteReservations($siteid) {
    $getSiteReservationsQuery = "SELECT count(*) " . "FROM tblReservations reservations, tblCourts courts " . "WHERE reservations.courtid = courts.courtid " . "AND courts.siteid = $siteid";
    $getSiteReservationsResult = db_query($getSiteReservationsQuery);
    return mysql_result($getSiteReservationsResult, 0);
}
?>
<?php

/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $
 */

include("../application.php");
require_login();
$DOC_TITLE = "Club Dashboard";



include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/club_dashboard_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");



/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

/**
 *  Return the number of members for a club
 */
function countClubMembers($clubid) {

	$getMemberCountQuery = "SELECT count(*) from  tblUsers WHERE clubid = $clubid";
	$getAllClubSitesResult = db_query($getMemberCountQuery);
	
	return mysql_result($getAllClubSitesResult, 0);
}

/**
 * Return the number of members for a site.
 */
function countSiteMembers($siteid) {
	
	$getMemberCountQuery = "SELECT count(auth.userid) FROM tblkupSiteAuth auth WHERE auth.siteid=$siteid";
	$getAllClubSitesResult = db_query($getMemberCountQuery);
	
	return mysql_result($getAllClubSitesResult, 0);
}

/**
 *  Loads Clubsites
 */

function loadClubSites($clubid) {

	$getAllClubSitesQuery = "SELECT * from  tblClubSites WHERE clubid = $clubid";
	$getAllClubSitesResult = db_query($getAllClubSitesQuery);

	return $getAllClubSitesResult;

}

/**
 * Returns the number of reservations made for a site.
 */

function countSiteReservations($siteid){
	
	$getSiteReservationsQuery = "SELECT count(*) " .
								"FROM tblReservations reservations, tblCourts courts " .
								"WHERE reservations.courtid = courts.courtid " .
								"AND courts.siteid = $siteid";
							
	$getSiteReservationsResult = db_query($getSiteReservationsQuery);
	
	return mysql_result($getSiteReservationsResult,0);
}

?>
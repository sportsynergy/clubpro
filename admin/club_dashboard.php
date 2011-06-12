<?php

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */

include("../application.php");
require_login();
$DOC_TITLE = "Club Dashboard";
require_priv("3");


include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/club_dashboard_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");



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
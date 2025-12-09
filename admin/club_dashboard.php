<?php


include ("../application.php");
require_login();
$DOC_TITLE = "Club Dashboard";
require_priv("3");

include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/club_dashboard_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

/**
 * Return the number of members for a site.
 */
function countSiteMembers($siteid) {
    $getMemberCountQuery = "SELECT count(*) FROM tblkupSiteAuth auth, tblUsers users WHERE auth.siteid=$siteid 
			AND users.userid = auth.userid
			AND users.enddate IS NULL";
			
    $getAllClubSitesResult = db_query($getMemberCountQuery);
    $sitearray = mysqli_fetch_array($getAllClubSitesResult);
    $count = $sitearray[0];

    return $count;
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
    $count = mysqli_fetch_array($getSiteReservationsResult);

    return $count[0];
}
?>
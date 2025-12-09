<?php



include ("../application.php");
require_login();

$_SESSION["selected_site"] = $_REQUEST["siteid"];
$siteid = $_REQUEST["siteid"];

$sitedetail = getSiteDetail($siteid);
$sitecourts = getSiteCourts($siteid);


$DOC_TITLE = "Site Info";
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/site_info_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function getSiteCourts($siteid){
	
	$query = "SELECT * from tblCourts where siteid = $siteid";
	return db_query($query);
}

function getSiteDetail($siteid){
	
	$query = "SELECT * from tblClubSites where siteid = '$siteid' ";
	$result = db_query($query);
	return mysqli_fetch_array($result);
}

?>
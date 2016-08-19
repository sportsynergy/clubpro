<?php

/**
* Class and Function List:
* Function list:
* Classes list:
*/
include ("../application.php");

// set some variables from the request
$start = $_REQUEST["start"];
$siteid = $_REQUEST["siteid"];

if (isDebugEnabled(1)) logMessage("getting recent activity for siteid $siteid and start $start");
header("content-type: application/json");

if (isset($start) && isset($siteid)) {
    $result = getRecentSiteActivityBlock($siteid, $start);
    $all_data = array();
    while ($recentActivity = db_fetch_object($result)) {
        $all_data[] = $recentActivity;
    }
    echo json_encode($all_data);
}
?>
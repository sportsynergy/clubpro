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
* - wrapWithDoubleQuotes()
* Classes list:
*/
include ("../application.php");
$searchname = $_REQUEST['searchname'];
$extraParametersResult = load_site_parameters();
$playerresult = get_admin_player_search($searchname);
header('Content-type: text/csv');
header('Content-disposition: attachment;filename=bookings.csv');

// get site parameters
$csvHeaderArray = array();

// Start with the headers
array_push($csvHeaderArray, "Court");
array_push($csvHeaderArray, "Time");
array_push($csvHeaderArray, "Date");

//Get General Club info
$clubquery = "SELECT timezone from tblClubs WHERE clubid=" . get_clubid() . "";
$clubresult = db_query($clubquery);
$clubobj = db_fetch_object($clubresult);
$tzdelta = $clubobj->timezone * 3600;
$curtime = time() + $tzdelta;
$simtzdelta = $clubobj->timezone;


//Print the Headers
print implode(",", $csvHeaderArray);
print "\n";

$ayearago = "31536000";


$query  = "SELECT reservations.time, tblCourts.courtname
FROM tblReservations reservations
INNER JOIN tblCourts ON reservations.courtid = tblCourts.courtid
WHERE reservations.courtid in (SELECT courtid from tblCourts where siteid = ".get_siteid()." )
AND reservations.time< $curtime
AND reservations.time  > $curtime - $ayearago
AND reservations.usertype=0
AND reservations.enddate IS NULL
ORDER BY reservations.time";

$result = db_query($query);

// Print the Data
while ($row = mysqli_fetch_array($result)) {
    $csvDataArray = array();
    $time = gmdate("g:i a", $row['time'] );
    $date = gmdate("l F j, Y", $row['time'] );
    
    array_push($csvDataArray, wrapWithDoubleQuotes($row['courtname']));
    array_push($csvDataArray, wrapWithDoubleQuotes($time));
    array_push($csvDataArray, wrapWithDoubleQuotes($date));
    
    print implode(",", $csvDataArray);
    print "\n";
}
/**
 *
 * @param unknown_type $string
 */
function wrapWithDoubleQuotes($string) {
    return '"' . trim($string, '"') . '"';
}
?>
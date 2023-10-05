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
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");

$ladderid = $_REQUEST['ladderid'];

header('Content-type: text/csv');
header('Content-disposition: attachment;filename=bookings.csv');

// get site parameters
$csvHeaderArray = array();

// Start with the headers
array_push($csvHeaderArray, "Date");
array_push($csvHeaderArray, "Winner");
array_push($csvHeaderArray, "Loser");
array_push($csvHeaderArray, "Score");


//Print the Headers
print implode(",", $csvHeaderArray);
print "\n";


$ladderMatchResult = getLadderMatches($ladderid, 1000000 );

// Print the Data
while ($row = mysqli_fetch_array($ladderMatchResult)) {

    $csvDataArray = array();
    $challengeDate = explode(" ",$row['match_time']);
    $winner =  $row['winner_first']." ". $row['winner_last'];
    $loser =  $row['loser_first']." ". $row['loser_last'];
    $score = $row['score'];

    array_push($csvDataArray, wrapWithDoubleQuotes( formatDateStringSimple( $challengeDate[0] )));
    array_push($csvDataArray, wrapWithDoubleQuotes($winner));
    array_push($csvDataArray, wrapWithDoubleQuotes($loser));
    array_push($csvDataArray, wrapWithDoubleQuotes($score));
    
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
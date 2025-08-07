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
header('Content-disposition: attachment;filename=ladder_export.csv');

// get site parameters
$csvHeaderArray = array();

// Start with the headers
array_push($csvHeaderArray, "Name");
array_push($csvHeaderArray, "Ranking");
array_push($csvHeaderArray, "Rating");
array_push($csvHeaderArray, "Email");
array_push($csvHeaderArray, "Mobile Phone");


//Print the Headers
print implode(",", $csvHeaderArray);
print "\n";


$ladderMatchResult = getLadder($ladderid );

// Print the Data
while ($row = mysqli_fetch_array($ladderMatchResult)) {

    $csvDataArray = array();
    $name =  $row['fullname'];
    $ladderposition =  $row['ladderposition'];
    $ranking = $row['ranking'];
    $email = $row['email'];
    $cellphone = $row['email'];

    array_push($csvDataArray, wrapWithDoubleQuotes( $name));
    array_push($csvDataArray, wrapWithDoubleQuotes($ladderposition));
    array_push($csvDataArray, wrapWithDoubleQuotes($ranking));
    array_push($csvDataArray, wrapWithDoubleQuotes($email));
    array_push($csvDataArray, wrapWithDoubleQuotes($cellphone));
    
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
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
* Classes list:
*/
/*
 * Created on May 28, 2007
 *
*/
include ("../application.php");
$player1 = $_REQUEST['player1'];
$player2 = $_REQUEST['player2'];
$courttype = $_REQUEST['courttype'];

if (!isset($player1) || !isset($player2) || !isset($courttype)) {
    print "Please specify player1 and player2 and courttype";
    die;
}

// ********* Basically Copied from getTeamIDForCurrentUser
//find teams for current user

$currentuserteamquery = "SELECT teamdetails.teamid
	                                  FROM tblTeams teams, tblkpTeams teamdetails
	                                  WHERE teams.teamid = teamdetails.teamid
	                                  AND teamdetails.userid=$player1";

// run the query on the database
$currentuserteamresult = db_query($currentuserteamquery);

//Build an single dimensional array for current user teams
$currentUserStack = array();
while ($currentuserteamarray = mysql_fetch_array($currentuserteamresult)) {
    array_push($currentUserStack, $currentuserteamarray['teamid']);
}

//find teams for current users partner
$currentuserpartnerteamquery = "SELECT teamdetails.teamid
	                                         FROM tblTeams teams, tblkpTeams teamdetails
	                                         WHERE teams.teamid = teamdetails.teamid
	                                         AND teamdetails.userid=$player2";

// run the query on the database
$currentuserpartnerteamresult = db_query($currentuserpartnerteamquery);

//Build an single dimensional array for current users partners teams
$currentUserPartnerStack = array();
while ($currentuserpartnerteamarray = mysql_fetch_array($currentuserpartnerteamresult)) {
    array_push($currentUserPartnerStack, $currentuserpartnerteamarray['teamid']);
}
$teamexistsarray = array_intersect($currentUserStack, $currentUserPartnerStack);

//print "This is my teamarray: $teamexistsarray[0]";

if (count($teamexistsarray) == 1) {

    //found  a team
    $teamid = current($teamexistsarray);
    print "This is the team: $teamid";
} elseif (count($teamexistsarray) > 1) {
    print "Guys are on more than one team";
} else {
    print "Guys are not teams";
}
?>

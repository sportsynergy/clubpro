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
 * $LastChangedRevision: 823 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-06 22:50:11 -0600 (Sun, 06 Feb 2011) $

*/
?>

<div>
  <?
$challengeMatchResult = getMatchesByType( get_siteid(), 2, 10 );

if(mysql_num_rows($challengeMatchResult) > 0){ ?>
  <h2 style="padding-top: 15px">Recent Challenge Matches</h2>
  <hr class="hrline"/>
  <ul class="ladderactivity">
    <?
	
while($challengeMatch = mysql_fetch_array($challengeMatchResult)){ 

	$playerQuery = "SELECT users.firstname, users.lastname, reservationdetails.outcome, users.userid
						FROM tblkpUserReservations reservationdetails, tblUsers users
						WHERE reservationdetails.reservationid = '$challengeMatch[reservationid]'
						AND users.userid = reservationdetails.userid 
						ORDER BY reservationdetails.outcome DESC";

	$playerResult = db_query($playerQuery);
	
		// only display full reservations
		if( mysql_num_rows($playerResult)==2){ 
	
			$playerArray = mysql_fetch_array($playerResult);
			$scored = $playerArray['outcome'] > 0 ? true : false;
			
			$playerOne = "$playerArray[firstname] $playerArray[lastname]";
			$userid1 = $playerArray['userid'];
			
			$playerArray = mysql_fetch_array($playerResult);
			$playerTwo = "$playerArray[firstname] $playerArray[lastname]";
			$userid2 = $playerArray['userid'];
			$loserscore = $playerArray['outcome'];
			  
			$inreservation = get_userid() == $userid1 || get_userid() == $userid2 ? true : false;
			
			printLadderEvent($playerOne, $playerTwo, $challengeMatch['courtname'], $challengeMatch['time'], $challengeMatch['reservationid'], $scored, $loserscore, $inreservation )
			?>
    <? } ?>
  </ul>
  <? }} ?>
</div>

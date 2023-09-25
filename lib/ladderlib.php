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
* - adjustDoublesClubLadder()
* - moveLadderGroup()
* - moveDoublesLadderGroup()
* - adjustClubLadder()
* - moveEveryOneInClubLadderUp()
* - moveEveryOneInClubLadderDown()
* - moveUpOneInClubLadder()
* - printChallengeMatch()
* Classes list:
*/
/**
 *
 *
 * A ladder just for libraries
 *
 *
 *
 */
/**
 * For a doubles club ladder the individual winners will be ahead of the individual loser in their
 * respective order.
 */
function adjustDoublesClubLadder($winner1userid, $winner2userid, $loser1userid, $loser2userid, $ladderid, $clubid) {
    
    if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  winnerid 1 = $winner1userid\n winner 2 = $winner2userid\n loser 1 = $loser1userid\n loser 2 = $loser2userid\n ladderid = $ladderid\n clubid = $clubid");

    //winner 1
    $winner1query = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.ladderid = $ladderid 
						AND ladder.userid = $winner1userid
						AND ladder.enddate IS NULL";
    $winner1result = db_query($winner1query);
    $winner1position = mysqli_result($winner1result, 0);

    //winner 2
    $winner2query = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.ladderid = $ladderid 
						AND ladder.userid = $winner2userid
						AND ladder.enddate IS NULL";
    $winner2result = db_query($winner2query);
    $winner2position = mysqli_result($winner2result, 0);

    //loser 1
    $loser1query = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.ladderid = $ladderid 
						AND ladder.clubid =  $clubid 
						AND ladder.userid = $loser1userid
						AND ladder.enddate IS NULL";
    $loser1result = db_query($loser1query);
    $loser1position = mysqli_result($loser1result, 0);

    //loser 2
    $loser2query = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.ladderid = $ladderid 
						AND ladder.userid = $loser2userid
						AND ladder.enddate IS NULL";
    $loser2result = db_query($loser2query);
    $loser2position = mysqli_result($loser2result, 0);

    //if the winners is already ahead of the losers don't do anything
    
    if ($winner1position < $loser1position && $winner1position < $loser2position && $winner2position < $loser1position && $winner2position < $loser2position) {
        
        if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  Both members of the winning teams is already  ahead of both members of the losing team. Don't do nothing.");
        return;
    }
    
    if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  At leaset one of the members of the winning team is below one of the members of the losing team, adjusting...");

    // Set some variables for the losing team
    
    if ($loser1position < $loser2position) {
        $highestRankedLoserPosition = $loser1position;
        $highestRankedLoserid = $loser1userid;
        $lowestRankedLoserPosition = $loser2position;
        $lowestRankedLoserid = $loser2userid;
    } else {
        $highestRankedLoserPosition = $loser2position;
        $highestRankedLoserid = $loser2userid;
        $lowestRankedLoserPosition = $loser1position;
        $lowestRankedLoserid = $loser1userid;
    }

    // Set some variables for the winning team
    
    if ($winner1position < $winner2position) {
        $highestRankedWinnerPosition = $winner1position;
        $highestRankedWinnerid = $winner1userid;
        $lowestRankedWinnerPosition = $winner2position;
        $lowestRankedWinnerid = $winner2userid;
    } else {
        $highestRankedWinnerPosition = $winner2position;
        $highestRankedWinnerid = $winner2userid;
        $lowestRankedWinnerPosition = $winner1position;
        $lowestRankedWinnerid = $winner1userid;
    }

    // At this point we know that we will need to move at least one of the winners (the other winner might already be ahead of both losers)
    // Figure out how many of th ewinners need to move.

    
    if ($highestRankedWinnerPosition > $highestRankedLoserPosition) {

        //the highest winner was below the highest loser, this means both winners will move
        
        if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  Both winners are moving up");
        moveDoublesLadderGroup($highestRankedLoserPosition, $lowestRankedWinnerPosition, $highestRankedWinnerPosition, $clubid, $courttypeid);
        
        if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  Moving highest ranked winner ($highestRankedWinnerid) to position $highestRankedLoserPosition");

        //The highest ranked winner gets the highest ranked losers ladder position
        $updateWinnerQuery = "UPDATE tblClubLadder 
								SET ladderposition = $highestRankedLoserPosition 
								WHERE userid = $highestRankedWinnerid 
								AND ladderid = $ladderid 
								AND clubid = $clubid 
								AND enddate IS NULL";
        db_query($updateWinnerQuery);
        $lowestRankedWinnerNewPosition = $highestRankedLoserPosition + 1;
        
        if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  Moving the next winner ($lowestRankedWinnerid) to position $lowestRankedWinnerNewPosition");

        //The other winner gets the next position down
        $updateWinnerQuery = "UPDATE tblClubLadder 
								SET ladderposition = $lowestRankedWinnerNewPosition 
								WHERE userid = $lowestRankedWinnerid 
								AND ladderid = $ladderid 
								AND clubid = $clubid 
								AND enddate IS NULL";
        db_query($updateWinnerQuery);

        // The highest ranked loser moves down two spots to make room for each winner
        $highestRankedLoserNewPosition = $lowestRankedWinnerNewPosition + 1;
        
        if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  Resetting the highest loser id $highestRankedLoserid to $highestRankedLoserNewPosition");
        $updateLoserQuery = "UPDATE tblClubLadder 
								SET ladderposition = $highestRankedLoserNewPosition   
								WHERE userid = $highestRankedLoserid 
								AND ladderid = $ladderid 
								AND clubid = $clubid 
								AND enddate IS NULL";
        db_query($updateLoserQuery);
    } else {

        //this highest winner was already ahead of the highest loser, only the lowest winner will move
        
        if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  Only the lowest ranking winner moving.");
        moveDoublesLadderGroup($highestRankedLoserPosition, $lowestRankedWinnerPosition, $highestRankedWinnerPosition, $clubid, $courttypeid);
        
        if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  Moving lowest ranked winner ($lowestRankedWinnerid) to position $highestRankedLoserPosition");

        //The other winner gets the next position down
        $updateWinnerQuery = "UPDATE tblClubLadder 
								SET ladderposition = $highestRankedLoserPosition 
								WHERE userid = $lowestRankedWinnerid 
								AND ladderid = $ladderid 
								AND clubid = $clubid 
								AND enddate IS NULL";
        db_query($updateWinnerQuery);
        $highestRankedLoserNewPosition = $highestRankedLoserPosition + 1;
        
        if (isDebugEnabled(2)) logMessage("ladderlib: adjustDoublesClubLadder.  Moving highest ranked loser ($highestRankedLoserid) down one to position $highestRankedLoserNewPosition");
        $updateLoserQuery = "UPDATE tblClubLadder 
								SET ladderposition = $highestRankedLoserNewPosition   
								WHERE userid = $highestRankedLoserid 
								AND ladderid = $ladderid 
								AND clubid = $clubid 
								AND enddate IS NULL";
        db_query($updateLoserQuery);
    }
}
/**
 * A ladder group is a block of players in a club ladder that move down when a ladder is adjusted.  Generally
 * this is called first then the winners and losers are set specicially (by id).
 */
function moveLadderGroup($highestrankedloserposition, $lowestrankedwinnerposition, $ladderid) {
    
    if (isDebugEnabled(2)) logMessage("ladderlib: moveLadderGroup. Moving the ladder group from position $lowestrankedwinnerposition to $highestrankedloserposition");

    //Everybody in the ladder between the lowest ranking winner (which moved up) and the highest ranking loser ( which moved down)
    $everybodyQuery = "SELECT ladder.*, user.firstname, user.lastname
							FROM tblClubLadder ladder
							INNER JOIN tblUsers user on ladder.userid = user.userid
							WHERE ladder.enddate IS NULL
							AND ladder.ladderid = $ladderid
							AND ladder.ladderposition >= $highestrankedloserposition
							AND ladder.ladderposition <= $lowestrankedwinnerposition
							AND ladder.enddate IS NULL";
    $everybodyResult = db_query($everybodyQuery);
    while ($array = db_fetch_array($everybodyResult)) {
        $id = $array['id'];
        $position = $array['ladderposition'];
        $userid = $array['userid'];
        $newposition = $position + 1;
		$fullname = $array['firstname']. ' '. $array['lastname'];
        
        if (isDebugEnabled(2)) logMessage("ladderlib: moveLadderGroup.  Setting person at position $position to position $newposition for id $id and user $fullname ($userid)");
        $updateLoserQuery = "UPDATE tblClubLadder 
								SET ladderposition = $newposition   
								WHERE id = $id";
        db_query($updateLoserQuery);
        ++$newposition;
    }
}
/**
 * A ladder group is a block of players in a club ladder that move down when a ladder is adjusted.  Generally
 * this is called first then the winners and losers are set specicially (by id).
 */
function moveDoublesLadderGroup($highestRankedLoserPosition, $lowestRankedWinnerPosition, $highestRankedWinnerPosition, $clubid, $ladderid) {
    
    if (isDebugEnabled(2)) logMessage("ladderlib: moveLadderGroup. Moving the ladder group from position $lowestRankedWinnerPosition to $highestRankedLoserPosition");

    //Everybody in the ladder between the lowest ranking winner (which moved up) and the highest ranking loser ( which moved down)
    $everybodyQuery = "SELECT ladder.* from tblClubLadder ladder
							WHERE ladder.enddate IS NULL
							AND ladder.ladderid = $ladderid
							AND ladder.ladderposition >= $highestRankedLoserPosition
							AND ladder.ladderposition <= $lowestRankedWinnerPosition
							AND ladder.enddate IS NULL";
    $everybodyResult = db_query($everybodyQuery);
    while ($array = db_fetch_array($everybodyResult)) {
        $id = $array['id'];
        $position = $array['ladderposition'];
        $userid = $array['userid'];

        // Everyone that was ranked ahead of the top winner goes down 2 (for the winner and the loser)
        // Everyone that was ranked below the top winner goes down 1 (for the bottam winner)

        
        if ($highestRankedWinnerPosition > $position) {
            $newposition = $position + 2;
        } else {
            $newposition = $position + 1;
        }
        
        if (isDebugEnabled(2)) logMessage("ladderlib: moveLadderGroup.  Setting person at position $position to position $newposition for id $id and user $userid");
        $updateLoserQuery = "UPDATE tblClubLadder 
								SET ladderposition = $newposition   
								WHERE id = $id";
        db_query($updateLoserQuery);
        ++$newposition;
    }
}
/**
 * This assumes that these two are ladder players
 *
 * Returns an object that looks like this:
 * 		winnerid
 * 		winnernewspot
 * 		winneroldspot
 * 		loserid
 * 		losernewspot
 * 		loseroldspot
 *
 */
function adjustClubLadder($winneruserid, $loseruserid, $ladderid) {
    
    if (isDebugEnabled(2)) logMessage("ladderlib: adjustClubLadder.  winnerid = $winneruserid loserid = $loseruserid ladderid = $ladderid");
    
	$var = new clubpro_obj;
    $var->winnerid = $winneruserid;
    $var->loserid = $loseruserid;
    $winnerquery = "SELECT ladder.ladderposition, ladder.going, ladder.id 
						FROM tblClubLadder ladder 
						WHERE ladder.ladderid = $ladderid 
						AND ladder.userid = $winneruserid
						AND ladder.enddate IS NULL";
    $winnerresult = db_query($winnerquery);
    $winnerarray = mysqli_fetch_array($winnerresult);
    $winnerposition = $winnerarray['ladderposition'];
    $winnergoing = $winnerarray['going'];
    $var->winneroldspot = $winnerposition;
    $var->winnernewspot = $winnerposition;

    $loserquery = "SELECT ladder.ladderposition, ladder.going, ladder.id 
						FROM tblClubLadder ladder 
						WHERE ladder.ladderid = $ladderid 
						AND ladder.userid = $loseruserid
						AND ladder.enddate IS NULL";

    $loserresult = db_query($loserquery);
    $loserarray = mysqli_fetch_array($loserresult);
    $loserposition = $loserarray['ladderposition'];
    $losergoing = $loserarray['going'];
    $var->loseroldspot = $loserposition;
    $var->losernewspot = $loserposition;
    
    if (isDebugEnabled(2)) logMessage("ladderlib: adjustClubLadder.  The winners position/going is $winnerposition/$winnergoing and the losers position/going is $loserposition/$losergoing");

    //Set the goings
    
    if ($winnergoing == "up") {
        $wgoing = "up";
    } else 
    if ($winnergoing == "steady") {
        $wgoing = "up";
    } else {
        $wgoing = "steady";
    }
    
    if ($losergoing == "up") {
        $lgoing = "steady";
    } else 
    if ($losergoing == "steady") {
        $lgoing = "down";
    } else {
        $lgoing = "down";
    }

    //if the winner is already ahead of the loser don't do anything
    
    if ($winnerposition < $loserposition) {
        
        if (isDebugEnabled(2)) logMessage("ladderlib: adjustClubLadder:  Winner is already ahead of the loser. Just update the going attribute");

        //add new record for the guy that lost
        $query = "INSERT INTO tblClubLadder (userid, ladderid, ladderposition, going) VALUES (
                          $loseruserid,$ladderid,$loserposition,'$lgoing')";
        db_query($query);
        $query = "INSERT INTO tblClubLadder (userid, ladderid, ladderposition, going) VALUES (
                          $winneruserid,$ladderid,$winnerposition,'$wgoing')";
        db_query($query);

        //end date old ones
        $updateWinnerQuery = "UPDATE tblClubLadder SET enddate = NOW() WHERE id = " . $winnerarray['id'] . " OR id = " . $loserarray['id'];
        db_query($updateWinnerQuery);
        return $var;
    }
    
    if (isDebugEnabled(2)) logMessage("ladderlib: adjustClubLadder:  The winner was ranked below the loser, adjusting...");
    moveLadderGroup($loserposition, $winnerposition, $ladderid);
    
    if (isDebugEnabled(2)) logMessage("ladderlib: adjustClubLadder:  Setting winner ($winneruserid) to position $loserposition and going to $wgoing");

    //The winner gets the losers (higher) ladder position
    //add new record for the guy that won

    $query = "INSERT INTO tblClubLadder (userid, ladderid, ladderposition, going) VALUES (
                          $winneruserid,$ladderid,$loserposition,'$wgoing')";
    db_query($query);

    //end date old one
    $updateWinnerQuery = "UPDATE tblClubLadder SET enddate = NOW() WHERE id = " . $winnerarray['id'];
    db_query($updateWinnerQuery);

    // The loser's ladder position goes down one
    $newloserposition = $loserposition + 1;
    $var->losernewspot = $newloserposition;
    $var->winnernewspot = $loserposition;
    
    if (isDebugEnabled(2)) logMessage("ladderlib: adjustClubLadder.  Setting loser ($loseruserid) down one to $newloserposition and going to $lgoing");

    //add new record for the guy that lost
    $query = "INSERT INTO tblClubLadder (userid, ladderid, ladderposition, going) VALUES (
                          $loseruserid,$ladderid,$newloserposition,'$lgoing')";
    db_query($query);

    // end date the old one
    $updateLoserQuery = "UPDATE tblClubLadder SET enddate = NOW() WHERE id = " . $loserarray['id'];
    db_query($updateLoserQuery);
    return $var;
}
/**
 * Starting with the position, moves everyone in the ladder up.  This is used
 * when someone is removed from a club ladder
 */
function moveEveryOneInClubLadderUp($ladderid, $clubid, $ladderposition) {
    $query = "SELECT ladder.* 
				FROM tblClubLadder ladder
				WHERE ladder.ladderid = $ladderid
				AND ladder.ladderposition >= $ladderposition
				AND ladder.enddate IS NULL";
    $result = db_query($query);
    $count = 0;
    while ($array = db_fetch_array($result)) {
        $position = $array['ladderposition'];
        $clubladderid = $array['id'];
        $newposition = $position - 1;
        $updateQuery = "UPDATE tblClubLadder 
			SET ladderposition = $newposition 
			WHERE id = $clubladderid";
        db_query($updateQuery);
        ++$count;
    }
    
    if (isDebugEnabled(2)) logMessage("ladderlib: moveEveryOneInClubLadderUp.  Starting with position $ladderposition moved $count people up in ladderid id $ladderid ladder for club $clubid");
}
/**
 * Starting with the position, moves everyone in the ladder up.  This is used
 * when someone is added to the ladder
 */
function moveEveryOneInClubLadderDown($ladderid, $clubid, $ladderposition) {
    $query = "SELECT ladder.* 
				FROM tblClubLadder ladder
				WHERE ladder.ladderid = $ladderid
				AND ladder.ladderposition >= $ladderposition
				AND ladder.enddate IS NULL";
    $result = db_query($query);
    $count = 0;
    while ($array = db_fetch_array($result)) {
        $position = $array['ladderposition'];
        $clubladderid = $array['id'];
        $newposition = $position + 1;
        $updateQuery = "UPDATE tblClubLadder 
			SET ladderposition = $newposition 
			WHERE id = $clubladderid";
        db_query($updateQuery);
        ++$count;
    }
    
    if (isDebugEnabled(2)) logMessage("ladderlib: moveEveryOneInClubLadderDown.  Starting with position $ladderposition moved $count people up in ladder id $ladderid ladder for club $clubid");
}
/**
 * Used by club administrators when moving people up one in the club ladder. Quietly exits if there is a problem.
 *
 * @param $courttypeid
 * @param $clubid
 * @param $userid
 */
function moveUpOneInClubLadder($ladderid, $clubid, $userid) {
    
    if (isDebugEnabled(2)) logMessage("ladderlib: moveUpOneInClubLadder: moving user $userid up one in the ladder for club $clubid and ladderid $ladderid");

    //Look up the user (if this person has a ladder position of 1, exit
    $query = "SELECT ladder.* 
				FROM tblClubLadder ladder
				WHERE ladder.ladderid = $ladderid
				AND ladder.userid = $userid
				AND ladder.enddate IS NULL";
    $result = db_query($query);
    $movingUpArray = mysqli_fetch_array($result);
    
    if ($movingUpArray['ladderposition'] == 1) {
        if (isDebugEnabled(2)) logMessage("ladderlib: moveUpOneInClubLadder: already on top. exiting...");
		return;
    }

    //increment and store as variable
    $oneup = $movingUpArray['ladderposition'] - 1;
    
    if (isDebugEnabled(2)) logMessage("ladderlib: moveUpOneInClubLadder: user " . $movingUpArray['userid'] . " is moving up to position $oneup from position " . $movingUpArray['ladderposition']);

    //Get the next person up
    $query = "SELECT ladder.* 
				FROM tblClubLadder ladder
				WHERE ladder.ladderid = $ladderid
				AND ladder.ladderposition = $oneup
				AND ladder.enddate IS NULL";
    $result = db_query($query);
    $movingDownArray = mysqli_fetch_array($result);
    
    if (isDebugEnabled(2)) logMessage("ladderlib: moveUpOneInClubLadder:  userid " . $movingDownArray['userid'] . " is moving down");

    //Add a new record for guy going up a spot
    $query = "INSERT INTO tblClubLadder (userid, ladderid, ladderposition, going, locked) VALUES (
                          $movingUpArray[userid],$ladderid,$movingDownArray[ladderposition],'$movingUpArray[going]','$movingUpArray[locked]')";
    db_query($query);

    //enddate the old one
    $updateWinnerQuery = "UPDATE tblClubLadder ladder SET ladder.enddate = NOW() WHERE ladder.id = " . $movingUpArray['id'];
    db_query($updateWinnerQuery);
    
    if (isDebugEnabled(2)) logMessage("ladderlib: moveUpOneInClubLadder:  moved ladderid " . $movingUpArray['id'] . " to ladder position: " . $movingDownArray['ladderposition']);

    //Add a new record for guy going down a spot
    $query = "INSERT INTO tblClubLadder (userid, ladderid, ladderposition, going, locked) VALUES (
                          $movingDownArray[userid],$ladderid,$movingUpArray[ladderposition],'$movingDownArray[going]', '$movingDownArray[locked]')";
    db_query($query);

    //end date the old one
    $updateLoserQuery = "UPDATE tblClubLadder ladder SET ladder.enddate = NOW() WHERE ladder.id = " . $movingDownArray['id'];
    db_query($updateLoserQuery);
    
    if (isDebugEnabled(2)) logMessage("ladderlib: moveUpOneInClubLadder:  moved ladderid " . $movingDownArray['id'] . " to ladder position: " . $movingUpArray['ladderposition']);
}
/**
 * Prints out the HTML for the ladder
 *
 * @param $user1
 * @param $user2
 * @param $courtid
 * @param $time
 * @param $reservationid
 * @param $loserscore
 * @param $scored
 */
function printChallengeMatch($user1, $user2, $courtname, $time, $reservationid, $scored, $loserscore, $inreservation) {
?>
	
	 <li>
			 <? if( !$scored && (get_roleid() ==2 || get_roleid()==4 || $inreservation)) { ?>
			 	<a title="Click on me to record the score" href="javascript:submitForm('recordScoreForm<?=$reservationid?>')">
			 <? }?>
			 <span class="bold">
			<?=gmdate("l F j g:i a ", $time);?><br/>
			</span>
			<? 
			
			if( !$scored){
				print "$user1 and $user2";
			} else {
				print "$user1 defeated $user2 3-$loserscore ";
			}
			
			// Print out the players
			?>
			on <?=$courtname?>
			
			 <? if( !$scored && (get_roleid() ==2 || get_roleid()==4 || $inreservation) ) { ?>
			 	</a>
			 <? }?>
			
    
    
    <form name="recordScoreForm<?=$id?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_ladder.php" method="post">
           <input type="hidden" name="source" value="ladder">
           <input type="hidden" name="challengematchid" value="<?=$id?>">
     </form>
     
     </li>

<?
}

/**
 * Writes out HTML for the challenge match
 * 
 * @param unknown_type $id
 * @param unknown_type $challengerName
 * @param unknown_type $challengeeName
 * @param unknown_type $challengeDate
 * @param unknown_type $scored
 * @param unknown_type $inreservation
 */
function printLadderEvent($id, $challengerName, $challengeeName, $challengeDate, $scored, $inreservation, $singles ){
	
	$isscored = empty($scored) ? false : true;
	$isinreservation = $inreservation ? true : false;
	$loserscore = 3 - abs($scored);
	
	if( isDebugEnabled(1) ) logMessage("ladderlib.printChallengeMatch: challengerName: $challengerName challengeeName $challengeeName scored: $isscored isinreservation: $isinreservation");
	
	?>
	
	 <li> 
			 <span class="bold">
			<?=formatDateString( $challengeDate)?><br/>
			</span>
			<span class="normal">
			<? 
			
			// Print out the players
			if( $isscored && $scored > 0 ){
				print "$challengerName challenged and defeated $challengeeName 3-$loserscore.";
			} 
			else if($isscored && $scored < 0){
				print "$challengerName challenged and lost to $challengeeName 3-$loserscore.";
			}
			else if( !$isscored  && (get_roleid() == 2 || get_roleid() == 4 || $inreservation)  ){ ?>
				 <?=$challengerName." challenged ". $challengeeName?>.  Click <a title="Click on me to record the score" href="javascript:submitForm('recordScoreForm<?=$id?>')">here</a> to put the score in.
				
			<? } else {
				print "$challengerName challenged $challengeeName.";
			}
			
			?>
			</span>
			
    
    <form name="recordScoreForm<?=$id?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_ladder.php" method="post">
           <input type="hidden" name="laddertype" value="<?=$singles?"player":"team"?>">
           <input type="hidden" name="source" value="ladder">
           <input type="hidden" name="challengematchid" value="<?=$id?>">
     </form>
     
     </li>

<?
	
}


function printLadderMatchRow($id, $winner, $loser, $challengeDate, $score){
	
	?>
	
	<tr>
		<td>
			<?=formatDateStringSimple( $challengeDate)?>
		</td>
		<td>
			<?=$winner->fullname ?>
		</td>
		<td>
			<?=$loser->fullname ?>
		</td>
		<td>
			<?=$score ?>
		</td>
		
		
	</tr>
	<?

}
/**
 * 
 * @param $id
 * @param $challengerName
 * @param $challengeeName
 * @param $challengeDate
 * @param $scored
 * @param $inreservation
 * @param $singles
 */
function printLadderEventRow($id, $challenger, $challengee, $challengeDate, $scored, $inreservation, $singles){
	
	$isscored = empty($scored) ? false : true;
	$isinreservation = $inreservation ? true : false;
	$loserscore = 3 - abs($scored);
	
	if( isDebugEnabled(1) ) logMessage("ladderlib.printChallengeMatch: challengerName: $challenger->fullname challengeeName $challengee->fullname scored: $isscored isinreservation: $isinreservation id: $id");
	
	?>
	
	<tr>
		<td>
			<?=formatDateStringSimple( $challengeDate)?>
		</td>
		<td>
			<?=$challenger->fullname ?>
		</td>
		
		<td>
			<?=$challengee->fullname ?>
		</td>
		<? if($isscored && $scored > 0 ){ ?>
		<td align="center">
			<span title="<?="3-".$loserscore?>"><?=$challenger->fullname ?></span>
		</td>
		<? } elseif( $isscored && $scored < 0){?>
		<td align="center">
			<span title="<?="3-".$loserscore?>"><?=$challengee->fullname ?></span>
		</td>
		
		<? } elseif(!$isscored  && (get_roleid() == 2 || get_roleid() == 4 || $inreservation) ) {?>
			  <td align="center">
			  	<a title="Click on me to record the score" href="javascript:recordScore('<?=$id?>','<?=$singles?"player":"team"?>')">enter score</a> 

			  	<? if(get_roleid() == 2 || get_roleid() == 4){ ?>
			  		<a title="Click on me to remove this challenge" href="javascript:removeChallengeMatch('<?=$id?>', '<?=$challenger->id?>', '<?=$challengee->id?>')"> 
			  			<img src="<?=$_SESSION["CFG"]["imagedir"]?>/recyclebin_empty.png" title="remove this challenge"/>
			  		</a> 
			  	<?}?>
			  </td>
		<? } else { ?>
		<td align="center">
			--
		</td>
		<? } ?>
	</tr>
	<?
}

/**
 * Determines whether or not the person can challenge or now
 */
function isLadderChallengable($myposition, $playerposition){
	
	//if( isDebugEnabled(1) ) logMessage("ladderlib: isLadderChallengable myposition: $myposition playerposition $playerposition");
	
	if ( isJumpLadderRankingScheme() ){
		return false;
	}

	$range = getChallengeRange();
	
	$value = $myposition-$range;
	
	//if( isDebugEnabled(1) ) logMessage("ladderlib: isLadderChallengable checking range: $range is ". $value);
	
	if($playerposition >= $myposition-$range && $playerposition < $myposition){
		return true;
	} else {
		return false;
	}

}


/**
 * Gets the recent challenges matches
 * 
 * @param $siteid
 */
function getLadderDetails($ladderid){
	
	if( isDebugEnabled(1) ) logMessage("ladderlib.getLadderDetails: Ladder $ladderid");
	
	$query = "SELECT * from tblClubSiteLadders WHERE id = $ladderid";
	
	$result = db_query($query);
	return db_fetch_object($result);
	

}

function getLadderMatches($ladderid, $limit){

	if( isDebugEnabled(1) ) logMessage("ladderlib.getLadderMatches: Getting ladder matches for ladderid $ladderid with limit $limit");
	
	$curresidquery = "SELECT
						winner.firstname AS winner_first,
						winner.lastname AS winner_last,
						concat_ws(' ', winner.firstname, winner.lastname) AS winner_full,
						winner.userid AS winner_id,
						loser.firstname AS loser_first,
						loser.lastname AS loser_last,
						loser.userid AS loser_id,
						ladder.id, ladder.score, ladder.match_time
						FROM tblLadderMatch ladder
						inner join tblUsers winner on ladder.winnerid = winner.userid
						inner join tblUsers loser on ladder.loserid = loser.userid
						inner join tblClubSiteLadders tCSL on ladder.ladderid = tCSL.id
						WHERE ladder.ladderid =$ladderid
							AND tCSL.enddate IS NULL
							AND ladder.enddate IS NULL
						ORDER BY ladder.match_time DESC , ladder.reported_time DESC
						LIMIT $limit";
	
	//print $curresidquery;
	return db_query($curresidquery);
}
/**
 * Gets the recent challenges matches
 * 
 * @param $siteid
 */
function getChallengeMatches($siteid, $ladderid, $limit){
	
	if( isDebugEnabled(1) ) logMessage("ladderlib.getChallengeMatches: Getting challenge matches $siteid and ladderid $ladderid limit $limit");
	
	$curresidquery = "SELECT 	
							challenge.id, challenge.score, challenge.date, 
							challenger.firstname AS challenger_first, 
							challenger.lastname AS challenger_last, 
							concat_ws(' ', challenger.firstname, challenger.lastname) AS challenger_full,
							challenger.userid AS challenger_id, 
							challengee.firstname AS challengee_first, 
							challengee.lastname AS challengee_last, 
							challengee.userid AS challengee_id, 
							challengerladder.locked AS challenger_locked, 
							challengeeladder.locked AS challengee_locked
						FROM tblChallengeMatch challenge
							INNER JOIN tblUsers challenger ON challenge.challengerid = challenger.userid
							INNER JOIN tblClubLadder challengerladder ON challenge.challengerid = challengerladder.userid
							INNER JOIN tblUsers challengee ON challenge.challengeeid = challengee.userid
							INNER JOIN tblClubLadder challengeeladder ON challenge.challengeeid = challengeeladder.userid
						WHERE challenge.enddate IS NULL 
							AND challenge.ladderid =$ladderid
							AND challenge.siteid = $siteid
							AND challengerladder.enddate IS NULL 
							AND challengeeladder.enddate IS NULL 
					 ORDER BY challenge.date DESC LIMIT $limit";
	
	//print $curresidquery;
	return db_query($curresidquery);
}


/**
 * Gets the recent challenges matches for doubles ladders
 * 
 * @param $siteid
 * @param $courttypeid
 * @param $limit
 */
function getDoublesChallengeMatches($siteid, $ladderid, $limit){
	
	if( isDebugEnabled(1) ) logMessage("ladderlib.getChallengeMatches: Getting challenge matches $siteid and ladderid $ladderid limit $limit");
	
	$curresidquery = "SELECT 	
							challenge.id, challenge.score, challenge.date, challenge.challengerid, challenge.challengeeid,
							challengerladder.locked AS challenger_locked, 
							challengeeladder.locked AS challengee_locked
						FROM tblChallengeMatch challenge
							INNER JOIN tblClubLadder challengerladder ON challenge.challengerid = challengerladder.userid
							INNER JOIN tblClubLadder challengeeladder ON challenge.challengeeid = challengeeladder.userid
						WHERE challenge.enddate IS NULL 
							AND challenge.ladderid =$ladderid
							AND challenge.siteid = $siteid
							AND challengerladder.enddate IS NULL 
							AND challengeeladder.enddate IS NULL 
					 ORDER BY challenge.date DESC LIMIT $limit";
	
	//print $curresidquery;
	$result =  db_query($curresidquery);
	
	$array = array();
	
	while($ladder = mysqli_fetch_array($result)){
		
		//get users for team id
		
		
		$challengerarray = getFullnameForTeamPlayers($ladder['challengerid']);
		$challengeearray = getFullnameForTeamPlayers($ladder['challengeeid']);
		
		$challenger1 = $challengerarray[0]['lastname'];
		$challenger2 = $challengerarray[1]['lastname'];
		
		$challengee1 = $challengeearray[0]['lastname'];
		$challengee2 = $challengeearray[1]['lastname'];
		
		$playerids = array($challengerarray[0]['userid'],$challengerarray[1]['userid'],$challengeearray[0]['userid'],$challengeearray[1]['userid']);

		$item = array('id' => $ladder['id'], 
					'score' => $ladder['score'], 
					'date' => $ladder['date'], 
					'challenger_team_id' => $ladder['challengerid'],
					'challenger_team' => $challenger1."/".$challenger2,
					'challengee_team_id' => $ladder['challengeeid'],
					'challengee_team' => $challengee1."/".$challengee2,
					'ids' => $playerids);
		$array[] = $item;
	}
	
	return $array;
}

/**
 * Get the recent doubles challenge matches
 * 
 * @param $siteid
 * @param $courttypeid
 * @param $limit
 */
function getTeamChallengeMatches($siteid, $ladderid, $limit){
	
	if( isDebugEnabled(1) ) logMessage("ladderlib.getTeamChallengeMatches: Getting challenge matches $siteid and ladderid $ladderid limit $limit");
	
	$query = "";
}

/**
 * 
 * @param $challengeMatchId
 */
function loadLadderMatch($challengeMatchId){
	
	
	logMessage("ladderlib.loadLadderMatch: Getting challenge match challengeMatchId $challengeMatchId");
	
	$curresidquery = "SELECT 	
							challenge.id, challenge.score, challenge.date, challenge.courttypeid,
							challenger.firstname AS challenger_first, 
							challenger.lastname AS challenger_last,
							concat_ws(' ', challenger.firstname, challenger.lastname) AS challenger_full, 
							challenger.userid AS challenger_id, 
							challengee.firstname AS challengee_first, 
							challengee.lastname AS challengee_last, 
							concat_ws(' ', challengee.firstname, challengee.lastname) AS challengee_full,
							challengee.userid AS challengee_id, 
							challengerladder.locked AS challenger_locked, 
							challengeeladder.locked AS challengee_locked
						FROM tblChallengeMatch challenge
							INNER JOIN tblUsers challenger ON challenge.challengerid = challenger.userid
							INNER JOIN tblClubLadder challengerladder ON challenge.challengerid = challengerladder.userid
							INNER JOIN tblUsers challengee ON challenge.challengeeid = challengee.userid
							INNER JOIN tblClubLadder challengeeladder ON challenge.challengeeid = challengeeladder.userid
						WHERE challenge.id =$challengeMatchId
							AND challengerladder.enddate IS NULL 
							AND challengeeladder.enddate IS NULL";
	
	//print $curresidquery;
	$result = db_query($curresidquery);
	
	$ladder = mysqli_fetch_array($result);
		
	$array = array('id' => $ladder['id'], 
				'score' => $ladder['score'], 
				'date' => $ladder['date'], 
				'challenger_full' => $ladder['challenger_full'],
				'challengee_full' => $ladder['challengee_full'],
				'courttypeid' => $ladder['courttypeid'],
				'challengee_id' => $ladder['challengee_id'],
				'challenger_id' => $ladder['challenger_id']);
	
	
	return $array;
}



/**
 * Used for loading up the challenge match for reporting the score.
 * 
 * @param $challengeMatchId
 */
function loadDoublesLadderMatch($challengeMatchId){
	
	logMessage("ladderlib.loadDoublesLadderMatch: Getting challenge match challengeMatchId $challengeMatchId");
	
	$curresidquery = "SELECT 	
							challenge.id, challenge.score, challenge.date, challenge.courttypeid,
							challengerladder.locked AS challenger_locked, 
							challengeeladder.locked AS challengee_locked,
							challenge.challengerid,
							challenge.challengeeid
						FROM tblChallengeMatch challenge
							INNER JOIN tblClubLadder challengerladder ON challenge.challengerid = challengerladder.userid
							INNER JOIN tblClubLadder challengeeladder ON challenge.challengeeid = challengeeladder.userid
						WHERE challenge.id =$challengeMatchId
							AND challengerladder.enddate IS NULL 
							AND challengeeladder.enddate IS NULL";
	
	//print $curresidquery;
	$result = db_query($curresidquery);
	
	$ladder = mysqli_fetch_array($result);
	
	//get users for team id
	$challengerarray = getFullnameForTeamPlayers($ladder['challengerid']);
	$challengeearray = getFullnameForTeamPlayers($ladder['challengeeid']);
	
	$challenger1 = $challengerarray[0]['firstname']." ".$challengerarray[0]['lastname'];
	$challenger2 = $challengerarray[1]['firstname']." ".$challengerarray[1]['lastname'];
	
	$challengee1 = $challengeearray[0]['firstname']." ".$challengeearray[0]['lastname'];
	$challengee2 = $challengeearray[1]['firstname']." ".$challengeearray[1]['lastname'];
	
	$playerids = array($challengerarray[0]['userid'],$challengerarray[1]['userid'],$challengeearray[0]['userid'],$challengeearray[1]['userid']);

	$array = array('id' => $ladder['id'], 
				'score' => $ladder['score'], 
				'date' => $ladder['date'], 
				'challenger_full' => $challenger1." and ".$challenger2,
				'challengee_full' => $challengee1." and ".$challengee2,
				'courttypeid' => $ladder['courttypeid'],
				'challengee_id' => $ladder['challengeeid'],
				'challenger_id' => $ladder['challengerid'],
				'ids' => $playerids);
		

	return $array;
	
}

function unlockLadderPlayers($challengerid, $challengeeid, $ladderid){
	
	if( isDebugEnabled(1) ) logMessage("ladderlib.unlockLadderPlayers: locking challenger:  $challengerid and challengee:  $challengeeid on ladderid $ladderid");
	
	$query = "UPDATE tblClubLadder ladder SET ladder.locked = 'n' WHERE ladder.userid = $challengerid OR ladder.userid = $challengeeid
				AND ladder.enddate IS NULL and ladder.ladderid = $ladderid and ladder.clubid = ".get_clubid();
	
	db_query($query);
	
}

/**
 * Locks the players in the ladder
 * 
 * @param $challengerid
 * @param $challengeeid
 */
function lockLadderPlayers($challengerid, $challengeeid, $ladderid){
	
	if( isDebugEnabled(1) ) logMessage("ladderlib.lockLadderPlayers: locking challenger:  $challengerid and challengee:  $challengeeid on ladderid $ladderid");
	
	$query = "UPDATE tblClubLadder ladder SET ladder.locked = 'y' WHERE ladder.userid = $challengerid OR ladder.userid = $challengeeid
				AND ladder.enddate IS NULL and ladder.ladderid = $ladderid and ladder.clubid = ".get_clubid();
	
	db_query($query);
}


/**
 * Gets the ladder for the given court type
 *
 * @param unknown_type $courttypeid
 */
function getLadder($ladderid) {
    
    if (isDebugEnabled(1)) logMessage("player_ladder.getLadder: getting the players in the ladder for ladderid $ladderid");
    $rankquery = "SELECT 
						users.userid,
						ladder.ladderposition,
						ladder.going,
						users.firstname, 
						users.lastname,
						concat_ws(' ', users.firstname, users.lastname) as fullname,
						users.email,
						ladder.locked
                    FROM 
						tblUsers users, 
						tblClubLadder ladder
                    WHERE 
						users.userid = ladder.userid
                    AND ladder.ladderid=$ladderid
					AND ladder.enddate IS NULL
                    ORDER BY ladder.ladderposition";
    
    return db_query($rankquery);
}
/**
 * True is user is, false if player isn't
 * @param $userid
 */
function isPlayingInLadder($userid, $ladderid) {
    $query = "SELECT 1 FROM tblClubLadder 
                WHERE userid = $userid 
                AND ladderid = $ladderid 
                AND clubid = " . get_clubid() . " AND enddate IS NULL";
    
    $result = db_query($query);
    $rows = mysqli_num_rows($result);
    
    if ($rows > 0) {
        return true;
    } else {
        return false;
    }
}
/**
 * Send off an email to the challenger and the challengee
 *
 * @param unknown_type $challengerid
 * @param unknown_type $challengeeid
 */
function sendEmailsForLadderMatch($challengerid, $challengeeid, $message) {
    
    if (isDebugEnabled(1)) logMessage("player_ladder.sendEmailsForLadderMatch: sending out emails to challenger $challengerid and challengee $challengeeid ");

    //Set some variables
    $subject = get_clubname() . " - Ladder Match Confirmation";

    /* load up the user record for the winner */
    $query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$challengerid'";
    $qid = db_query($query);
    $challenger = db_fetch_object($qid);

    /* load up the user record for the winner */
    $query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$challengeeid'";
    $qid = db_query($query);
    $challengee = db_fetch_object($qid);

    /* email the user with the new account information */
    $var = new clubpro_obj;
    $var->challenger_firstname = $challenger->firstname;
    $var->challenger_fullname = $challenger->firstname . " " . $challenger->lastname;
    $var->challengee_firstname = $challengee->firstname;
    $var->challengee_fullname = $challengee->firstname . " " . $challengee->lastname;
    $var->support = $_SESSION["CFG"]["support"];
    $template = get_sitecode();
    $challenger_emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/confirm_ladder_match_challenger.php", $var);
    $challenger_emailbody = nl2br($challenger_emailbody);

    // Provide Content for Challenger
    $challenger_email = array(
        $challenger->email => array(
            'name' => $challenger->firstname
        )
    );
    $content = new clubpro_obj;
    $content->line1 = $challenger_emailbody;
    $content->clubname = get_clubname();


    //Send the email
    send_email($subject, $challenger_email, $content, "Ladder Match");

    // Provide Content for Challengee
    $challengee_email = array(
        $challengee->email => array(
            'name' => $challengee->firstname
        )
    );
    $content = new clubpro_obj;
    $message = nl2br($message);
    $content->line1 = $message;
    $content->clubname = get_clubname();
    $from_email = "$var->challenger_fullname <$challenger->email>";
    $template = get_sitecode() . "-blank";
    $subject = get_clubname() . " - You've been challenged in a ladder match";

    //Send the email
    send_email($subject, $challengee_email, $content, "Ladder Match");
}

?>
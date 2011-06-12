<?

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
function adjustDoublesClubLadder($winner1userid, $winner2userid, $loser1userid, $loser2userid, $courttypeid, $clubid){
	
	if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  winnerid 1 = $winner1userid\n winner 2 = $winner2userid\n loser 1 = $loser1userid\n loser 2 = $loser2userid\n courttypeid = $courttypeid\n clubid = $clubid");
	
	//winner 1
	$winner1query = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.courttypeid = $courttypeid 
						AND ladder.clubid =  $clubid 
						AND ladder.userid = $winner1userid
						AND ladder.enddate IS NULL";
	
	$winner1result = db_query($winner1query);
	$winner1position = mysql_result($winner1result,0);
		
	//winner 2				
	$winner2query = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.courttypeid = $courttypeid 
						AND ladder.clubid =  $clubid 
						AND ladder.userid = $winner2userid
						AND ladder.enddate IS NULL";
	
	$winner2result = db_query($winner2query);
	$winner2position = mysql_result($winner2result,0);
	
	//loser 1
	$loser1query = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.courttypeid = $courttypeid 
						AND ladder.clubid =  $clubid 
						AND ladder.userid = $loser1userid
						AND ladder.enddate IS NULL";
	
	$loser1result = db_query($loser1query);
	$loser1position = mysql_result($loser1result,0);
		
	//loser 2					
	$loser2query = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.courttypeid = $courttypeid 
						AND ladder.clubid =  $clubid 
						AND ladder.userid = $loser2userid
						AND ladder.enddate IS NULL";
	
	$loser2result = db_query($loser2query);
	$loser2position = mysql_result($loser2result,0);
	
	
	//if the winners is already ahead of the losers don't do anything
	if($winner1position < $loser1position
		&& $winner1position < $loser2position
		&& $winner2position < $loser1position 
		&& $winner2position < $loser2position){
		if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  Both members of the winning teams is already  ahead of both members of the losing team. Don't do nothing.");
		return;
	}
	
	if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  At leaset one of the members of the winning team is below one of the members of the losing team, adjusting...");
	

	// Set some variables for the losing team
	if($loser1position < $loser2position){
		$highestRankedLoserPosition = $loser1position;
		$highestRankedLoserid = $loser1userid;
		$lowestRankedLoserPosition = $loser2position;
		$lowestRankedLoserid = $loser2userid;
	}
	else{
		$highestRankedLoserPosition = $loser2position;
		$highestRankedLoserid = $loser2userid;
		$lowestRankedLoserPosition = $loser1position;
		$lowestRankedLoserid = $loser1userid;
	}
	
	// Set some variables for the winning team
	if($winner1position < $winner2position){
		$highestRankedWinnerPosition = $winner1position;
		$highestRankedWinnerid = $winner1userid;
		$lowestRankedWinnerPosition = $winner2position;
		$lowestRankedWinnerid = $winner2userid;
	}
	else{
		$highestRankedWinnerPosition = $winner2position;
		$highestRankedWinnerid = $winner2userid;
		$lowestRankedWinnerPosition = $winner1position;
		$lowestRankedWinnerid = $winner1userid;
	}
	
	// At this point we know that we will need to move at least one of the winners (the other winner might already be ahead of both losers)
	// Figure out how many of th ewinners need to move.
	
	if($highestRankedWinnerPosition > $highestRankedLoserPosition){
		//the highest winner was below the highest loser, this means both winners will move
		
		if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  Both winners are moving up");
	
		moveDoublesLadderGroup($highestRankedLoserPosition, $lowestRankedWinnerPosition, $highestRankedWinnerPosition, $clubid, $courttypeid)	;
		
		
		if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  Moving highest ranked winner ($highestRankedWinnerid) to position $highestRankedLoserPosition");
		//The highest ranked winner gets the highest ranked losers ladder position
		$updateWinnerQuery = "UPDATE tblClubLadder 
								SET ladderposition = $highestRankedLoserPosition 
								WHERE userid = $highestRankedWinnerid 
								AND courttypeid = $courttypeid 
								AND clubid = $clubid 
								AND enddate IS NULL";
	
		db_query($updateWinnerQuery);
		
		$lowestRankedWinnerNewPosition = $highestRankedLoserPosition + 1;
		
		if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  Moving the next winner ($lowestRankedWinnerid) to position $lowestRankedWinnerNewPosition");
		//The other winner gets the next position down
		$updateWinnerQuery = "UPDATE tblClubLadder 
								SET ladderposition = $lowestRankedWinnerNewPosition 
								WHERE userid = $lowestRankedWinnerid 
								AND courttypeid = $courttypeid 
								AND clubid = $clubid 
								AND enddate IS NULL";
	
		db_query($updateWinnerQuery);
		
		// The highest ranked loser moves down two spots to make room for each winner
		$highestRankedLoserNewPosition = $lowestRankedWinnerNewPosition + 1;
		if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  Resetting the highest loser id $highestRankedLoserid to $highestRankedLoserNewPosition");
		
		$updateLoserQuery = "UPDATE tblClubLadder 
								SET ladderposition = $highestRankedLoserNewPosition   
								WHERE userid = $highestRankedLoserid 
								AND courttypeid = $courttypeid 
								AND clubid = $clubid 
								AND enddate IS NULL";
	
		db_query($updateLoserQuery);
		
		
	}else{
		//this highest winner was already ahead of the highest loser, only the lowest winner will move
		
		if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  Only the lowest ranking winner moving.");
		
		moveDoublesLadderGroup($highestRankedLoserPosition, $lowestRankedWinnerPosition, $highestRankedWinnerPosition,  $clubid, $courttypeid)	;
	
		if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  Moving lowest ranked winner ($lowestRankedWinnerid) to position $highestRankedLoserPosition");
		//The other winner gets the next position down
		$updateWinnerQuery = "UPDATE tblClubLadder 
								SET ladderposition = $highestRankedLoserPosition 
								WHERE userid = $lowestRankedWinnerid 
								AND courttypeid = $courttypeid 
								AND clubid = $clubid 
								AND enddate IS NULL";
	
		db_query($updateWinnerQuery);
		
		$highestRankedLoserNewPosition = $highestRankedLoserPosition + 1;
		
		if( isDebugEnabled(2) ) logMessage("ladderlib: adjustDoublesClubLadder.  Moving highest ranked loser ($highestRankedLoserid) down one to position $highestRankedLoserNewPosition");
		$updateLoserQuery = "UPDATE tblClubLadder 
								SET ladderposition = $highestRankedLoserNewPosition   
								WHERE userid = $highestRankedLoserid 
								AND courttypeid = $courttypeid 
								AND clubid = $clubid 
								AND enddate IS NULL";
	
		db_query($updateLoserQuery);
		
	}
	
		
}

/**
 * A ladder group is a block of players in a club ladder that move down when a ladder is adjusted.  Generally
 * this is called first then the winners and losers are set specicially (by id).
 */
function moveLadderGroup($highestrankedloserposition, $lowestrankedwinnerposition, $clubid, $courttypeid){
	
		if( isDebugEnabled(2) ) logMessage("ladderlib: moveLadderGroup. Moving the ladder group from position $lowestrankedwinnerposition to $highestrankedloserposition");
				
	
		//Everybody in the ladder between the lowest ranking winner (which moved up) and the highest ranking loser ( which moved down)
		$everybodyQuery = "SELECT ladder.* from tblClubLadder ladder
							WHERE ladder.clubid = $clubid
							AND ladder.enddate IS NULL
							AND ladder.courttypeid = $courttypeid
							AND ladder.ladderposition >= $highestrankedloserposition
							AND ladder.ladderposition <= $lowestrankedwinnerposition
							AND ladder.enddate IS NULL";
							
	   
	   $everybodyResult = db_query($everybodyQuery);
	
		while( $array = db_fetch_array($everybodyResult)){
			
			$id = $array['id'];
			$position = $array['ladderposition'];
			$userid = $array['userid'];
			
			$newposition = $position + 1;
			if( isDebugEnabled(2) ) logMessage("ladderlib: moveLadderGroup.  Setting person at position $position to position $newposition for id $id and user $userid");
			
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
function moveDoublesLadderGroup($highestRankedLoserPosition, $lowestRankedWinnerPosition, $highestRankedWinnerPosition, $clubid, $courttypeid){
	
		if( isDebugEnabled(2) ) logMessage("ladderlib: moveLadderGroup. Moving the ladder group from position $lowestRankedWinnerPosition to $highestRankedLoserPosition");
				
	
		//Everybody in the ladder between the lowest ranking winner (which moved up) and the highest ranking loser ( which moved down)
		$everybodyQuery = "SELECT ladder.* from tblClubLadder ladder
							WHERE ladder.clubid = $clubid
							AND ladder.enddate IS NULL
							AND ladder.courttypeid = $courttypeid
							AND ladder.ladderposition >= $highestRankedLoserPosition
							AND ladder.ladderposition <= $lowestRankedWinnerPosition
							AND ladder.enddate IS NULL";
							
	   
	   $everybodyResult = db_query($everybodyQuery);
	
		while( $array = db_fetch_array($everybodyResult)){
			
			$id = $array['id'];
			$position = $array['ladderposition'];
			$userid = $array['userid'];
			
			// Everyone that was ranked ahead of the top winner goes down 2 (for the winner and the loser)
			// Everyone that was ranked below the top winner goes down 1 (for the bottam winner)
			if($highestRankedWinnerPosition > $position){
				$newposition = $position + 2;
			}
			else{
				$newposition = $position + 1;
			}
			

			if( isDebugEnabled(2) ) logMessage("ladderlib: moveLadderGroup.  Setting person at position $position to position $newposition for id $id and user $userid");
			
			$updateLoserQuery = "UPDATE tblClubLadder 
								SET ladderposition = $newposition   
								WHERE id = $id";
								
			db_query($updateLoserQuery);
			++$newposition;
			
		}
}


/**
 * This assumes that these two are ladder players
 */
function adjustClubLadder($winneruserid, $loseruserid, $courttypeid, $clubid){

	if( isDebugEnabled(2) ) logMessage("ladderlib: adjustClubLadder.  winnerid = $winneruserid\n loserid = $loseruserid\n courttypeid = $courttypeid\n clubid = $clubid");

	$winnerquery = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.courttypeid = $courttypeid 
						AND ladder.clubid =  $clubid 
						AND ladder.userid = $winneruserid
						AND ladder.enddate IS NULL";
	
	$winnerresult = db_query($winnerquery);
	$winnerposition = mysql_result($winnerresult,0);
						
	$loserquery = "SELECT ladder.ladderposition 
						FROM tblClubLadder ladder 
						WHERE ladder.courttypeid = $courttypeid 
						AND ladder.clubid =  $clubid 
						AND ladder.userid = $loseruserid
						AND ladder.enddate IS NULL";
	
	$loserresult = db_query($loserquery);
	$loserposition = mysql_result($loserresult,0);
	
	if( isDebugEnabled(2) ) logMessage("ladderlib: adjustClubLadder.  The winners position is $winnerposition and the losers position is $loserposition");
	
	//if the winner is already ahead of the loser don't do anything
	if($winnerposition < $loserposition){
		if( isDebugEnabled(2) ) logMessage("ladderlib: adjustClubLadder.  Winner is already ahead of the loser. Don't do nothing.");
		return;
	}
	
	if( isDebugEnabled(2) ) logMessage("ladderlib: adjustClubLadder.  The winner was ranked below the loser, adjusting...");
	
	// The loser's ladder position goes down one
	$newloserposition = $loserposition + 1;		
	
	moveLadderGroup($loserposition, $winnerposition, $clubid, $courttypeid)	;
					
	if( isDebugEnabled(2) ) logMessage("ladderlib: adjustClubLadder.  Setting winner ($winneruserid) to position $loserposition");
	//The winner gets the losers (higher) ladder position
	$updateWinnerQuery = "UPDATE tblClubLadder 
							SET ladderposition = $loserposition 
							WHERE userid = $winneruserid 
							AND courttypeid = $courttypeid 
							AND clubid = $clubid 
							AND enddate IS NULL";

	db_query($updateWinnerQuery);
	

	if( isDebugEnabled(2) ) logMessage("ladderlib: adjustClubLadder.  Setting loser ($loseruserid) down one to $newloserposition");
	$updateLoserQuery = "UPDATE tblClubLadder 
							SET ladderposition = $newloserposition   
							WHERE userid = $loseruserid 
							AND courttypeid = $courttypeid 
							AND clubid = $clubid 
							AND enddate IS NULL";

	db_query($updateLoserQuery);
		

}

/**
 * Starting with the position, moves everyone in the ladder up.  This is used
 * when someone is removed from a club ladder
 */
function moveEveryOneInClubLadderUp($courttypeid, $clubid, $ladderposition){
	
	$query = "SELECT ladder.* 
				FROM tblClubLadder ladder
				WHERE ladder.courttypeid = $courttypeid
				AND ladder.clubid = $clubid
				AND ladder.ladderposition >= $ladderposition";
				
	$result = db_query($query);
	$count = 0;
	
	while($array = db_fetch_array($result)){
		
		$position = $array['ladderposition'];
		$clubladderid = $array['id'];
		$newposition = $position - 1 ;
		
		$updateQuery = "UPDATE tblClubLadder 
			SET ladderposition = $newposition 
			WHERE id = $clubladderid";
			
		db_query($updateQuery);
		++$count;
		
	}
	
	if( isDebugEnabled(2) ) logMessage("ladderlib: moveEveryOneInClubLadderUp.  Starting with position $ladderposition moved $count people up in courttype id $courttypeid ladder for club $clubid");
	
}
?>
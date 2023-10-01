<?php

/*

Schedule this in cron
*/
/*
This script runs every minute to send out the reservation reminders
*/

include ("../application.php");
include ("../lib/ladderlib.php");


date_default_timezone_set('GMT');


$service = new LadderUpdateService();
$service->updateJumpLadders();

class LadderUpdateService{

    /*
	*
	* Get all jump ladder
	*
	*/
	public function updateJumpLadders(){

        if (isDebugEnabled(1)) logMessage("LadderUpdateService:updateJumpLadders. Starting...");
        
        $yesterday = date('Y-m-d', time() - 60 * 60 * 24);

        $query = "SELECT tCSL.*
                FROM tblClubSites sites
                INNER JOIN tblClubSiteLadders tCSL on sites.siteid = tCSL.siteid
                WHERE sites.rankingscheme = 'jumpladder'";
	
	  	$ladders_result = db_query($query);

        /** For each one, go through each ladder and 
         * Get all scores that were reported for the previous day 
         * */

		while($ladder_array = mysqli_fetch_array($ladders_result) ){

            $query = "SELECT
                            winner.firstname AS winner_first,
                            winner.lastname AS winner_last,
                            concat_ws(' ', winner.firstname, winner.lastname) AS winner_full,
                            winner.userid AS winner_id,
                            loser.firstname AS loser_first,
                            loser.lastname AS loser_last,
                            concat_ws(' ', loser.firstname, loser.lastname) AS loser_full,
                            loser.userid AS loser_id,
                            ladder.ladderid as ladder_id, 
                            ladder.score, ladder.match_time, ladder.reported_time,
                            ladder.score,
                            ladder.id as ladder_match_id
                            FROM tblLadderMatch ladder
                            inner join tblUsers winner on ladder.winnerid = winner.userid
                            inner join tblUsers loser on ladder.loserid = loser.userid
                            inner join tblClubSiteLadders tCSL on ladder.ladderid = tCSL.id
                            WHERE ladder.ladderid = ".$ladder_array['id']." AND tCSL.enddate IS NULL
                                AND ladder.enddate IS NULL
                                AND date(ladder.match_time) = '$yesterday'
                             ORDER BY ladder.match_time ASC, ladder.reported_time ASC";
               
                
                $result = db_query($query);
                while($match_array = mysqli_fetch_array($result) ){
                    if (isDebugEnabled(1)) logMessage("LadderUpdateService:getJumpLadders ".$match_array['winner_full']. " defeated ". $match_array['loser_full']. " ". $match_array['score']);
                    adjustClubLadder( $match_array['winner_id'], $match_array['loser_id'], $match_array['ladder_id']);

                    $query = "update tblLadderMatch set processed = TRUE where id  = ".$match_array['ladder_match_id'];
                    db_query($query);
            
                }

                if (isDebugEnabled(1)) logMessage("LadderUpdateService:getJumpLadders ".$ladder_array['id'] . " has ". mysqli_num_rows($result). " results");
                
                // If there are adjustments in the ladder
                if ( mysqli_num_rows($result) > 0 ){

                    // Update last updated
                    if (isDebugEnabled(1)) logMessage("LadderUpdateService:updateJumpLadders. updated lastUpdate for ladder #".$ladder_array['id']);
    
                    $query = "UPDATE tblClubSiteLadders SET lastUpdated = CURRENT_TIMESTAMP WHERE id = ".$ladder_array['id'];
                    db_query($query);

                }
               

        }

    
    }


}

?>
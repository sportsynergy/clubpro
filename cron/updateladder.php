<?php

/*

Schedule this in cron
*/
/*
This script runs every minute to send out the reservation reminders
*/

include ("../application.php");


date_default_timezone_set('GMT');


$service = new LadderUpdateService();
$service->getJumpLadders();

class LadderUpdateService{

    /*
	*
	* Get all jump ladder
	*
	*/
	public function getJumpLadders(){

        if (isDebugEnabled(1)) logMessage("LadderUpdateService:getJumpLadders. Starting...");
        
        $yesterday = date('Y-m-d', time() - 60 * 60 * 24);

        $query = "SELECT tCSL.*
                FROM tblClubSites sites
                INNER JOIN tblClubSiteLadders tCSL on sites.siteid = tCSL.siteid
                WHERE sites.rankingscheme = 'jumpladder'";
	
	  	$result = db_query($query);

        /** For each one, go through each ladder and 
         * Get all scores that were reported for the previous day 
         * */

		while($ladder_array = mysqli_fetch_array($result) ){

            $query = "SELECT
                            winner.firstname AS winner_first,
                            winner.lastname AS winner_last,
                            concat_ws(' ', winner.firstname, winner.lastname) AS winner_full,
                            winner.userid AS winner_id,
                            loser.firstname AS loser_first,
                            loser.lastname AS loser_last,
                            loser.userid AS loser_id,
                            ladder.id, ladder.score, ladder.match_time, ladder.reported_time
                            FROM tblLadderMatch ladder
                            inner join tblUsers winner on ladder.winnerid = winner.userid
                            inner join tblUsers loser on ladder.loserid = loser.userid
                            inner join tblClubSiteLadders tCSL on ladder.ladderid = tCSL.id
                            WHERE ladder.ladderid = ".$ladder_array['id']."
                                AND tCSL.enddate IS NULL
                                AND ladder.enddate IS NULL
                                AND date(ladder.match_time) = '$yesterday'
                            ORDER BY ladder.match_time DESC";

                if (isDebugEnabled(1)) logMessage("LadderUpdateService:getJumpLadders. $query");

                $result = db_query($query);
                while($match_array = mysqli_fetch_array($result) ){

                    if (isDebugEnabled(1)) logMessage("LadderUpdateService:getJumpLadders ".$match_array['winner_full']);

                }

    }
}


/*




2.) For each one, go through each ladder
3.) Get all scores that were reported for the previous day
4.) update ladder for each one, and log it
5.) Update ladder last update time
3
*/


}

?>
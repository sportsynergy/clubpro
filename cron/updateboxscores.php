<?php

/*

Updates the the box league score from the


- Players play each other twice over the course of the season
- They get two points for a win, one for a loss in the first match
- They get one point for a win and zero points for a loss in the rematch
- In case the players split matches, total games won serves as the tiebreak

This can run all time or by a day

*/

include ("../application.php");
include ("../lib/ladderlib.php");

date_default_timezone_set('GMT');

$service = new UpdateBoxLeagueScoresService();
$service->updateScores();

class UpdateBoxLeagueScoresService{

	public function updateScores(){


        /** 
         * 
         * 1.) Get all box leagues with ladders matches
         * 2. For each one make a unique list of players
         * 3.) for each create an object with a list of players they have played
         * 4.) for each player go through each ladder match result and count the scores
         *  */



        if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. Starting...");
        
        $yesterday = date('Y-m-d', time() - 60 * 60 * 24);

        $query = "SELECT tCSL.*
        FROM tblClubSites sites
        INNER JOIN tblClubSiteLadders tCSL on sites.siteid = tCSL.siteid
        WHERE sites.rankingscheme = 'jumpladder'";

    }



}
?>
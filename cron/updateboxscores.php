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

        if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. Starting...");
        
        /* 1.) Get all box leagues with ladders matches */

        $query = "SELECT boxname, ladderid FROM tblBoxLeagues INNER JOIN tblClubSiteLadders tCSL ON tblBoxLeagues.ladderid = tCSL.id";
        $mresult = db_query($query);
        
        while($box_array = mysqli_fetch_array($mresult) ){


            /* 2. For each ladder make a unique list of players */
            $query = "SELECT  winnerid as player FROM tblLadderMatch WHERE enddate IS NULL AND ladderid = ".$box_array['ladderid']." UNION SELECT loserid AS player FROM tblLadderMatch where enddate IS NULL AND ladderid = ".$box_array['ladderid'];
            
            $lmresult = db_query($query);
            $count = mysqli_num_rows($lmresult);

            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. Processing ". $box_array['boxname'] . "(".$box_array['ladderid'].") for $count players"); 
          

            /* 3.) for each player in the ladder figure out the scores   */
            while($lm_player_array = mysqli_fetch_array($lmresult) ){

                if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. Calculating scores for user ". $lm_player_array['player'] ); 

                $query = "SELECT * FROM tblLadderMatch where winnerid = ".$lm_player_array['player']." or loserid = ".$lm_player_array['player'] ." AND ladderid = ".$box_array['ladderid'];

                $lpresult = db_query($query);

                $points = 0;
                $games = 0;
                $gameswon = 0;
                $players = array();

                while($lp_match_array = mysqli_fetch_array($lpresult) ){

                
                    // when the player wins
                    if( $lm_player_array['player'] == $lp_match_array['winnerid']){
                        
                        $pastmatches = array_count_values($players)[$lp_match_array['loserid']] ?? 0;

                        #score
                        if ($lp_match_array['score'] == '3-0'){
                            $wins = 3;
                        } elseif ($lp_match_array['score'] == '2-1') {
                            $wins = 2;
                        } else {
                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. Not a valid score ".$lp_match_array['score']  ); 
                        }

                        if($pastmatches == 0){

                            $games = $games + 1;
                            
                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. Adding 2 points for a win in first match against ".$lp_match_array['loserid'] ); 

                            $gameswon = $wins;
                            $points = $points + 2;

                        } elseif($pastmatches == 1){

                            $games = $games + 1;
                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. Adding 1 points for a win in a rematch against ".$lp_match_array['loserid'] ); 

                            $gameswon = $wins;
                            $points = $points + 1;

                        } else {

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. no scoring for a win after more than 2 matches against ".$lp_match_array['loserid'] ); 
                        }

                        array_push($players, $lp_match_array['loserid']);
                    }

                    // when the player loses
                    if( $lm_player_array['player'] == $lp_match_array['loserid']){
                        
                         #score
                         if ($lp_match_array['score'] == '2-1') {
                            $wins = 1;
                        } 

                        $pastmatches = array_count_values($players)[$lp_match_array['winnerid']] ?? 0;

                        if($pastmatches == 0){

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. Adding 1 point for a loss in first match against ".$lp_match_array['winnerid'] ); 

                            $gameswon = $wins;
                            $points = $points + 1;

                        } elseif($pastmatches == 1){

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. Adding 0 points for a loss in a rematch against ".$lp_match_array['winnerid'] );
                            $gameswon = $wins;

                        } else {

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. no scoring for a loss after more than 2 matches against ".$lp_match_array['winnerid'] );
                        }

                        array_push($players, $lp_match_array['winnerid']);
                    }

                    // Get the box for the player, then update games and score
                    $query = "SELECT boxid FROM tblkpBoxLeagues WHERE userid = ".$lm_player_array['player'];
                    $result = db_query($query);
                    $boxid = mysqli_result($result, 0);

                    if ( isset($boxid) ) {

                         #Set the score and games played
                        $query = "UPDATE tblkpBoxLeagues SET score = $points, games = $games, gameswon = $gameswon WHERE boxid = $boxid AND userid = ".$lm_player_array['player'];
                        $result = db_query($query);

                        if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. points and games updated for boxid $boxid for user ".$lm_player_array['player'] ); 

                    } else {
                        if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. user ".$lm_player_array['player']. " not in $boxid" ); 
                    }

                }
                
                if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. total points for user ".$lm_player_array['player'] . " is: $points and total games is $games and gameswon is $gameswon" ); 
            }
           
            // Update last updated
            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScoresService:updateScores. updated lastUpdate for ladder ".$box_array['ladderid']);
                
            $query = "UPDATE tblClubSiteLadders SET leaguesUpdated = CURRENT_TIMESTAMP WHERE id = ".$box_array['ladderid'];
            db_query($query);

        }

    }



}
?>
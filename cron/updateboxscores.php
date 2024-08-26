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

$service = new UpdateBoxLeagueScores();
$service->updateScores();


class UpdateBoxLeagueScores{

	public function updateScores(){

        if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: Starting...");
        
        /* 1.) Get all box leagues with ladders matches */

        $query = "SELECT boxname, ladderid FROM tblBoxLeagues INNER JOIN tblClubSiteLadders tCSL ON tblBoxLeagues.ladderid = tCSL.id";
        $mresult = db_query($query);
        
        while($box_array = mysqli_fetch_array($mresult) ){


            /* 2. For each ladder make a unique list of players */
       
            $query = "SELECT winnerid as player, concat(tblUsers.firstname, ' ',tblUsers.lastname) AS playername FROM tblLadderMatch lm INNER JOIN tblUsers on winnerid = tblUsers.userid WHERE lm.enddate IS NULL AND ladderid = ".$box_array['ladderid']." UNION SELECT loserid AS playerid, concat(tblUsers.firstname, ' ',tblUsers.lastname) AS playername FROM tblLadderMatch lm INNER JOIN tblUsers on loserid = tblUsers.userid WHERE lm.enddate IS NULL AND ladderid = ".$box_array['ladderid'];
            
            $lmresult = db_query($query);
            $count = mysqli_num_rows($lmresult);

            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: Processing ". $box_array['boxname'] . "(".$box_array['ladderid'].") for $count players"); 
          

            /* 3.) for each player in the ladder figure out the scores   */
            while($lm_player_array = mysqli_fetch_array($lmresult) ){

                if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: ---------------------------------" ); 
                if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: Calculating scores for ". $lm_player_array['playername']." (".$lm_player_array['player'].")" ); 
                if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: ---------------------------------" ); 

                $query = "SELECT tblLadderMatch.id,
                            date(tblLadderMatch.match_time) as match_time,
                            concat(winner.firstname, ' ',winner.lastname) AS winner,
                            tblLadderMatch.winnerid,
                            concat(loser.firstname, ' ',loser.lastname) AS loser,
                            tblLadderMatch.loserid,
                            tblLadderMatch.score 
                        FROM tblLadderMatch
                        INNER JOIN tblUsers winner ON tblLadderMatch.winnerid = winner.userid
                        INNER JOIN tblUsers loser ON tblLadderMatch.loserid = loser.userid
                        WHERE (winnerid = ".$lm_player_array['player'] ." OR loserid = ".$lm_player_array['player'] .") 
                        AND tblLadderMatch.enddate IS NULL
                        AND league = TRUE
                        AND ladderid = ".$box_array['ladderid'];

                $lpresult = db_query($query);

                $points = 0;
                $games = 0;
                $gameswon = 0;
                $players = array();

                while($lp_match_array = mysqli_fetch_array($lpresult) ){

                    // when the player wins
                    if( $lm_player_array['player'] == $lp_match_array['winnerid']){
                        
                        $pastmatches = array_count_values($players)[$lp_match_array['loserid']] ?? 0;

                        $scores = explode("-", $lp_match_array['score']);

                        #score
                        if ($scores[0] == '3'){
                            $wins = 3;
                          
                        } elseif ($scores[0] == '2') {
                            $wins = 2;
                        
                        } else {
                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: Not a valid score ".$lp_match_array['score']  ); 
                        }

                        if($pastmatches == 0){

                            $games = $games + 1;
                            $gameswon = $gameswon + $wins;
                            $points = $points + 2;
                            
                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: Adding 2 points for a win (".$lp_match_array['score'].") in first match against ".$lp_match_array['loser']." on ".$lp_match_array['match_time'] . " and updated gameswon to $gameswon" );   


                        } elseif($pastmatches == 1){

                            $games = $games + 1;
                            $gameswon = $gameswon + $wins;
                            $points = $points + 1;

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: Adding 1 points for a win (".$lp_match_array['score'].") in a rematch against ".$lp_match_array['loser']." on ".$lp_match_array['match_time']. " and updated gameswon to $gameswon" );  


                        } else {

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: no scoring for a win after more than 2 matches against ".$lp_match_array['loser']." on ".$lp_match_array['match_time'] ); 
                        }

                        array_push($players, $lp_match_array['loserid']);
                    }

                    // when the player loses
                    if( $lm_player_array['player'] == $lp_match_array['loserid']){
                        
                        $scores = explode("-", $lp_match_array['score']);

                         #score
                         if ($scores[1]==1 ) {
                            $wins = 1;
                        } elseif($scores[1]==2 ){
                            $wins = 2;
                        }else {
                            $wins = 0;
                        }

                        $pastmatches = array_count_values($players)[$lp_match_array['winnerid']] ?? 0;

                        if($pastmatches == 0){

                            $gameswon = $gameswon + $wins;
                            $points = $points + 1;

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: Adding 1 point for a loss (".$lp_match_array['score'].") in first match against ".$lp_match_array['winner'] ." on ".$lp_match_array['match_time']. " and updated gameswon to $gameswon" );  


                        } elseif($pastmatches == 1){

                            $gameswon = $gameswon + $wins;

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: Adding 0 points for a loss (".$lp_match_array['score'].") in a rematch against ".$lp_match_array['winner'] ." on ".$lp_match_array['match_time']. " and updated gameswon to $gameswon" ); 

                        } else {

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: no scoring for a loss after more than 2 matches against ".$lp_match_array['winnerid'] ." on ".$lp_match_array['match_time'] ); 
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

                        if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: Total points for ".$lm_player_array['playername'] . " is: $points and total games is $gameswon and gameswon is $gameswon" ); 

                    } else {
                        if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: ".$lm_player_array['playername']. " not in $boxid" ); 
                    }
                }
                
                
            }
           
            // Update last updated
            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores: updated lastUpdate for ladder ".$box_array['ladderid']);
                
            $query = "UPDATE tblClubSiteLadders SET leaguesUpdated = CURRENT_TIMESTAMP WHERE id = ".$box_array['ladderid'];
            db_query($query);

        }

    }



}
?>
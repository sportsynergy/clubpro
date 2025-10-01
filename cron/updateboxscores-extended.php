<?php

/*

Updates the the box league score


- Players play each other twice over the course of the season
- They get two points for a win, one for a loss in the first match
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

        if (isDebugEnabled(2)) logMessage("UpdateBoxLeagueScores(e): Starting...");
        
        /* 1.) Get all box leagues with ladders matches */

        $query = "SELECT boxname, ladderid, startdate, boxid FROM tblBoxLeagues 
                    INNER JOIN tblClubSiteLadders tCSL ON tblBoxLeagues.ladderid = tCSL.id
                    WHERE startdate IS NOT NULL
                    AND  tblBoxLeagues.ladder_type = 'extended'
                    AND enable=TRUE";

        $mresult = db_query($query);
        
        while($box_array = mysqli_fetch_array($mresult) ){

            // Reset the points to start
            $query = "UPDATE tblkpBoxLeagues SET games = 0, score = 0, gameswon = 0, totalgameswon = 0, totalscore = 0 WHERE boxid = ".$box_array['boxid'];
            db_query($query);

            /* 2. For each ladder make a unique list of players */
       
            $query = "SELECT winnerid as player, concat(tblUsers.firstname, ' ',tblUsers.lastname) AS playername    
                FROM tblLadderMatch lm 
                    INNER JOIN tblUsers on winnerid = tblUsers.userid 
                    WHERE lm.enddate IS NULL 
                    AND ladderid = ".$box_array['ladderid']." 
                    UNION 
                    SELECT loserid AS playerid, concat(tblUsers.firstname, ' ',tblUsers.lastname) AS playername FROM tblLadderMatch lm INNER JOIN tblUsers on loserid = tblUsers.userid WHERE lm.enddate IS NULL AND ladderid = ".$box_array['ladderid'];
            
            $lmresult = db_query($query);
            $count = mysqli_num_rows($lmresult);

            if (isDebugEnabled(2)) logMessage("UpdateBoxLeagueScores(e): Processing ". $box_array['boxname'] . "(".$box_array['ladderid'].") for $count players"); 
          
            /* 3.) for each player in the ladder figure out the scores   */
            while($lm_player_array = mysqli_fetch_array($lmresult) ){

                if (isDebugEnabled(2)) logMessage("UpdateBoxLeagueScores(e): ---------------------------------" ); 
                if (isDebugEnabled(2)) logMessage("UpdateBoxLeagueScores(e): Calculating scores for ". $lm_player_array['playername']." (".$lm_player_array['player'].") since ". $box_array['startdate'] ); 
                if (isDebugEnabled(2)) logMessage("UpdateBoxLeagueScores(e): ---------------------------------" ); 

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
                        AND match_time >= '".$box_array['startdate']."'
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
                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores(e): Not a valid score ".$lp_match_array['score']  ); 
                        }

                        if($pastmatches == 0){

                            $games = $games + 1;
                            $gameswon = $gameswon + $wins;
                            $points = $points + 2;
                            
                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores(e): Adding 2 points for a win (".$lp_match_array['score'].") in match against ".$lp_match_array['loser']." on ".$lp_match_array['match_time'] . " and updated gameswon to $gameswon" );   

                        } else {

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores(e): no scoring for a win after the first matche against ".$lp_match_array['loser']." on ".$lp_match_array['match_time'] ); 
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

                            $games = $games + 1;
                            $gameswon = $gameswon + $wins;
                            $points = $points + 1;

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores(e): Adding 1 point for a loss (".$lp_match_array['score'].") in first match against ".$lp_match_array['winner'] ." on ".$lp_match_array['match_time']. " and updated gameswon to $gameswon" );  


                        } else {

                            if (isDebugEnabled(1)) logMessage("UpdateBoxLeagueScores(e): no scoring for a loss after the first match against ".$lp_match_array['winnerid'] ." on ".$lp_match_array['match_time'] ); 
                        }

                        array_push($players, $lp_match_array['winnerid']);
                    }

                   
               
                #Set the score and games played
                $query = "UPDATE tblkpBoxLeagues SET score = $points, games = $games, gameswon = $gameswon WHERE boxid = ".$box_array['boxid']." AND userid = ".$lm_player_array['player'];
                $result = db_query($query);


                if (isDebugEnabled(2)) logMessage("UpdateBoxLeagueScores(e): Total points for ".$lm_player_array['playername'] . " is: $points and total games is $games and gameswon is $gameswon  for box ".$box_array['boxid'].".  " ); 

                }

                 // Get all scores from the past (since the box session started)
                    $totalscorequery = "SELECT sum(score) as 'totalscore', sum(games) as 'totalgames', sum(gameswon) as 'totalgameswon'
                                    FROM tblkpBoxLeagues
                                    INNER JOIN tblBoxLeagues ON tblkpBoxLeagues.boxid = tblBoxLeagues.boxid
                                    INNER JOIN tblClubSites ON tblBoxLeagues.siteid = tblClubSites.siteid
                                    WHERE userid = ".$lm_player_array['player']."
                                    AND tblBoxLeagues.ladderid = ".$box_array['ladderid']."
                                    AND tblBoxLeagues.startdate >= tblClubSites.boxsession";

                    $tsresult = db_query($totalscorequery);
                    $tsrow = mysqli_fetch_array($tsresult);
                    $totalscore = $tsrow['totalscore'] ?? 0;
                    $totalgameswon = $tsrow['totalgameswon'] ?? 0;
                    
                     #Set the totalgameswon and games won
                    $query = "UPDATE tblkpBoxLeagues SET totalscore = $totalscore, totalgameswon = $totalgameswon WHERE boxid = ".$box_array['boxid']." AND userid = ".$lm_player_array['player'];
                    $result = db_query($query);

                    if (isDebugEnabled(2)) logMessage("UpdateBoxLeagueScores(e): Grand total games won for the season is $totalgameswon and totalscore is $totalscore  for box ".$box_array['boxid'].".  for player ".$lm_player_array['player'] ); 
                    
                
            }
           
            // Update last updated
            if (isDebugEnabled(2)) logMessage("UpdateBoxLeagueScores: updated lastUpdate for ladder ".$box_array['ladderid']);
                
            $query = "UPDATE tblClubSiteLadders SET leaguesUpdated = CURRENT_TIMESTAMP WHERE id = ".$box_array['ladderid'];
            db_query($query);

        }

    }

   

}


?>
<?php

/*

Updates the the box league schedule


- for a given box league go through the prevously recorded scores and schedule matches for
- people who have auto schedule set to on in their profile
- between two people who haven't played more than twice.

*/

include ("../application.php");
include ("../lib/ladderlib.php");

date_default_timezone_set('GMT');

$service = new CreateBoxLeagueSchedule();
$service->createSchedule();



class CreateBoxLeagueSchedule {

    public function createSchedule(){

        if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: Starting...");
        
        // Go through each ladder 
        $query = "SELECT boxname, ladderid, boxid FROM tblBoxLeagues 
                    INNER JOIN tblClubSiteLadders tCSL 
                        ON tblBoxLeagues.ladderid = tCSL.id";
        $mresult = db_query($query);
       
        while($box_array = mysqli_fetch_array($mresult) ){

            if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: Processing ". $box_array['boxname'] . "(".$box_array['ladderid'].") "); 
        
            // For each person in the league, get the number of matches player for other players
            $box_query = "SELECT concat(tU.firstname,' ',tU.lastname) as full_name, tU.userid
                            FROM tblkpBoxLeagues
                            INNER JOIN clubpro_main.tblUsers tU on tblkpBoxLeagues.userid = tU.userid
                            WHERE boxid = ".$box_array['boxid']."
                            ORDER BY rand()";
            $bresult = db_query($box_query);
            
            $all_players = array();
            

            while($boxplayer_array = mysqli_fetch_array($bresult) ){
                //put all userids into an array
                array_push($all_players, $boxplayer_array['userid']);
            }

            mysqli_data_seek($bresult,0);
            while($boxplayer_array = mysqli_fetch_array($bresult) ){

                if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: Getting results for ". $boxplayer_array['full_name'] ); 
                
                $already_played = array();

                // add the players this person has beat to the already played array
                $loser_query = "SELECT loserid FROM tblLadderMatch WHERE league = TRUE AND winnerid = 15272";
                $loser_result = db_query($loser_query);
                while($loserid_array = mysqli_fetch_array($loser_result) ){
                    array_push($already_played, $loserid_array['loserid']);
                }

                // add the players this person has lost to to the already played array
                $winner_query = "SELECT winnerid FROM tblLadderMatch WHERE league = TRUE AND loserid = 15272";
                $winner_result = db_query($loser_query);
                while($loserid_array = mysqli_fetch_array($loser_result) ){
                    array_push($already_played, $loserid_array['winnerid']);
                }

                $havent_played = array_diff($all_players, $already_played);
                foreach ($havent_played as &$playerid) {
                    if (isDebugEnabled(1)) logMessage("\tStill needs to play $playerid" ); 
    
                }

            }

           
            

            
            //order of scheduling prioritizing players who haven't played first

        }   
    }

}

?>
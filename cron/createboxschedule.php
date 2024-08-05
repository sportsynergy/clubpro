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

    private function clearScheduledMatches(){

        if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: Resetting schedule");
        $query = "TRUNCATE tblBoxLeagueSchedule";
        db_query($query);

    }

    public function createSchedule(){

        if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: Starting...");
        
        // Clear out the old schedule
         $this->clearScheduledMatches();
        
        // Go through each ladder 
        $query = "SELECT boxname, ladderid, boxid FROM tblBoxLeagues 
                    INNER JOIN tblClubSiteLadders tCSL 
                        ON tblBoxLeagues.ladderid = tCSL.id WHERE boxid = 922"; // BE SURE TO TAKE THIS OUT!!!
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
            $scheduled = array();

            while($boxplayer_array = mysqli_fetch_array($bresult) ){
                //put all userids into an array
                array_push($all_players, $boxplayer_array['userid']);
            }

            mysqli_data_seek($bresult,0);
            while($boxplayer_array = mysqli_fetch_array($bresult) ){

                if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: Getting results for ". $boxplayer_array['full_name']." (". $boxplayer_array['userid'].")" ); 
                $already_played = array();

                // check to see if this player is already scheduled
                if( in_array( $boxplayer_array['userid'], $scheduled) ){
                    if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: ". $boxplayer_array['userid'] ." is already scheduled to play" ); 
                    continue;
                }

                // add the players this person has lost to the already played array
                $loser_query = "SELECT loserid FROM tblLadderMatch WHERE league = TRUE AND winnerid = ".$boxplayer_array['userid'];
                $loser_result = db_query($loser_query);
                while($loserid_array = mysqli_fetch_array($loser_result) ){
                    array_push($already_played, $loserid_array['loserid']);
                }

                // add the players this person has beat to to the already played array
                $winner_query = "SELECT winnerid FROM tblLadderMatch WHERE league = TRUE AND loserid = ".$boxplayer_array['userid'];
                $winner_result = db_query($loser_query);
                while($loserid_array = mysqli_fetch_array($loser_result) ){
                    array_push($already_played, $loserid_array['winnerid']);
                }

                $havent_played = array_diff($all_players, $already_played);
                foreach ($havent_played as &$playerid) {
                    
                     //can't play yourself
                     if( $boxplayer_array['userid'] == $playerid){
                        continue;
                    }

                    if (isDebugEnabled(1)) logMessage("\tChecking ". $boxplayer_array['userid']." and $playerid" ); 
                    
                    //take the first one that hasn't been scheduled
                    if( in_array($playerid, $scheduled) ){
                        // do nothing
                        //if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: ". $playerid ." is already scheduled to play" ); 
                    } else{
                        // if they haven't been scheduled, schedule them and insert into database
                        array_push($scheduled, $playerid, $boxplayer_array['userid']);
                        if (isDebugEnabled(1)) logMessage("\tScheduling $playerid to play ".$boxplayer_array['userid'] ); 
                        $this->schedulePlayers($boxplayer_array['userid'], $playerid,$box_array['boxid'] );
                        break;
                    }
                }

            }

            if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: Here is the number of people scheduled: ". count($scheduled) ); 

        }   
    }

    private function schedulePlayers($userid1, $userid2, $boxid){

        if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: scheduling players $userid1, $userid2 for box $boxid" );

        $query = "INSERT INTO tblBoxLeagueSchedule (
            boxid, userid1, userid2
            ) VALUES (
                      $boxid
                      ,'$userid1'
                      ,$userid2
                      
                      )";
        
        db_query($query);
    }
}

?>
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
            $box_query = "SELECT concat(tU.firstname,' ',tU.lastname) as full_name
                            FROM tblkpBoxLeagues
                            INNER JOIN clubpro_main.tblUsers tU on tblkpBoxLeagues.userid = tU.userid
                            WHERE boxid = ".$box_array['boxid'];
            $bresult = db_query($box_query);
            
            while($boxplayer_array = mysqli_fetch_array($bresult) ){

                if (isDebugEnabled(1)) logMessage("CreateBoxLeagueSchedule: Getting results for ". $boxplayer_array['full_name'] ); 

                //put all userids into an array
        
            }

            // look through ladder matches that are box leagues, (need to add a column for this on laddermatch table to indicate that it was a box league match)
            
            //order of scheduling prioritizing players who haven't played first

        }   
    }

}

?>
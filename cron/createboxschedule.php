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
        
        // For each person in the league, get the number of matches player for other players

    }
}
?>
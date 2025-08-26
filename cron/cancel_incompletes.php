<?php

/*

For clubs with the autocancel feature enabled, this script runs every hour to cancel incomplete reservations

*/

include ("../application.php");


$service = new CancelIncompleteReservationService();
$service->cancelReservations();

class CancelIncompleteReservationService{

    public function cancelReservations(){

         if (isDebugEnabled(1)) logMessage("CancelIncompleteReservationService:cancelReservations. Starting up. Processing incomplete reservations");

         // Get clubs with autocancel enabled
         $query = "SELECT siteid, clubid, autocancelincompletes
                    FROM tblClubSites
                    WHERE autocancelincompletes IN ('1', '4', '12');";

        $clubs_result = db_query($query);

        // if no clubs found log and exit
        if(mysqli_num_rows($clubs_result) == 0){
            if (isDebugEnabled(1)) logMessage("CancelIncompleteReservationService:cancelReservations. No clubs found with autocancel enabled. Exiting.");
            return;
        }

        while($club_array = mysqli_fetch_array($clubs_result) ){

            $siteid = $club_array['siteid'];
            $clubid = $club_array['clubid'];
            $autocancel_time = $club_array['autocancelincompletes'] * 3600; // convert hours to seconds

            //Get General Club info
            $clubquery = "SELECT * from tblClubs WHERE clubid='" . $clubid . "'";
            $clubresult = db_query($clubquery);
            $clubobj = db_fetch_object($clubresult);
            $tzdelta = $clubobj->timezone * 3600;
            $isDST = date("I");

            // Set timezone name based on offset and whether DST is in effect
            $timezoneName = timezone_name_from_abbr("", $tzdelta, $isDST);
            if($timezoneName === false) {
                // Fallback for when timezone_name_from_abbr fails
                $timezoneName = 'UTC';
            } 

            $date = new DateTime('now', new DateTimeZone($timezoneName));
            $currHour = $date->format('H');
            $currYear = $date->format('Y');
            $currMonth = $date->format('n');
            $currDay = $date->format('j'); 
            $currMinute = $date->format('i'); 
            
            $i = gmmktime($currHour,$currMinute,0,$currMonth,$currDay,$currYear);
            $eventtime =date("M d Y H:i:s", $i + $autocancel_time);

             if (isDebugEnabled(1)) logMessage("CancelIncompleteReservationService:cancelReservations. Processing club ID: $siteid and events that are scheduled for: $eventtime");
        
            // get events that have a player limit and are not full
            $eventquery = "SELECT tR.reservationid, tE.eventname, tE.playerlimit, count(tCEP.reservationid) AS signedup
                FROM tblReservations tR
                INNER JOIN tblEvents tE on tR.eventid = tE.eventid
                INNER JOIN tblCourts tC on tR.courtid = tC.courtid
                LEFT JOIN tblCourtEventParticipants tCEP on tR.reservationid = tCEP.reservationid
                WHERE tR.time = $i + $autocancel_time
                AND tC.siteid = $siteid
                AND tR.enddate is null
                GROUP BY tR.reservationid;";  

            $eventresult = db_query($eventquery);

            // if no events found log and exit  
            if(mysqli_num_rows($eventresult) == 0){
                if (isDebugEnabled(1)) logMessage("CancelIncompleteReservationService:cancelReservations. No events found for club ID: $siteid at time: $eventtime. Exiting.");
                return; 
            }

            while($event_array = mysqli_fetch_array($eventresult) ){

                $reservationid = $event_array['reservationid'];
                $eventname = $event_array['eventname'];
                $playerlimit = $event_array['playerlimit'];
                $signedup = $event_array['signedup'];

                if (isDebugEnabled(1)) logMessage("CancelIncompleteReservationService:cancelReservations. Found event: $eventname, reservationid: $reservationid, playerlimit: $playerlimit, signedup: $signedup");

                if($playerlimit > 0 && $signedup < $playerlimit){

                    // update the enddate of the reservation to now
                    $reservationquery = "UPDATE tblReservations 
                        SET enddate = now() WHERE reservationid='" . $reservationid . "'";
                    db_query($reservationquery);
                    if (isDebugEnabled(1)) logMessage("CancelIncompleteReservationService:cancelReservations. Updated enddate of reservation ID: $reservationid to now()");
                }
                else{
                    if (isDebugEnabled(1)) logMessage("CancelIncompleteReservationService:cancelReservations. Event: $eventname, reservationid: $reservationid is full or has no player limit. No action taken.");
                    }
                   
        }
        
    }
}

}


?>
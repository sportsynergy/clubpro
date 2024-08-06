<?

/*
This script runs every minute to send out the reservation reminders
*/

include ("../application.php");
require '../vendor/autoload.php';

date_default_timezone_set('GMT');

$service = new ReminderService();

$service->sendReminders();


class LeagueReminderService{
	
	
	/*
	*
	* This is for sending out reminders at a specific time of day
	*
	*/
	public function sendReminders(){
		
			
		$query = "SELECT concat(tU1.firstname, ' ',tU1.lastname) AS player1,
                        tU1.email AS email1,
                        tU1.userid,
                        tC1.recleaguematchnotifications as rec1,
                        concat(tU2.firstname, ' ',tU2.lastname) AS player2,
                        tU2.email AS email2,
                        tU2.userid,
                        tC2.recleaguematchnotifications AS rec2,
                        tBL.boxname
                    FROM tblBoxLeagueSchedule
                    INNER JOIN tblBoxLeagues tBL on tblBoxLeagueSchedule.boxid = tBL.boxid
                    INNER JOIN tblUsers tU1 on tblBoxLeagueSchedule.userid1 = tU1.userid
                    INNER JOIN tblClubUser tC1 on tU1.userid = tC1.userid
                    INNER JOIN tblUsers tU2 on tblBoxLeagueSchedule.userid2 = tU2.userid
                    INNER JOIN tblClubUser tC2 on tU2.userid = tC2.userid";
	
	  	$result = db_query($query);
		while($player_array = mysqli_fetch_array($result) ){
				
			if (isDebugEnabled(1)) logMessage("send-reminder.checkTimedSchedule:  found ". mysqli_num_rows($res_result) ." reservations");
					
            //only send to people who want these emails
            if( $player_array['rec1']=='y' ){

                sendReminder($email, $firstname, $boxname, $otherguy);
            }
                

						
						
				}
			} 		
									
		}
						
						
	}
	

/*
*
* Sends reminders for doubles and singles
*
*/
private function sendReminder($email, $firstname, $boxname, $otherguy){
	
            // matchtype, courtname, time, otherguy
        $var = new clubpro_obj;
        $var->matchtype = $matchtype;
        $var->courtname = $courtname;
        $var->time = gmdate("l F j g:i a", $time);
        $var->otherguy = $player_two['firstname']." ". $player_two['lastname'];

        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/singles_reminder.php", $var);
        $emailbody = nl2br($emailbody);	

        $subject = "$clubname - Match Reminder";
        
        $content = new clubpro_obj;
        $content->line1 = $emailbody;
        $content->clubname = $clubname;
        
        $to_emails = array();
        $to_email = $player_one['email'];
        $to_emails[$to_email] = array(
            'name' => $player_one['firstname']
        );

        //Send email to player one
        send_email($subject, $to_emails, $content, "Singles Reminders");
        
        //Send email to player two
        $var->otherguy = $player_one['firstname']." ". $player_one['lastname'];	
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/singles_reminder.php", $var);
        $emailbody = nl2br($emailbody);	
        $content->line1 = $emailbody;
        
        $to_emails = array();
        $to_email = $player_two['email'];
        $to_emails[$to_email] = array(
            'name' => $player_two['firstname']
        );
            
        send_email($subject, $to_emails, $content, "Singles Reminders");
		
	
}
	



/*
*
* This is just for sending out singles reminders
*
*/
private function sendSinglesReminder($reservationid, $matchtype, $courtname, $time, $clubname){
	
	if (isDebugEnabled(1)) logMessage("send-reminder.sendSinglesReminder: sending out a singles reminder for $clubname");
			
	$singles_query = "SELECT users.firstname, users.lastname, users.email 
						FROM tblkpUserReservations details
						INNER JOIN tblUsers users ON details.userid = users.userid
						WHERE reservationid = $reservationid
						AND details.userid != 0";
						
	$singles_result = db_query($singles_query);
	
	if(mysqli_num_rows($singles_result) != 2 ){
		if (isDebugEnabled(1)) logMessage("send-reminder: $reservationid is not a full singles reservations, skipping");
		return;
	}
	
	$player_one = mysqli_fetch_array($singles_result);
	$player_two = mysqli_fetch_array($singles_result);
	
	
}

/*
*
* This is reminders for events (for the whole club)
*
*/
private function sendEventReminder($reservationid, $eventname, $courtname, $time, $clubname, $courttypeid, $clubid){

	if (isDebugEnabled(1)) logMessage("send-reminder.sendEventReminder: sending out an event reminder for $clubname");
	
	$emailidquery = "SELECT  users.firstname, users.lastname, users.email
					   FROM tblUsers users
						INNER JOIN tblUserRankings rankings ON rankings.userid = users.userid
						INNER JOIN tblClubUser clubuser ON users.userid = clubuser.userid
					   WHERE users.userid = rankings.userid
					   AND users.userid = clubuser.userid
					   AND clubuser.recemail='y'
					   AND clubuser.clubid = $clubid
					   AND rankings.courttypeid=$courttypeid
					   AND rankings.usertype = 0
					   AND clubuser.enable= 'y'
					   AND clubuser.enddate IS NULL";

	$event_result = db_query($emailidquery);

	 $to_emails = array();
	 while ($emailidarray = mysqli_fetch_array($event_result)) {

	 	if( !empty($emailidarray['firstname']) 
	 		&& !empty($emailidarray['lastname']) 
	 		&& !empty($emailidarray['email'])){

				$to_email = $emailidarray['email'];
	            $to_emails[$to_email] = array(
	                'name' => $emailidarray['firstname']
	            );
			} else {
				if (isDebugEnabled(1)) logMessage("send-reminder.sendEventReminder: incomplete account for ".$emailidarray['firstname']." ".$emailidarray['lastname']." ".$emailidarray['email']);
			}

	 }
	$var = new clubpro_obj;
	$var->eventname = $eventname;
	$var->courtname = $courtname;
	$var->time = gmdate("l F j g:i a", $time);

	$emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/event_reminder.php", $var);
	$emailbody = nl2br($emailbody);	

	$content = new clubpro_obj;
	$content->line1 = $emailbody;
	$content->clubname = $clubname;
	$subject = "$clubname - There are still spots available for the $eventname";

	//Send the email
    send_email($subject, $to_emails, $content, "Event Reminders");


}

}


?>
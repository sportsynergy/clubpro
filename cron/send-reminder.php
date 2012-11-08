<?

/*
This script runs every minute to send out the reservation reminders
*/

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/sendgrid-php/SendGrid_loader.php");
date_default_timezone_set('GMT');

$service = new ReminderService();

$service->checkFor24HoursAhead();
$service->checkTimedSchedule();




class ReminderService{
	
	
	public function checkTimedSchedule(){
		
		if (isDebugEnabled(1)) logMessage("send-reminder.checkTimedSchedule: checking all reservations in the next 24 hour window");
		
		$query = "SELECT sites.siteid, sites.sitename, sites.clubid, clubs.clubname, clubs.timezone, sites.reminders
						FROM tblClubSites sites
						INNER JOIN tblClubs clubs ON sites.clubid =  clubs.clubid
						WHERE sites.reminders IN ('5','6','7','8','9','10')";
	
	  	$result = db_query($query);
		while($sites_array = mysql_fetch_array($result) ){
				
			$siteprefs = getSitePreferences($sites_array['siteid']);
			$_SESSION["siteprefs"] = $siteprefs;
						
			$tzdelta = $sites_array['timezone'] * 3600;
			$curtime = mktime() + $tzdelta;
						
			$current_hour = gmdate("G", $curtime);
			$current_minute = gmdate("i", $curtime);
						
			if( $current_hour == $sites_array['reminders'] && $current_minute == "00"){
							
				//Get all of the reservations for the next 24 hours
				$in24hours = $curtime + (60*60*24);
				
				
				$reservations = "SELECT reservations.reservationid, 
									reservations.usertype, 
									courts.courtname, 
									reservations.time, 
									matchtype.name
							 	FROM tblReservations reservations
									INNER JOIN tblCourts courts ON courts.courtid = reservations.courtid
									INNER JOIN tblMatchType matchtype ON matchtype.id = reservations.matchtype
								WHERE reservations.time >= $curtime 
									AND reservations.time <= $in24hours
									AND reservations.enddate IS NULL
									AND courts.siteid = ".$sites_array['siteid']."
									AND reservations.guesttype = 0
									AND reservations.eventid = 0";

					$res_result = db_query($reservations);

						if( mysql_num_rows($res_result) == 0 ){
								continue;
						}
						
						while( $res_array = mysql_fetch_array($res_result ) ){

							$this->sendReminder($res_array['usertype'],
										 $res_array['reservationid'],
										 $res_array['name'],
										 $res_array['courtname'],
										 $res_array['time'],
										 $res_array['clubname']);
						}	
				
			} 		
						
						
		}
						
		unset($_SESSION["siteprefs"]);	
						
	}
	
	public function checkFor24HoursAhead(){

		if (isDebugEnabled(1)) logMessage("send-reminder.checkFor24HoursAhead: checking for reservations 24 hours ahead");

	    // for each club site look up the setting
	    $query = "SELECT sites.siteid, sites.sitename, sites.clubid, clubs.clubname, clubs.timezone
					FROM tblClubSites sites
					INNER JOIN tblClubs clubs ON sites.clubid =  clubs.clubid
					WHERE reminders = '24'";

		$result = db_query($query);
		while($sites_array = mysql_fetch_array($result) ){

			$siteprefs = getSitePreferences($sites_array['siteid']);
			$_SESSION["siteprefs"] = $siteprefs;
			
			$tzdelta = $sites_array['timezone'] * 3600;
			$curtime = mktime() + $tzdelta;
			
			$in24hours = $curtime + (60*60*24);

				if (isDebugEnabled(1)) logMessage("send-reminder.checkFor24HoursAhead: target time $in24hours");

			// Get the reservations 
			$reservations = "SELECT reservations.reservationid, 
									reservations.usertype, 
									courts.courtname, 
									reservations.time, 
									matchtype.name
								FROM tblReservations reservations
									INNER JOIN tblCourts courts ON courts.courtid = reservations.courtid
									INNER JOIN tblMatchType matchtype ON matchtype.id = reservations.matchtype
								WHERE reservations.time = $in24hours 
									AND reservations.enddate IS NULL
									AND courts.siteid = ".$sites_array['siteid']."
									AND reservations.guesttype = 0
									AND reservations.eventid = 0
									AND reservations.lastmodified > TIMESTAMPADD(HOUR,-2,NOW())";

			$res_result = db_query($reservations);

			if( mysql_num_rows($res_result) == 0 ){
				continue;
			}

			while( $res_array = mysql_fetch_array($res_result ) ){
				
				$this->sendReminder($res_array['usertype'],
						 $res_array['reservationid'],
						 $res_array['name'],
						 $res_array['courtname'],
						 $res_array['time'],
						 $sites_array['clubname']);
			}
			
		}

		unset($_SESSION["siteprefs"]);

	}


private function sendReminder($usertype, $reservationid, $matchtype, $courtname, $time, $clubname){
	
		// For singles reservations
		if( $usertype == 0 ){

			$this->sendSinglesReminder($reservationid, 
								$matchtype, 
								$courtname, 
								$time, 
								$clubname);

		} 
		// Send email for the doubles
		elseif( $usertype == 1 ){

			$this->sendDoublesReminder($reservationid,
								$matchtype, 
								$courtname, 
								$time, 
								$clubname);

	}
	
}
	
private function sendDoublesReminder($reservationid, $matchtype, $courtname, $time, $clubname){
	
	if (isDebugEnabled(1)) logMessage("send-reminder.sendDoublesReminder: sending out a doubles reminder");
	
	$doubles_query = "SELECT users.firstname, users.lastname, users.email 
							FROM tblkpUserReservations details
							INNER JOIN tblkpTeams teams ON details.userid = teams.teamid
							INNER JOIN tblUsers users ON teams.userid = users.userid
							WHERE reservationid = $reservationid
							AND details.userid != 0
							AND details.usertype = 1";
							
	$doubles_result = db_query($doubles_query);

	if(mysql_num_rows($doubles_result) != 4 ){
		if (isDebugEnabled(1)) logMessage("send-reminder: $reservationid is not a full doubles reservation, skipping");
		return;
	}
		
	$player_one = mysql_fetch_array($doubles_result);
	$player_two = mysql_fetch_array($doubles_result);
	$player_three = mysql_fetch_array($doubles_result);
	$player_four = mysql_fetch_array($doubles_result);
		
	// partner, matchtype, otherguy1, otherguy2, courtname, time
	$var = new Object;
	$var->matchtype = $matchtype;
	$var->courtname = $courtname;
	$var->time = gmdate("l F j g:i a", $time); 
		
	//send email to player 1
	$var->partner = $player_two['firstname']." ".$player_two['lastname'];
	$var->otherguy1 = $player_three['firstname']." ".$player_three['lastname'];
	$var->otherguy2 = $player_four['firstname']." ".$player_four['lastname'];
		
	$emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/doubles_reminder.php", $var);
	$emailbody = nl2br($emailbody);	
	
	$subject = "$clubname - Match Reminder";
		
	$content = new Object;
	$content->line1 = $emailbody;
	$content->clubname = $clubname;
	
	$to_emails = array();
	$to_email = $player_one['firstname']." ".$player_one['lastname']." <".$player_one['email'].">";
    $to_emails[$to_email] = array(
            'name' => $player_one['firstname']
     );

	sendgrid_email($subject, $to_emails, $content, "Doubles Reminders");
		
	//send email to player 2
	$var->partner = $player_one['firstname']." ".$player_one['lastname'];
	
	$emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/doubles_reminder.php", $var);
	$emailbody = nl2br($emailbody);
	$content->line1 = $emailbody;
	
	$to_emails = array();
	$to_email = $player_two['firstname']." ".$player_two['lastname']." <".$player_two['email'].">";
    $to_emails[$to_email] = array(
            'name' => $player_two['firstname']
     );
		
	sendgrid_email($subject, $to_emails, $content, "Doubles Reminders");
		
	//send email to player 3
	$var->partner = $player_four['firstname']." ".$player_four['lastname'];
	$var->otherguy1 = $player_one['firstname']." ".$player_one['lastname'];
	$var->otherguy2 = $player_two['firstname']." ".$player_two['lastname'];
		
	$emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/doubles_reminder.php", $var);
	$emailbody = nl2br($emailbody);
	$content->line1 = $emailbody;
	
	$to_emails = array();
	$to_email = $player_three['firstname']." ".$player_three['lastname']." <".$player_three['email'].">";
    $to_emails[$to_email] = array(
        'name' => $player_three['firstname']
     );

	sendgrid_email($subject, $to_emails, $content, "Doubles Reminders");
		
	//send email to player 4
	$var->partner = $player_three['firstname']." ".$player_three['lastname'];
	$emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/doubles_reminder.php", $var);
	$emailbody = nl2br($emailbody);
	$content->line1 = $emailbody;
		
	$to_emails = array();
	$to_email = $player_four['firstname']." ".$player_four['lastname']." <".$player_four['email'].">";
    $to_emails[$to_email] = array(
          'name' => $player_four['firstname']
      );

	sendgrid_email($subject, $to_emails, $content, "Doubles Reminders");
}


private function sendSinglesReminder($reservationid, $matchtype, $courtname, $time, $clubname){
	
	if (isDebugEnabled(1)) logMessage("send-reminder.sendSinglesReminder: sending out a singles reminder");
			
	$singles_query = "SELECT users.firstname, users.lastname, users.email 
						FROM tblkpUserReservations details
						INNER JOIN tblUsers users ON details.userid = users.userid
						WHERE reservationid = $reservationid
						AND details.userid != 0";
						
	$singles_result = db_query($singles_query);
	
	if(mysql_num_rows($singles_result) != 2 ){
		if (isDebugEnabled(1)) logMessage("send-reminder: $reservationid is not a full singles reservations, skipping");
		return;
	}
	
	$player_one = mysql_fetch_array($singles_result);
	$player_two = mysql_fetch_array($singles_result);
	
	// matchtype, courtname, time, otherguy
	$var = new Object;
	$var->matchtype = $matchtype;
	$var->courtname = $courtname;
	$var->time = gmdate("l F j g:i a", $time);
	$var->otherguy = $player_two['firstname']." ". $player_two['lastname'];

	$emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/singles_reminder.php", $var);
    $emailbody = nl2br($emailbody);	

	$subject = "$clubname - Match Reminder";
	
	$content = new Object;
    $content->line1 = $emailbody;
    $content->clubname = $clubname;
	
	$to_emails = array();
	$to_email = $player_one['firstname']." ".$player_one['lastname']." <".$player_one['email'].">";
    $to_emails[$to_email] = array(
        'name' => $player_one['firstname']
    );

	//Send email to player one
	sendgrid_email($subject, $to_emails, $content, "Singles Reminders");
	
	//Send email to player two
	$var->otherguy = $player_one['firstname']." ". $player_one['lastname'];	
	$emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/singles_reminder.php", $var);
	$emailbody = nl2br($emailbody);	
	$content->line1 = $emailbody;
	
	$to_emails = array();
	$to_email = $player_two['firstname']." ".$player_two['lastname']." <".$player_two['email'].">";
    $to_emails[$to_email] = array(
        'name' => $player_two['firstname']
    );
		
	sendgrid_email($subject, $to_emails, $content, "Singles Reminders");
}



}


?>
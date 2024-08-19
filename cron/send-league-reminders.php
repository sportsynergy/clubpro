<?

/*
This script runs every minute to send out the reservation reminders
*/

include ("../application.php");
require '../vendor/autoload.php';

date_default_timezone_set('GMT');

$service = new LeagueReminderService();
$service->sendReminders();

class LeagueReminderService{
	
	public function sendReminders(){
			
		$query = "SELECT tU1.firstname AS firstname1,
                    tU1.lastname AS lastname1,
                    tU1.email AS email1,
                    tU1.userid,
                    tC1.recleaguematchnotifications AS rec1,
                    tU2.firstname AS firstname2,
                    tU2.lastname AS lastname2,
                    tU2.email AS email2,
                    tU2.userid,
                    tC2.recleaguematchnotifications AS rec2,
                    tBL.boxname,
                    tCL.clubname,
                    tCS.sitecode,
                    tCS.siteid,
                    tblBoxLeagueSchedule.scored
                FROM tblBoxLeagueSchedule
                INNER JOIN tblBoxLeagues tBL on tblBoxLeagueSchedule.boxid = tBL.boxid
                INNER JOIN tblUsers tU1 on tblBoxLeagueSchedule.userid1 = tU1.userid
                INNER JOIN tblClubUser tC1 on tU1.userid = tC1.userid
                INNER JOIN tblUsers tU2 on tblBoxLeagueSchedule.userid2 = tU2.userid
                INNER JOIN tblClubUser tC2 on tU2.userid = tC2.userid
                INNER JOIN tblClubSites tCS on tBL.siteid = tCS.siteid
                INNER JOIN tblClubs tCL on tCS.clubid = tCL.clubid";
	
	  	$result = db_query($query);
        if (isDebugEnabled(1)) logMessage("LeagueReminderService.sendReminders: Found ". mysqli_num_rows($result) ." auto-scheduled league matches");

		while($player_array = mysqli_fetch_array($result) ){
				
                // little bit of a hack, but this is needed for email template. normally this is set when the user logs in
                $_SESSION["siteprefs"]["sitecode"] = $player_array['sitecode']; 
                $_SESSION["siteprefs"]["siteid"] = $player_array['siteid']; 

                //only send to people who want these emails
                if( $player_array['rec1']=='y' && $player_array['scored']==FALSE ){

                    $otherguy = $player_array['firstname2'] ." " . $player_array['lastname2'];
                    $this->sendReminderEmail($player_array['email1'], $player_array['firstname1'], $player_array['boxname'], $otherguy, $player_array['clubname']);
                } else {
                    if (isDebugEnabled(1)) logMessage("LeagueReminderService.sendReminders:". $player_array['email1']. " is not set up to receive these reminders.");
                }

                if( $player_array['rec2']=='y' && $player_array['scored']==FALSE){

                    $otherguy = $player_array['firstname1']. " ". $player_array['lastname1'];
                    $this->sendReminderEmail($player_array['email2'], $player_array['firstname2'], $player_array['boxname'], $otherguy,$player_array['clubname']);
                    
                } else {
                    if (isDebugEnabled(1)) logMessage("LeagueReminderService.sendReminders:". $player_array['email2']. " is not set up to receive these reminders.");
                }
                				
			}
		} 		
													

    private function sendReminderEmail($email, $firstname, $boxname, $otherguy, $clubname){
	
        $var = new clubpro_obj;
        $var->otherguy = $otherguy;
        
        $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/league_reminder.php", $var);
        $emailbody = nl2br($emailbody);	

        $subject ="$clubname - League Match Reminder";
        
        $content = new clubpro_obj;
        $content->line1 = $emailbody;
        $content->boxnamne = $boxname;
        $content->otherguy = $otherguy;
        $content->clubname = $clubname;
        
        $to_emails = array();
        $to_email = $email;
        $to_emails[$to_email] = array(
            'name' => $firstname
        );

        if (isDebugEnabled(1)) logMessage("LeagueReminderService.sendReminders: sending reminder email to $firstname ($email)");

        //Send email to player one
        send_email($subject, $to_emails, $content, "League Match Reminders");
        
	
    }

}

?>
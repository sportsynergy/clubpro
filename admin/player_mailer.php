<?php



include("../application.php");
require($_SESSION["CFG"]["libdir"]."/postageapplib.php");

$DOC_TITLE = "Player Mailer";

require_loginwq();
require_priv("2");


if (match_referer() && isset($_POST['submitme'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
  
        if ( empty($errormsg) ){
             
              $emailResult = send_message($frm['subject'], $frm['message'], get_siteid(), $frm['who'], $frm['sport'], $frm['ranking']);  
               if( mysql_num_rows($emailResult)> 0){ 
               	$noticemsg = " Message sent to ".mysql_num_rows($emailResult)." people";  
               	unset($frm);
               } else {
               	$errormsg = "Hey, nobody was found at your club like this";
               }
                      
        }


}
$availbleSports = load_avail_sports();

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/player_mailer_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

        if (empty($frm["subject"])) {
                $errors->subject = true;
                $msg .= "You did not specify a Subject";

         }elseif (empty($frm["message"])) {
                $errors->message = true;
                $msg .= "You did not specify a message";
         }




        return $msg;
}

/**
 * Sends the email to everyone
 */
function send_message($subject, $message, $siteid, $category, $sport, $ranking){
	
		// Strip Slashes
		if(get_magic_quotes_gpc()){
			$message=stripslashes($message);
			$subject=stripslashes($subject);
		}
		
		$message = nl2br($message);
		
        if( isDebugEnabled(1) ) logMessage("playerMailer.send_message(): \n subject: $subject\n message: $message\n siteid: $siteid\n category: $category\n sport: $sport\n ranking: $ranking\n");

 		//Everyone
        if($category == "allplayers"){
        	 
        	 //A sport was not specified
        	if($sport == "all"){
        		 
        		 $emailidquery = "SELECT users.firstname, users.lastname, users.email
                         FROM tblUsers users, tblkupSiteAuth siteauth, tblClubUser clubuser
                         WHERE users.userid = siteauth.userid
					     AND users.userid = clubuser.userid
						 AND siteauth.siteid = $siteid
						 AND clubuser.enddate IS NULL
						 AND clubuser.clubid=" . get_clubid() . " 
                         AND clubuser.enable ='y'
                         AND clubuser.roleid != 4";
        	 }
        	 
        	 //A Sport was specified
        	 else{
        	 	
        	 	//A Ranking was not specified
        	 	if( $ranking == "all"){
	        	 	
	        	 	$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
					                       FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser
										   WHERE users.userid = rankings.userid
										   AND users.userid = clubuser.userid
					                       AND clubuser.clubid=" . get_clubid() . "
					                       AND clubuser.recemail='y'
					                       AND rankings.courttypeid=$sport
										   AND rankings.usertype = 0
										   AND clubuser.enddate IS NULL
					                       AND clubuser.enable='y'";

        	 	}
        	 	//A ranking was specified
        	 	else{
        	 		$rankinghigh = $ranking + .5;
	        	 	$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
					                       FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser
										   WHERE users.userid = rankings.userid
										   AND users.userid = clubuser.userid
					                       AND clubuser.clubid=" . get_clubid() . "
					                       AND rankings.ranking>$ranking
					                       AND rankings.ranking< $rankinghigh
										   AND rankings.usertype = 0
					                       AND clubuser.recemail='y'
					                       AND rankings.courttypeid=$sport
										   AND clubuser.enddate IS NULL
					                       AND clubuser.enable='y'";
        	 	}
        	 	
        	 }
        	
        	
                         
        }
        
        //Women
        elseif($category == "allWomen"){
        	
        	//A sport was not specified
        	if($sport == "all"){
        		
        		 $emailidquery = "SELECT users.firstname, users.lastname, users.email
                         FROM tblUsers users, tblkupSiteAuth siteauth, tblClubUser clubuser
                         WHERE users.userid = siteauth.userid
						 AND users.userid = clubuser.userid
						 AND siteauth.siteid = $siteid
						 AND users.gender = 0
                         AND clubuser.enable ='y'
						 AND clubuser.enddate IS NULL
						 AND clubuser.clubid=" . get_clubid() . "
                         AND clubuser.roleid != 4";
        		
        	}
        	
        	//A sport was specified
        	else{
        		
        		//A Ranking was not specified
        	 	if( $ranking == "all"){
        	 		
        	 		 $emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
					                       FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser
										   WHERE users.userid = rankings.userid
										   AND users.userid = clubuser.userid
										   AND users.gender = 0
					                       AND clubuser.clubid=" . get_clubid() . "
					                       AND clubuser.recemail='y'
					                       AND rankings.courttypeid=$sport
										   AND rankings.usertype = 0
										   AND clubuser.enddate IS NULL
					                       AND clubuser.enable='y'";
        	 	}
        	 	
        	 	//A ranking was specified
        	 	else{
        	 		
        	 		$rankinghigh = $ranking + .5;
	        	 	$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
					                       FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser
										   WHERE users.userid = rankings.userid
										   AND users.userid = clubuser.userid
					                       AND clubuser.clubid=" . get_clubid() . "
										   AND rankings.usertype = 0
					                       AND rankings.ranking>$ranking
					                       AND rankings.ranking< $rankinghigh
					                       AND clubuser.recemail='y'
										   AND users.gender = 0
					                       AND rankings.courttypeid=$sport
										   AND clubuser.enddate IS NULL
					                       AND clubuser.enable='y'";
        	 	}
        		
        	}
        	
                         
        }
        
        //Men
        elseif($category == "allMen"){
        	
        	 //A sport was not specified
        	if($sport == "all"){
        		
        		 $emailidquery = "SELECT users.firstname, users.lastname, users.email
                         FROM tblUsers users, tblkupSiteAuth siteauth,tblClubUser clubuser
                         WHERE users.userid = siteauth.userid
					     AND users.userid = clubuser.userid
						 AND siteauth.siteid = $siteid
						 AND users.gender = 1
						 AND clubuser.clubid=" . get_clubid() . "
                         AND clubuser.enable = 'y'
						 AND clubuser.enddate IS NULL
                         AND clubuser.roleid != 4
                         ORDER BY users.lastname";
        	}
        	
        	//A sport was specified
        	else{
        		
        		//A Ranking was not specified
        	 	if( $ranking == "all"){
        	 		
        	 		 $emailidquery = "SELECT users.firstname, users.lastname, users.email
					                       FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser
										   WHERE users.userid = rankings.userid
										   AND users.userid = clubuser.userid
										   AND users.gender = 1
					                       AND clubuser.clubid=" . get_clubid() . "
					                       AND clubuser.recemail='y'
					                       AND rankings.courttypeid=$sport
										   AND rankings.usertype = 0
										   AND clubuser.enddate IS NULL
					                       AND clubuser.enable='y'
					                        ORDER BY users.lastname";
        	 	}
        	 	
        	 	//A ranking was specified
        	 	else{
        	 		
        	 		$rankinghigh = $ranking + .5;
        	 		$emailidquery = "SELECT users.firstname, users.lastname, users.email
					                       FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser
										   WHERE users.userid = rankings.userid
										   AND users.userid = clubuser.userid
					                       AND clubuser.clubid=" . get_clubid() . "
					                       AND rankings.ranking>$ranking
					                       AND rankings.ranking< $rankinghigh
										   AND rankings.usertype = 0
					                       AND clubuser.recemail='y'
										   AND users.gender = 1
					                       AND rankings.courttypeid=$sport
										   AND clubuser.enddate IS NULL
					                       AND clubuser.enable='y'
					                        ORDER BY users.lastname";
        	 	}
        	 	
        	
        	}               
        
        
        
        }
        
        //Box League Player use a DISTINCTROW because people can be in more than one box
        elseif($category == "boxleaguePlayers"){
        	
        	 //A sport was not specified
        	if($sport == "all"){
	        	$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
	                         FROM tblUsers users, tblkupSiteAuth siteauth, tblkpBoxLeagues leagues, tblClubUser clubuser
	                         WHERE users.userid = siteauth.userid
							 AND users.userid = clubuser.userid
							 AND users.userid = leagues.userid
							 AND siteauth.siteid = $siteid
	                         AND clubuser.enable = 'y'
							 AND clubuser.enddate IS NULL
							 AND clubuser.clubid=" . get_clubid() . "
	                         AND clubuser.roleid != 4
	                          ORDER BY users.lastname";
        	}
        	
        	//A sport was specified
        	else{
        		
        		//A Ranking was not specified
        	 	if( $ranking == "all"){
        	 		
        	 		$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
			                         FROM tblUsers users, tblkupSiteAuth siteauth, tblkpBoxLeagues leaguedetails, tblBoxLeagues leagues, tblClubUser clubuser
			                         WHERE users.userid = siteauth.userid
									 AND users.userid = clubuser.userid
									 AND leaguedetails.boxid = leagues.boxid
									 AND users.userid = leaguedetails.userid
									 AND leagues.courttypeid=$sport
									 AND siteauth.siteid = $siteid
			                         AND clubuser.enable = 'y'
									 AND clubuser.enddate IS NULL
									 AND clubuser.clubid=" . get_clubid() . "
			                         AND clubuser.roleid != 4";
        	 	}
        	 	//A ranking was specified
        	 	else{
        	 		$rankinghigh = $ranking + .5;
        	 		$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
			                         FROM tblUsers users, tblkupSiteAuth siteauth, tblkpBoxLeagues leaguedetails, tblBoxLeagues leagues, tblClubUser clubuser, tblUserRankings rankings
			                         WHERE users.userid = siteauth.userid
									 AND users.userid = clubuser.userid
									 AND leaguedetails.boxid = leagues.boxid
									 AND users.userid = leaguedetails.userid
									 AND leagues.courttypeid=$sport
									 AND leagues.courttypeid = rankings.courttypeid
					                 AND rankings.ranking>$ranking
					                 AND rankings.ranking< $rankinghigh
                                     AND rankings.usertype = 0
                                     AND rankings.userid = users.userid
									 AND siteauth.siteid = $siteid
			                         AND clubuser.enable = 'y'
									 AND clubuser.enddate IS NULL
									 AND clubuser.clubid=" . get_clubid() . "
			                         AND clubuser.roleid != 4
			                          ORDER BY users.lastname";
        	 		
        	 	}
        	
        	
        	}	
        }
        elseif($category == "myBuddies"){
        	
        	 //A sport was not specified
        	if($sport == "all"){
        		$emailidquery = "SELECT users.firstname, users.lastname, users.email
		                         FROM tblUsers users, tblBuddies buddies, tblClubUser clubuser
								 WHERE users.userid = buddies.buddyid
								 AND users.userid = clubuser.userid
								 AND buddies.userid=".get_userid()."
								 AND clubuser.enddate IS NULL
								 AND clubuser.clubid=" . get_clubid() . "
		                         AND clubuser.enable = 'y'
		                          ORDER BY users.lastname";
        	}
        	
        	//A sport was specified
        	else{
        		
        		//A Ranking was not specified
        	 	if( $ranking == "all"){
        	 		
        	 		$emailidquery = "SELECT users.firstname, users.lastname, users.email
			                         FROM tblUsers users, tblBuddies buddies, tblClubUser clubuser, tblUserRankings rankings
									 WHERE users.userid = buddies.buddyid
									 AND users.userid = rankings.userid
									 AND users.userid = clubuser.userid
									 AND rankings.courttypeid = $sport
									 AND rankings.usertype = 0
									 AND buddies.userid=".get_userid()."
									 AND clubuser.enddate IS NULL
									 AND clubuser.clubid=" . get_clubid() . "
			                         AND clubuser.enable = 'y'
			                          ORDER BY users.lastname";
        	 		
        	 	}
        	 	
        	 	//A ranking was specified
        	 	else{
        	 		
        	 		$rankinghigh = $ranking + .5;
        	 		$emailidquery = "SELECT users.firstname, users.lastname, users.email
			                         FROM tblUsers users, tblBuddies buddies, tblClubUser clubuser, tblUserRankings rankings
									 WHERE users.userid = buddies.buddyid
									 AND users.userid = rankings.userid
									 AND users.userid = clubuser.userid
									 AND rankings.courttypeid = $sport
					                 AND rankings.ranking>$ranking
					                 AND rankings.ranking< $rankinghigh
                                     AND rankings.usertype = 0
									 AND buddies.userid=".get_userid()."
									 AND clubuser.enddate IS NULL
									 AND clubuser.clubid=" . get_clubid() . "
			                         AND clubuser.enable = 'y'
			                          ORDER BY users.lastname";
        	 	}
        	
                         
        }
        
        }
       

         // run the query on the database
        $emailidresult = db_query($emailidquery);

       $clubadminquery =  "SELECT tblUsers.email
                           FROM tblUsers
                           WHERE tblUsers.userid=".get_userid()."";

       // run the query on the database
        $clubadminresult =  db_query($clubadminquery);
        $clubadminval = mysql_result($clubadminresult,0);
        $to_emails = array();

        // Put all of the email addresses in an array
        while($emailidrow = db_fetch_row($emailidresult)) {
       		$to_email = "$emailidrow[0] $emailidrow[1] <$emailidrow[2]>";
        	$to_emails[$to_email] = array('name' => $emailidrow[0]);
       		
         }
           
		$from_email = $clubadminval;
		$content = new Object;
		$content->line1 = $message;
		$content->clubname = get_clubname();
		$template = get_sitecode()."-blank";
		
		//Send the email
        send_email($subject, $to_emails, $from_email, $content, $template);   
            
       return $emailidresult;
	
}





?>
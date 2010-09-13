<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */

include("../application.php");
$DOC_TITLE = "Player Mailer";


if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $backtopage = $_SESSION["CFG"]["wwwroot"]."/admin/player_mailer.php";

        if ($errormsg){
             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
        }

         else{

              include($_SESSION["CFG"]["templatedir"]."/header.php");
              $emailResult = send_message($frm['subject'], $frm['message'], get_siteid(), $frm['who'], $frm['sport'], $frm['ranking']);           
              include($_SESSION["CFG"]["includedir"]."/include_mailsuc.php");
              include($_SESSION["CFG"]["templatedir"]."/footer.php");
              die;
       }

}
$availbleSports = load_avail_sports();
include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/player_mailer_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

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
                         AND clubuser.roleid != 4";
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
					                       AND clubuser.enable='y'";
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
					                       AND clubuser.enable='y'";
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
	                         AND clubuser.roleid != 4";
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
			                         AND clubuser.roleid != 4";
        	 		
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
		                         AND clubuser.enable = 'y'";
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
			                         AND clubuser.enable = 'y'";
        	 		
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
			                         AND clubuser.enable = 'y'";
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


       while($emailidrow = db_fetch_row($emailidresult)) {

          mail(
                "$emailidrow[0] $emailidrow[1] <$emailidrow[2]>",
                "$subject",
                "$message",
                "From: $clubadminval");

            }
           
           
            
       return $emailidresult;
	
}





?>
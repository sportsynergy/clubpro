<?php


include("../application.php");
require($_SESSION["CFG"]["libdir"]."/postageapplib.php");
$DOC_TITLE = "Reservation Details";

//Set the http variables
$time = $_REQUEST["time"];

/* form has been submitted, try to create the new role */
if (match_referer() && isset($_POST)) {
      $frm = $_POST;
      $wwwroot = $_SESSION["CFG"]["wwwroot"];

     if($frm['resdetails']==1){
      //send out emails to players within range
      email_players($frm['resid'], 1);
      
      header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ."");

     }
     elseif($frm['resdetails']==2){

       //mark match type as a buddy match
     markMatchType($frm['resid'],3);

     //send out emails just to the buddies
     email_players($frm['resid'], 2);
     header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ."");

     }
     elseif($frm['resdetails']==3){

     //send out emails just to the whole club
     email_players($frm['resid'], 3);
     header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ."");

     }
     elseif($frm['boxid']){

	     if($frm[boxid]!="dontdoit"){
	
	           //send out emails just to the buddies
	           email_boxmembers($frm['resid'], getBoxIdForUser(get_userid()));
	     }
	
	      header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ."");
     }
     
     //Email about a lesson
     elseif($frm['resdetails']==5){
     	   if( isDebugEnabled(1) ) logMessage("Reservation_details: Emailing about lesson");
     	 email_players_about_lesson($frm['resid']);
     	 header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ."");
     }
     	
     
     else{
     	 header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ."");
     }


}

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/reservation_details_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

/************************************************************************************************************************/
/*
  This function is called after any reservation is made where the admin is available for a lesson

*/

function email_players_about_lesson($resid) {

	$rquery = "SELECT courts.courtname, reservations.time, users.firstname, users.lastname, courttype.courttypeid, rankings.ranking, reservations.matchtype, users.email, users.homephone, users.cellphone, users.workphone
						courts.courtid, users.userid
	                 FROM tblCourts courts, tblReservations reservations, tblUsers users, tblCourtType courttype, tblUserRankings rankings, tblkpUserReservations reservationdetails
					 WHERE users.userid = rankings.userid
					 AND reservations.courtid = courts.courtid
	                 AND reservationdetails.reservationid = reservations.reservationid
	                 AND courttype.courttypeid = rankings.courttypeid
	                 AND courts.courttypeid = courttype.courttypeid
	                 AND reservationdetails.userid = users.userid
	                 AND reservations.reservationid = $resid
					 AND rankings.usertype=0";

	$rresult = db_query($rquery);
	$robj = mysql_fetch_object($rresult);
	$var = new Object;

	/* email the user with the new account information    */

	$var->firstname = $robj->firstname;
	$var->lastname = $robj->lastname;
	$var->email = $robj->email;
	$var->homephone = $robj->homephone;
	$var->cellphone = $robj->cellphone;
	$var->workphone = $robj->workphone;
	$var->courtname = $robj->courtname;
	$var->userid = $robj->userid;
	$var->courtid = $robj->courtid;
	$var->time = gmdate("l F j g:i a", $robj->time);
	$var->timestamp = $robj->time;
	$var->dns = $_SESSION["CFG"]["dns"];
	$var->wwwroot = $_SESSION["CFG"]["wwwroot"];
	$var->fullname = $robj->firstname . " " . $robj->lastname;
	$var->support = $_SESSION["CFG"]["support"];

	$var->signupurl = "http://".$var->dns."".$var->wwwroot."/users/court_reservation.php?time=".$var->timestamp."&courtid=".$var->courtid."&userid=".$var->userid;
	$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/lesson_wanted.php", $var);

	//Now get all players who receive players wanted notifications at the club
	$emailidquery = "SELECT tblUsers.firstname, tblUsers.lastname, tblUsers.email
	                        FROM tblUsers, tblClubUser
	                        WHERE tblClubUser.recemail='y'
							AND  tblUsers.userid = tblClubUser.userid
	                        AND tblClubUser.clubid=" . get_clubid() . "
	                        AND tblUsers.userid != " . get_userid() . "
	                        AND tblClubUser.enable='y'
							AND tblClubUser.enddate IS NULL";

	// run the query on the database
	$emailidresult = db_query($emailidquery);
	$template = get_sitecode();

	while ($emailidrow = db_fetch_row($emailidresult)) {
		
		if( isDebugEnabled(1) ) logMessage($message);
			$subject = get_clubname()." - Lesson Available";
			$to_email = $emailidrow[2];
			$to_name = "$emailidrow[0] $emailidrow[1]";
			$from_email = "PlayerMailer@sportsynergy.net";
			$content = new Object;
			$content->line1 = $emailbody;
			$content->line2 = "";
			$content->line3 = "";
			$content->clubname = get_clubname();
			$content->to_firstname = $emailidrow[0];
	
		send_email($subject, $to_email, $to_name,$from_email, $content, $template);
		//mail("$emailidrow[0] $emailidrow[1] <$emailidrow[2]>", "$clubfullname -- Lesson Available", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

	}

}


?>
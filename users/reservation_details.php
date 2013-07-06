<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */
/**
* Class and Function List:
* Function list:
* - email_players_about_lesson()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/sendgrid-php/SendGrid_loader.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

$DOC_TITLE = "Reservation Details";

//Set the http variables
$time = $_REQUEST["time"];

/* form has been submitted, try to create the new role */

if (match_referer() && isset($_POST)) {
    $frm = $_POST;
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    
    if ($frm['resdetails'] == 1) {

        //send out emails to players within range
        email_players($frm['resid'], 1);
        header("Location: $wwwroot/clubs/" . get_sitecode() . "/index.php?daysahead=" . gmmktime(0, 0, 0, gmdate("n", $time + get_tzdelta()) , gmdate("j", $time + get_tzdelta()) , gmdate("Y", $time + get_tzdelta())) . "");
    } elseif ($frm['resdetails'] == 2) {

        //mark match type as a buddy match
        markMatchType($frm['resid'], 3);

        //send out emails just to the buddies
        email_players($frm['resid'], 2);
        header("Location: $wwwroot/clubs/" . get_sitecode() . "/index.php?daysahead=" . gmmktime(0, 0, 0, gmdate("n", $time + get_tzdelta()) , gmdate("j", $time + get_tzdelta()) , gmdate("Y", $time + get_tzdelta())) . "");
    } elseif ($frm['resdetails'] == 3) {

        //send out emails just to the whole club
        email_players($frm['resid'], 3);
        header("Location: $wwwroot/clubs/" . get_sitecode() . "/index.php?daysahead=" . gmmktime(0, 0, 0, gmdate("n", $time + get_tzdelta()) , gmdate("j", $time + get_tzdelta()) , gmdate("Y", $time + get_tzdelta())) . "");
    } elseif ($frm['boxid']) {
        
        if ($frm[boxid] != "dontdoit") {

            //send out emails just to the buddies
            email_boxmembers($frm['resid'], getBoxIdForUser(get_userid()));
        }
        header("Location: $wwwroot/clubs/" . get_sitecode() . "/index.php?daysahead=" . gmmktime(0, 0, 0, gmdate("n", $time + get_tzdelta()) , gmdate("j", $time + get_tzdelta()) , gmdate("Y", $time + get_tzdelta())) . "");
    }

    //Email about a lesson
    elseif ($frm['resdetails'] == 5) {
        
        if (isDebugEnabled(1)) logMessage("Reservation_details: Emailing about lesson");
        email_players_about_lesson($frm['resid']);
        header("Location: $wwwroot/clubs/" . get_sitecode() . "/index.php?daysahead=" . gmmktime(0, 0, 0, gmdate("n", $time + get_tzdelta()) , gmdate("j", $time + get_tzdelta()) , gmdate("Y", $time + get_tzdelta())) . "");
    } else {
        header("Location: $wwwroot/clubs/" . get_sitecode() . "/index.php?daysahead=" . gmmktime(0, 0, 0, gmdate("n", $time + get_tzdelta()) , gmdate("j", $time + get_tzdelta()) , gmdate("Y", $time + get_tzdelta())) . "");
    }
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/reservation_details_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

/************************************************************************************************************************/

/*
  This function is called after any reservation is made where the admin is available for a lesson

*/
function email_players_about_lesson($resid) {
    $rquery = "SELECT   courts.courtname, 
                        reservations.time, 
                        users.firstname, 
                        users.lastname, 
                        courttype.courttypeid, 
                        rankings.ranking, 
                        reservations.matchtype, 
                        users.email, 
                        users.homephone, 
                        users.cellphone, 
                        users.workphone,
						courts.courtid, 
                        users.userid
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
    $var->signupurl = "http://" . $var->dns . "" . $var->wwwroot . "/users/court_reservation.php?time=" . $var->timestamp . "&courtid=" . $var->courtid . "&userid=" . $var->userid;
    $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/lesson_wanted.php", $var);

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
     if (isDebugEnabled(1)) logMessage("email message: ".$emailbody);

    while ($emailidrow = db_fetch_row($emailidresult)) {
        
        $subject = get_clubname() . " - Lesson Available";
        $to_email = array(
            $emailidrow[2] => array(
                'name' => $emailidrow[0]
            )
        );

    }
        
    $content = new Object;
    $content->line1 = $emailbody;
    $content->clubname = get_clubname();
    sendgrid_email($subject, $to_email, $content, "Lesson Wanted");
    
}

?>
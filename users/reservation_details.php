<?php

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/

include("../application.php");
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



?>
<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */

include("../application.php");
$DOC_TITLE = "Delete Player";

require_loginwq();
require_priv("2");

//Set the http variables
$searchname= $_REQUEST["searchname"];
$userid = $_REQUEST["userid"];

if (match_referer() && isset($_POST)) {
     
     $frm = $_POST;
     include($_SESSION["CFG"]["templatedir"]."/header.php");
     $delplayer = delete_player($frm);
     include($_SESSION["CFG"]["includedir"]."/include_userdelsuc.php");
     include($_SESSION["CFG"]["templatedir"]."/footer.php");
	 die;

}

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/player_delete_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/



function delete_player(&$frm) {
/* Delete the User from the Users Table */

		//If this player belongs to more than one club, be sure to only remove
		// the club authorization not the entire user
		
		$clubUserQuery = "SELECT clubuser.clubid FROM tblClubUser clubuser WHERE clubuser.userid = '$frm[userid]'";
		$clubUserResult = db_query($clubUserQuery);
		
		//Only delete the user if there is only one club authorization
		if( mysql_num_rows($clubUserResult) == 1){
			$qid1 = db_query("UPDATE tblUsers SET enddate = NOW() WHERE userid = '$frm[userid]'");
			if( isDebugEnabled(2) ) logMessage("player_delete.delete_player: Deleting user: '$frm[userid]' from ".get_clubname()." by ".get_userfullname() );
		}

		while( $row = mysql_fetch_array($clubUserResult)){

			//Leave the user in tact (only remove the authorization as this user belongs to another club)
			if($row['clubid']== get_clubid() ){
				$qid1 = db_query("UPDATE tblClubUser SET enddate = NOW() WHERE userid = '$frm[userid]' AND clubid = $row[clubid]");
				if( isDebugEnabled(2) ) logMessage("player_delete.delete_player: Deleting club user: '$frm[userid]' from ".get_clubname()." by ".get_userfullname() );
			}
			
		}
		

       // Now we need to get rid of all of the teams they were on

       $teamidquery = "SELECT teamid
                       FROM tblkpTeams
                       WHERE userid='$frm[userid]'";

       // run the query on the database
        $teamidresult = db_query($teamidquery);

        while($teamidrow = db_fetch_row($teamidresult)) {

			//Get rid of the teams in the first teams table
             $qid1 = db_query("UPDATE tblTeams SET enable = 0 WHERE teamid = $teamidrow[0]");

			//Get rid of the teams in the second teams table
             $qid1 = db_query("UPDATE tblkpTeams SET enable = 0 WHERE teamid = $teamidrow[0]");
             

             //Get all reservations where the persons team was playing
             $teamresidquery = "SELECT reservations.reservationid
                              FROM tblReservations reservations, tblkpUserReservations reservationdetails
							  WHERE reservations.reservationid = reservationdetails.reservationid
                              AND reservations.usertype=1
							  AND reservationdetails.usertype=1
                              AND reservationdetails.userid=$teamidrow[0]";

             // run the query on the database
             $teamresidresult = db_query($teamresidquery);

		             while($teamresidrow = db_fetch_row($teamresidresult)) {
		               
 					$qid1 = db_query("UPDATE tblReservations 
							SET lastmodifier = ".get_userid().", enddate = NOW() 
							WHERE reservationid = $teamresidrow[0]");
							
							if( isDebugEnabled(2) ) logMessage("-> End dating doubles reservation: $teamresidrow[0]");

		        }


        }


       
       // Finally get rid of their reservations and scores.  We first get all reservationid
       // where this user was playing.  This will delete any doubles reservations that include
       // the player as a single person looking for a partner.

       $userresidquery = "SELECT reservations.reservationid
                          FROM tblReservations reservations, tblkpUserReservations reservationdetails
                          WHERE reservations.reservationid = reservationdetails.reservationid
						  AND reservationdetails.userid='$frm[userid]'
                          AND reservationdetails.usertype=0";
                          
                          
	       
       
       // For xome reason the userid is being (re)set to 0 which ends up enddating all
       //reservations where someone was looking for a match.  Do a quick check here to see
       //if this is the case if it is, just exit
       
       
       if( $frm['userid'] == 0){
       
         if( isDebugEnabled(2) ) logMessage("-> Exiting because userid = 0: Here is the query that was doing to jack everything all up: $userresidquery");
       	return;
       }

      // run the query on the database
      $userresidresult = db_query($userresidquery);


      /* Get rid of all singles reservations.  The reason why this is done
       * like this is it is presumed that if a player is being deleted, they will not
       * be in any current reservations, this is a little harse I do admit.
       */
      
      while($userresidrow = db_fetch_row($userresidresult)) {

                          
       $qid1 = db_query("UPDATE tblReservations 
							SET lastmodifier = ".get_userid().", enddate = NOW() 
							WHERE reservationid = $userresidrow[0]");
       
        if( isDebugEnabled(2) ) logMessage("-> End dating singles reservation: $userresidrow[0]");
        }


	//Remove them from any club ladder
	$userid = $frm['userid'];
	$clubid = get_clubid();
	$query = "SELECT ladder.* FROM tblClubLadder ladder WHERE clubid = $clubid AND userid = $userid AND enddate IS NULL";
    $result = db_query($query);
        
    //For each ladder they were in, remove them and move everyone up.
	while( $array = db_fetch_array($result)){
		
		$courttypeid = $array['courttypeid'];
		$clubid = $array['clubid'];
		$position = $array['position'];
		
		//end date the player
		$enddatequery = "UPDATE tblClubLadder 
					SET enddate = NOW() 
					WHERE userid = $userid 
					AND  courttypeid = $courttypeid 
					AND clubid = $clubid";
		
		db_query($enddatequery);
		
		//Move everybody else up
		moveEveryOneInClubLadderUp($courttypeid, $clubid, $position+1);
		if(isDebugEnabled(2) ) logMessage("player_delete: removing user $userid from club ladder for club $clubid for courttypeid $courttypeid");
		
	}
        	
			
			

			
			



    return 1;

   }

?>
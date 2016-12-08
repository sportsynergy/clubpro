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
* - get_playerswanted()
* Classes list:
*/
/*
 * $LastChangedRevision: 854 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-08 20:15:00 -0600 (Tue, 08 Mar 2011) $

*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
require_login();

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

$DOC_TITLE = "Players Wanted";
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
get_playerswanted();
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function get_playerswanted() {
    $imagedir = $_SESSION["CFG"]["imagedir"];
    $wwwroot = $_SESSION["CFG"]["wwwroot"];

    /* Get the Reservation id all court types */

    //Get General Club info
    $clubquery = "SELECT timezone from tblClubs WHERE clubid=" . get_clubid() . "";
    $clubresult = db_query($clubquery);
    $clubobj = db_fetch_object($clubresult);
    $gmtime = gmmktime();
    $mktime = mktime();
    $tzdelta = $clubobj->timezone * 3600;
    $curtime = $mktime + $tzdelta;
    $simtzdelta = $clubobj->timezone;

    //First we need to get all resvations that have a userid with 0 in the future
    $singlespwquery = "SELECT DISTINCTROW tblReservations.reservationid,tblReservations.time
                   FROM (tblCourts INNER JOIN tblReservations
                   ON tblCourts.courtid = tblReservations.courtid)
                   INNER JOIN tblkpUserReservations
                   ON tblReservations.reservationid = tblkpUserReservations.reservationid
                   WHERE (((tblReservations.time)>$curtime)
                   AND ((tblkpUserReservations.userid)=0)
                   AND ((tblCourts.siteid)=" . get_siteid() . ")
                   AND ((tblReservations.usertype)=0))
                   AND (tblReservations.matchtype != 4)
				   AND tblReservations.enddate IS NULL
                   ORDER BY tblReservations.time";

    $doublesspwquery = "SELECT DISTINCTROW tblReservations.reservationid, tblReservations.time
                    FROM (tblCourts INNER JOIN tblReservations
                    ON tblCourts.courtid = tblReservations.courtid)
                    INNER JOIN tblkpUserReservations
                    ON tblReservations.reservationid = tblkpUserReservations.reservationid
                    WHERE (((tblReservations.time)>$curtime)
                    AND ((tblkpUserReservations.userid)=0)
                    AND ((tblCourts.siteid)=" . get_siteid() . ")
                    AND ((tblReservations.usertype)=1))
                    ORDER BY tblReservations.time";

    //get doubles reservations that need an extra player
    $doublesspwquery2 = "SELECT DISTINCTROW reservations.reservationid, reservations.time
                     FROM tblCourts courts, tblkpUserReservations reservationdetails, tblReservations reservations
                     WHERE courts.courtid = reservations.courtid
                     AND reservations.reservationid = reservationdetails.reservationid
                     AND ( ( ( reservations.usertype ) = 1 )
                     AND ( ( reservationdetails.usertype ) = 0 )
                     AND ( ( reservationdetails.userid ) <> 0 )
                     AND ( ( reservations.time ) > $curtime )
                     AND ( ( courts.siteid ) = " . get_siteid() . " ) )
					 AND reservations.enddate IS NULL";
    $lessonspwquery = "SELECT reservations.reservationid
                   FROM tblCourts courts, tblReservations reservations, tblkpUserReservations reservationdetails
                   WHERE courts.courtid = reservations.courtid
                   AND reservationdetails.reservationid = reservations.reservationid
                   AND (((reservations.time)>$curtime)
                   AND ((reservationdetails.userid)=0)
                   AND ((courts.siteid)=" . get_siteid() . ")
                   AND ((reservations.usertype)=0))
                   AND (reservations.matchtype = 4)
				   AND reservations.enddate IS NULL
                   ORDER BY reservations.time";

    // run the query on the database
    $singlespwresult = db_query($singlespwquery);
    $doublesspwresult = db_query($doublesspwquery);
    $doublesspwresult2 = db_query($doublesspwquery2);
    $lessonpwresult = db_query($lessonspwquery);
    $doublesArray1 = array();
    $doublesArray2 = array();
    $doublesArray = array();

    //Jam our two subtlely different doubles reservation types together
    for ($i = 0; $i < mysqli_num_rows($doublesspwresult); $i++) {
        $data = mysqli_fetch_array($doublesspwresult);
        $doublesArray1[$i] = array(
            "time" => $data['time'],
            "reservationid" => $data['reservationid']
        );
    }
    for ($i = 0; $i < mysqli_num_rows($doublesspwresult2); $i++) {
        $data = mysqli_fetch_array($doublesspwresult2);
        $newEntry = array(
            "time" => $data['time'],
            "reservationid" => $data['reservationid']
        );

        //Only add if its unique
        
        if (!in_array($newEntry, $doublesArray1)) {
            $doublesArray2[$i] = $newEntry;
        }
    }
    $doublesArray = array_merge($doublesArray1, $doublesArray2);
    sort($doublesArray);
?>

<table cellspacing="0" cellpadding="0" border="0" width="710" class="borderless">

<?
  $anyboxesquery = "SELECT tblBoxLeagues.boxid
    FROM tblBoxLeagues
    WHERE (((tblBoxLeagues.siteid)=".get_siteid()."))";

    $anyboxesresult = db_query($anyboxesquery);

    if(mysqli_num_rows($anyboxesresult)>0){

         echo "\t<tr>\n";
         echo "\t\t<td>\n";
         echo "<img src=\"$imagedir/boxleague.gif\"> <font class=normalsm> Indicates League Match</font><br> ";
         echo "\t\t</td>\n";
         echo "\t</tr>\n";
   }
echo "\t<tr>\n";
echo "\t\t<td>\n";



if(mysqli_num_rows($singlespwresult)==0 && mysqli_num_rows($doublesspwresult)==0 && mysqli_num_rows($doublesspwresult2)==0 && mysqli_num_rows($lessonpwresult)==0){
   echo "<table>";
   echo "<tr>";
   echo "<td width=\"30\"></td>";
   echo "<td class=normal>No players looking for matches found.<td>";
   echo "</tr>";
   echo "</table>";
   }
else{


   echo "<table width=700>\n";




   if(mysqli_num_rows($singlespwresult)>0){
     ?>
       <tr>
        <td colspan="2"><font class="bigbanner">Players looking for a Singles Match</font></td>
       </tr>
     <?

               while($row = mysqli_fetch_array($singlespwresult)){
                 //Now for each returned reservationid we need to get the details of the court for the singles reservations

                 $scourtdetailsquery = "SELECT DISTINCTROW reservations.time, users.firstname, users.lastname, courts.courtname, reservations.courtid, rankings.ranking, reservations.matchtype, users.userid
                                        FROM tblUserRankings rankings, tblUsers users, tblCourtType courttype, tblCourts courts, tblkpUserReservations reservationdetails, tblReservations reservations, tblClubUser clubuser
										WHERE rankings.userid = users.userid
                                        AND courttype.courttypeid = rankings.courttypeid
                                        AND reservations.courtid = courts.courtid
                                        AND users.userid = reservationdetails.userid
                                        AND reservations.reservationid = reservationdetails.reservationid
                                        AND courts.courttypeid = courttype.courttypeid
                                        AND reservationdetails.reservationid=$row[reservationid]
                                        AND reservations.usertype=0
										AND users.userid = clubuser.userid
                                        AND clubuser.clubid=".get_clubid();

                 // run the query on the database
                 $scourtdetailsresult = db_query($scourtdetailsquery);

                     //Print out details on the singles reservations
                                 while($scourtdetailsrow = db_fetch_row($scourtdetailsresult)) {
                                     echo "<tr>\n";
                                     echo "<td width=\"30\"></td>\n";
                                     echo "<td>\n";
                                     echo "<font class=normal><a href=\"$wwwroot/users/court_reservation.php?time=$scourtdetailsrow[0]&courtid=$scourtdetailsrow[4]&userid=$scourtdetailsrow[7]\">".gmdate(" l F j h:i A",$scourtdetailsrow[0])."</a> $scourtdetailsrow[1] $scourtdetailsrow[2]  (rank $scourtdetailsrow[5]) on $scourtdetailsrow[3] ";
                                      if($scourtdetailsrow[6]==1){
                                          echo "<img src=\"$imagedir\boxleague.gif\">";
                                       }
                                     echo "</td>\n";
                                     echo "</tr>\n";
                                 }
                }

 }


 ?>
 <tr>
      <td height="20"></td>
 </tr>
<?



 if(count($doublesArray)>0){
     ?>
       <tr>
        <td colspan="2"><font class=bigbanner>Players looking for a Doubles Match</font></td>
       </tr>
 <?


  for($i=0; $i<count($doublesArray); $i++){
			
				//reset all of these
				$needOnePlayer=false;
				$needTwoPlayer=false;
				$needThreePlayer=false;

                 //Now for each returned reservationid we need to get the details of the court for the doubles reservations
                 $printlonelyUser = FALSE;
                 $resid = $doublesArray[$i]['reservationid'];

                 $dcourtdetailsquery = "SELECT tblkpUserReservations.usertype, tblkpUserReservations.userid, tblCourts.courtid, tblReservations.time, tblCourts.courtname
                                        FROM (tblReservations
                                        INNER JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid)
                                        INNER JOIN tblCourts ON tblReservations.courtid = tblCourts.courtid
                                        WHERE (((tblReservations.usertype)=1) AND ((tblReservations.reservationid)=$resid)
                                        AND ((tblCourts.clubid)=".get_clubid()."))
                                        ORDER BY tblkpUserReservations.usertype, tblkpUserReservations.userid";


                 // run the query on the database
                 $dcourtdetailsresult = mysqli_query($dcourtdetailsquery);
				$playerOneArray = mysqli_fetch_array($dcourtdetailsresult);
				$playerTwoArray = mysqli_fetch_array($dcourtdetailsresult);

				//Get Users for reservation needing one player
	           if($playerOneArray['usertype']=="0" && $playerOneArray['userid']!="0" && $playerTwoArray['usertype']=="1"){

	                $lonelyuser =  getFullNameForUserId($playerOneArray['userid']); 
	                $lonelyuserid = $playerOneArray['userid'];
	                $needOnePlayer = TRUE;

	           }
	           //Get Users for reservation needing three player
			   elseif( $playerOneArray['userid']=="0" && $playerOneArray['usertype']=="0" && $playerTwoArray['usertype']=="0"){
					
					$lonelyuserid = $playerTwoArray['userid'];
	                $reallyLonelyuser =  getFullNameForUserId($playerTwoArray['userid']);  
	                 $needThreePlayer = TRUE;               
				}
				//Get Users for reservation needing two player (different teams)
				elseif($playerOneArray['usertype']=="0" && $playerOneArray['userid']!=0 && $playerTwoArray['usertype']=="0" && $playerTwoArray['userid']!=0){
	                          	

					$user1Name = getFullNameForUserId($playerOneArray['userid']); 
					$userOneId = $playerOneArray['userid'];
					$user2Name = getFullNameForUserId($playerTwoArray['userid']);
					$userTwoId = $playerTwoArray['userid'];
 					$needTwoPlayer = TRUE;              	
	            }

	           //Print out details on the doubles reservations
	           mysqli_data_seek($dcourtdetailsresult,0);

              echo "<tr>\n";
              echo "<td width=\"30\"></td>\n";
              echo "<td>\n";
              
              if( $needTwoPlayer ){
              	 echo "<font class=normal><a href=\"$wwwroot/users/court_reservation.php?time=$playerOneArray[time]&courtid=$playerOneArray[courtid]&userid=$lonelyuserid\">".gmdate(" l F j h:i A",$playerOneArray['time'])."</a> $user1Name and $user2Name are both looking for a partner. ";
				
              }
              elseif( $needThreePlayer ){
              	 echo "<font class=normal><a href=\"$wwwroot/users/court_reservation.php?time=$playerOneArray[time]&courtid=$playerOneArray[courtid]&userid=$lonelyuserid\">".gmdate(" l F j h:i A",$playerOneArray['time'])."</a> $reallyLonelyuser is looking for some doubles. ";
				
              }
              elseif( $needOnePlayer ){

				//Get the team player names
              $teamnamesquery = "SELECT tblUsers.firstname, tblUsers.lastname
                                 FROM tblUsers INNER JOIN tblkpTeams ON tblUsers.userid = tblkpTeams.userid
                                 WHERE (((tblkpTeams.teamid)=$playerTwoArray[userid]))";

              // run the query on the database
              $teamnamesresult = db_query($teamnamesquery);
              $teamnamesrow = db_fetch_row($teamnamesresult);

              //Get team rank
              $teamRankQuery = "SELECT ranking from tblUserRankings
                                WHERE userid = $playerTwoArray[userid]
                                AND usertype = 1";


              $teamRankResult = db_query($teamRankQuery);
              $teamRankValue = mysqli_result($teamRankResult,0);


                 echo "<font class=normal><a href=\"$wwwroot/users/court_reservation.php?time=$playerOneArray[time]&courtid=$playerOneArray[courtid]&userid=$lonelyuserid\">".gmdate(" l F j h:i A",$playerOneArray['time'])."</a> $lonelyuser needs a partner to play against $teamnamesrow[0] $teamnamesrow[1] and ";
				 //Get the next player
                 $teamnamesrow = db_fetch_row($teamnamesresult);
                 echo "$teamnamesrow[0] $teamnamesrow[1]";
                 echo " (rank $teamRankValue) on $playerOneArray[courtname] ";
              }
              //Display reservation where a team is needed
              else{
                  
                  //Get the team player names
              $teamnamesquery = "SELECT tblUsers.firstname, tblUsers.lastname
                                 FROM tblUsers INNER JOIN tblkpTeams ON tblUsers.userid = tblkpTeams.userid
                                 WHERE (((tblkpTeams.teamid)=$playerTwoArray[userid]))";


              // run the query on the database
              $teamnamesresult = db_query($teamnamesquery);
              $teamnamesrow = db_fetch_row($teamnamesresult);

              //Get team rank
              $teamRankQuery = "SELECT ranking from tblUserRankings
                                WHERE userid = $playerTwoArray[userid]
                                AND usertype = 1";


              $teamRankResult = db_query($teamRankQuery);
              $teamRankValue = mysqli_result($teamRankResult,0);
                  
                   echo "<font class=normal><a href=\"$wwwroot/users/court_reservation.php?time=$playerTwoArray[time]&courtid=$playerTwoArray[courtid]&userid=$playerTwoArray[userid]\">".gmdate(" l F j h:i A",$playerTwoArray['time'])."</a> $teamnamesrow[0] $teamnamesrow[1] and ";
                  //Get the next player
                  $teamnamesrow = db_fetch_row($teamnamesresult);
                  echo "$teamnamesrow[0] $teamnamesrow[1]";
                  echo " (rank $teamRankValue) on $playerTwoArray[courtname] ";
              }
              

              echo "</td>\n";
              echo "</tr>\n";

 }

}
   if(mysqli_num_rows($lessonpwresult)>0){
?>
       <tr>
        <td colspan="2"><font class="bigbanner">Club Pro Available for a Lesson</font></td>
       </tr>
 <?

    while($row = mysqli_fetch_array($lessonpwresult)){
    $scourtdetailsquery = "SELECT DISTINCTROW reservations.time, users.firstname, users.lastname, courts.courtname, reservations.courtid, rankings.ranking, reservations.matchtype
                           FROM tblReservations reservations, tblUserRankings rankings, tblUsers users, tblCourts courts, tblCourtType courttype, tblkpUserReservations reservationdetails, tblClubUser clubuser
						   WHERE rankings.userid = users.userid
                           AND courttype.courttypeid = rankings.courttypeid
                           AND reservations.courtid = courts.courtid
                           AND users.userid = reservationdetails.userid
                           AND reservations.reservationid = reservationdetails.reservationid
                           AND courts.courttypeid = courttype.courttypeid
                           AND reservationdetails.reservationid=$row[reservationid]
                           AND reservations.usertype=0
						   AND users.userid = clubuser.userid
                           AND clubuser.clubid=".get_clubid()."
						   AND reservations.enddate IS NULL";


      // run the query on the database
                 $scourtdetailsresult = db_query($scourtdetailsquery);

                     //Print out details on the singles reservations
                             while($scourtdetailsrow = db_fetch_row($scourtdetailsresult)) {
                               echo "<tr>\n";
                               echo "<td width=\"30\"></td>\n";
                               echo "<td>\n";
                               echo "<font class=normal><a href=\"$wwwroot/users/court_reservation.php?time=$scourtdetailsrow[0]&courtid=$scourtdetailsrow[4]\">".gmdate(" l F j h:i A",$scourtdetailsrow[0])."</a> $scourtdetailsrow[1] $scourtdetailsrow[2] on $scourtdetailsrow[3] ";
                                if($scourtdetailsrow[6]==1){
                                    echo "<img src=\"$imagedir\boxleague.gif\">";
                                 }
                               echo "</td>\n";
                               echo "</tr>\n";
                           }

    }


 }


echo "</table>\n";


echo "\t\t<td>\n";
echo "\t<tr>\n";
echo "<table>\n";

}

}

?>
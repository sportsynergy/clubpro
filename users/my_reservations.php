<?

include("../application.php");
require_login();


$DOC_TITLE = "My Reservations";



include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
get_myreservations();
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/


function get_myreservations() {
/* Get the Reservation id all court types */
//Get General Club info

$clubquery = "SELECT timezone from tblClubs WHERE clubid=".get_clubid()."";
$clubresult = db_query($clubquery);
$clubobj = db_fetch_object($clubresult);

$tzdelta = $clubobj->timezone*3600;
$curtime =   mktime()+$tzdelta;



$simtzdelta = $clubobj->timezone;

$twoweeksago = $curtime - (14*86400);

?>
<table cellspacing="0" cellpadding="0" border="0" width="710" class="borderless">
     <tr>
     <td>
     <?
     
$anyboxesquery = "SELECT tblBoxLeagues.boxid
                  FROM tblBoxLeagues
                  WHERE (((tblBoxLeagues.siteid)=".get_siteid()."))";

$anyboxesresult = db_query($anyboxesquery);

if(mysql_num_rows($anyboxesresult)>0){

?>

     <img src="<?=$_SESSION["CFG"]["imagedir"]?>/boxleague.gif "\> <font class="normalsm"> Indicates League Match</font><br>
    
     <?
 }


 ?>
 <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lessonIcon.gif"\> <font class="normalsm"> Indicates Lesson</font><br>
 </td>
 </tr>
 
 <?

/**
 * Array to store all of the past reservations (doubles or singles)
 */
$pastReservationsArray = array();
$noupcomingreservations = false;

//********************************************************************************************
//first for previous reservations.  So in doing this we look for all singles reservations
// made between now and 14 days ago.


$curresidquery = "SELECT reservations.reservationid, reservations.time
                  FROM tblUsers users, tblReservations reservations, tblkpUserReservations reservationdetails
                  WHERE reservations.reservationid = reservationdetails.reservationid
                  AND users.userid = reservationdetails.userid
                  AND reservationdetails.userid=".get_userid()."
                  AND reservations.time>$twoweeksago
                  AND reservations.time<$curtime
                  AND reservations.usertype=0
				  AND reservations.enddate IS NULL";


// run the query on the database
$curresidresult = db_query($curresidquery);

while($curresidarray = db_fetch_array($curresidresult)) {    
	
	if( !isSinglesPartialReservation($curresidarray[reservationid]) ){
		$pastEntry = array($curresidarray['time'] => $curresidarray['reservationid']);
		array_push_associative($pastReservationsArray, $pastEntry);
	}
	

}
//********************************************************************************************
//Now for previous reservations.  So in doing this we look for all doubles reservations
// made between now and 14 days ago.



$myteamsquery = "SELECT tblTeams.teamid
                 FROM tblTeams INNER JOIN tblkpTeams ON tblTeams.teamid = tblkpTeams.teamid
                 WHERE (((tblkpTeams.userid)=".get_userid() .")) ";



// run the query on the database
$myteamsresult = db_query($myteamsquery);

while($myteamsrow = db_fetch_row($myteamsresult)) {

            $dcurresidquery = "SELECT reservations.reservationid, reservations.time, reservationdetails.*
                               FROM tblReservations reservations, tblkpUserReservations reservationdetails
							   WHERE reservations.reservationid = reservationdetails.reservationid
                               AND reservationdetails.userid=$myteamsrow[0]
                               AND reservations.time>$twoweeksago
                               AND reservations.time<$curtime
                               AND reservations.usertype=1
							   AND enddate IS NULL";

            // run the query on the database
            $dcurresidresult = db_query($dcurresidquery);

            while($dcurresidarray = db_fetch_array($dcurresidresult)) {
				//Only store full, past doubles reservations 
				if( !isDoublesPartialReservation($dcurresidarray[reservationid]) ){
					$pastEntry = array($dcurresidarray['time'] => $dcurresidarray['reservationid']);
					array_push_associative($pastReservationsArray, $pastEntry);
					
				}
				
				
  			}

 }

//********************************************************************************************
//Print out the reservations
if( sizeof($pastReservationsArray)>0  ){
echo "<tr><td><br><br>";
echo "<font class=bigbanner>Reservations made within the past two weeks</font><br>\n";
echo "<table width=650>\n";

ksort($pastReservationsArray);

foreach ( $pastReservationsArray as $reservationTime => $reservationID) {

   if(isDoublesReservation($reservationID)){
   	 printDoublesPastReservation($reservationID);
   }
   else{
   	printSinglesPastReservation($reservationID);
   }

}
  echo "</table>\n";


}
else{
	$noupcomingreservations = true;
}


//********************************************************************************************
//Now for future reservations.  So in doing this we look for all singles reservations
// made in the future


/**
 * Array to store all of the past reservations (doubles or singles)
 */
$upcomingReservationsArray = array();


$furresidquery = "SELECT reservations.reservationid, reservations.time
                  FROM tblUsers users, tblReservations reservations, tblkpUserReservations reservationdetails
                  WHERE reservations.reservationid = reservationdetails.reservationid
                  AND users.userid = reservationdetails.userid
                  AND reservationdetails.userid=".get_userid()."
                  AND reservations.time>$curtime
                  AND reservations.usertype=0
				  AND reservations.enddate IS NULL
                  ORDER BY reservations.time";



// run the query on the database
$furresidresult = db_query($furresidquery);



	while($furresidarray = db_fetch_array($furresidresult)) {
		
		$upcomingEntry = array( $furresidarray['reservationid']  => $furresidarray['time']);
		array_push_associative($upcomingReservationsArray, $upcomingEntry);;
		
	}


//********************************************************************************************
//Now for future reservations.  So in doing this we look for all doubles reservations
// made in the future.


$myteamsquery = "SELECT tblTeams.teamid
                 FROM tblTeams INNER JOIN tblkpTeams ON tblTeams.teamid = tblkpTeams.teamid
                 WHERE (((tblkpTeams.userid)=".get_userid() .")) ";

// run the query on the database
$myteamsresult = db_query($myteamsquery);

while($myteamsrow = db_fetch_row($myteamsresult)) {


            $dcurresidquery = "SELECT tblkpUserReservations.reservationid, tblReservations.time
                               FROM tblReservations INNER JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
                               WHERE (((tblkpUserReservations.userid)=$myteamsrow[0])
                               AND ((tblReservations.time)>$curtime)
                               AND ((tblReservations.usertype)=1))
							   AND enddate IS NULL";

            // run the query on the database
            $dcurresidresult = db_query($dcurresidquery);
			
			
            while($dcurresidarray = db_fetch_array($dcurresidresult)) {

            	$upcomingEntry = array(  $dcurresidarray['reservationid'] => $dcurresidarray['time'] );
            	array_push_associative($upcomingReservationsArray, $upcomingEntry);


            }

  }


if(sizeof($upcomingReservationsArray)>0){
echo "<tr><td><br><br>";
echo "<font class=bigbanner>Future Reservations</font>";
echo "<table width=650>\n";


asort($upcomingReservationsArray);

	foreach ( $upcomingReservationsArray as $reservationID => $reservationTime) {
	   
	
	   if(isDoublesReservation($reservationID)){
	   	 printDoublesUpcomingReservation($reservationID);
	   }
	   else{
	   	printSinglesUpcomingReservation($reservationID);
	   }
	  
	 
	}
 echo "</table>";
}
else{
	
	if($noupcomingreservations ){
		echo "<tr><td class=normal><br><br>\n";
		
		print "You have no upcoming reservations or any reservations made within the last two weeks. ";
		 echo "</td>\n";
		  echo "</table>";
	}

}


}



/**
 * Prints an upcoming singles reservation
 */
function printSinglesUpcomingReservation($reservationID){
	
  $imagedir = $_SESSION["CFG"]["imagedir"];
  $wwwroot = $_SESSION["CFG"]["wwwroot"];
  
  $courtdetailsquery = "SELECT reservations.time, users.firstname, users.lastname, courts.courtname, courts.courtid, reservations.matchtype
                        FROM tblUsers users, tblReservations reservations, tblCourts courts, tblkpUserReservations reservationdetails
						WHERE users.userid = reservationdetails.userid
                        AND reservations.reservationid = reservationdetails.reservationid
                        AND courts.courtid = reservations.courtid
                        AND reservations.reservationid=$reservationID";


  // run the query on the database
  $courtdetailsresult = db_query($courtdetailsquery);
  $numrows = mysql_num_rows($courtdetailsresult);

   //Find out if the player is in need of an opponent.  If only one row is in the result, we
   // can assume that the player doesn't have a partner
   if ($numrows==1){

                             while($courtdetailsrow = db_fetch_row($courtdetailsresult)) {
                              echo "<tr>\n";
                              echo "<td width=\"30\"></td>\n";
                              echo "<td>";

                              echo "<font class=normal>";
                              			
                              			//If solo reservation, only allow them to cancel 
                              			if($courtdetailsrow[5]==5){
                             			 	echo "<a href=\"$wwwroot/users/court_cancelation.php?time=$courtdetailsrow[0]&courtid=$courtdetailsrow[4]&cmd=cancelall\">";
                              			}
                              			else{
                              				echo "<a href=\"$wwwroot/users/court_cancelation.php?time=$courtdetailsrow[0]&courtid=$courtdetailsrow[4]\">";
                              			}

                              echo "".gmdate(" l F j h:i a",$courtdetailsrow[0])."";
                              echo "</a>";

									//If solo reservation, don't claim that we are looking for a match.'
									if($courtdetailsrow[5]==5){
										echo " I am playing by myself";
									}
									else{
										echo " I am still looking for a match";
									}
                              			
                              echo " on $courtdetailsrow[3]</font>";
                                     if($courtdetailsrow[5]==1){
                                          echo "<img src=\"$imagedir\boxleague.gif\">\n";
                                      }
                                      if($courtdetailsrow[5]==4){
                                          echo "<img src=\"$imagedir\lessonIcon.gif\">\n";
                                      }
                              echo "</td>";
                              echo "</tr>\n";
                              }


   }
   else{
                            while($courtdetailsrow = db_fetch_row($courtdetailsresult)) {
                                echo "<tr>\n";
                                echo "<td width=\"30\"></td>\n";
                                echo "<td>\n";
                                echo "<font class=normal><a href=\"$wwwroot/users/court_cancelation.php?time=$courtdetailsrow[0]&courtid=$courtdetailsrow[4]\">".gmdate(" l F j h:i a",$courtdetailsrow[0])."</a> $courtdetailsrow[1] $courtdetailsrow[2] and ";
                                 $courtdetailsrow = db_fetch_row($courtdetailsresult);
                                 echo "$courtdetailsrow[1] $courtdetailsrow[2] on $courtdetailsrow[3]</font>";

                                                 if($courtdetailsrow[5]==1){
                                                     echo "<img src=\"$imagedir\boxleague.gif\">";
                                                 }
                                                 if($courtdetailsrow[5]==4){
                                                     echo "<img src=\"$imagedir\lessonIcon.gif\">";
                                                 }
                                 echo "</td>";
                                echo "</tr>\n";
                            }

  }
	
}

/**
 *  Prints an upcoming doubles reservation
 */
function printDoublesUpcomingReservation($reservationID){

			$wwwroot = $_SESSION["CFG"]["wwwroot"];
			
            $ddetailsquery = "SELECT tblkpUserReservations.userid,tblUsers.firstname, tblUsers.lastname, tblkpUserReservations.outcome,tblReservations.time,tblCourts.courtname, tblCourts.courtid
                              FROM tblReservations INNER JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
                              INNER JOIN tblkpTeams ON tblkpTeams.teamid = tblkpUserReservations.userid
                              INNER JOIN tblUsers ON tblUsers.userid = tblkpTeams.userid
                              INNER JOIN tblCourts ON tblCourts.courtid = tblReservations.courtid
                              WHERE (((tblkpUserReservations.reservationid)=$reservationID))
                              ORDER BY tblReservations.time";

            // run the query on the database
            $ddetailsresult = db_query($ddetailsquery);

            //Get the number of rows in the result
            $numrows = mysql_num_rows($ddetailsresult);

            if ($numrows!=2){

                 if ((mysql_result($ddetailsresult,0,3))==0 && (mysql_result($ddetailsresult,2,3))==0){

                    //reset the results pointer
                    $int = mysql_data_seek($ddetailsresult,0);
                    while($ddetailsrow = db_fetch_row($ddetailsresult)) {

                           echo "<tr>\n";
                           echo "<td width=\"30\"></td>\n";
                           echo "<td>\n";
                           echo "<font class=normal><a href=\"$wwwroot/users/court_cancelation.php?time=$ddetailsrow[4]&courtid=$ddetailsrow[6]\">".gmdate(" l F j h:i a",$ddetailsrow[4])."</a> $ddetailsrow[1] $ddetailsrow[2] and ";
                           $ddetailsrow = db_fetch_row($ddetailsresult);
                           echo "$ddetailsrow[1] $ddetailsrow[2]";
                           $ddetailsrow = db_fetch_row($ddetailsresult);
                           echo " vs. ";
                           echo "$ddetailsrow[1] $ddetailsrow[2] and ";
                           $ddetailsrow = db_fetch_row($ddetailsresult);
                           echo "$ddetailsrow[1] $ddetailsrow[2] on $ddetailsrow[5] </font> </td>";
                           echo "</tr>\n";
                   }


                 }
              }
         
}

/**
 * Prints an past doubles reservation
 */
function printSinglesPastReservation($reservationID){
	
  $imagedir = $_SESSION["CFG"]["imagedir"];
  $wwwroot = $_SESSION["CFG"]["wwwroot"];
  	
  $courtdetailsquery = "SELECT reservations.time, users.firstname, users.lastname, courts.courtname, reservationdetails.outcome, reservations.matchtype
                        FROM tblReservations reservations, tblUsers users, tblkpUserReservations reservationdetails, tblCourts courts
						WHERE users.userid = reservationdetails.userid
                        AND reservations.reservationid = reservationdetails.reservationid
                        AND courts.courtid = reservations.courtid
                        AND reservations.reservationid=$reservationID
                        ORDER BY reservations.time";


  // run the query on the database
  $courtdetailsresult = db_query($courtdetailsquery);


  //Get the number of rows in the result
  $numrows = mysql_num_rows($courtdetailsresult);

   //Find out if the player is in need of an opponent.  If only one row is in the result, we
   // can assume that the player doesn't have a partner
   if ($numrows!=1){


          //Check to see which scores have already been reported.  Only if they haven't do
          //we print the reservation details to the screen.


           //If the score has not been reported
           if ((mysql_result($courtdetailsresult,0,4))==0 && (mysql_result($courtdetailsresult,1,4))==0){
            $int = mysql_data_seek($courtdetailsresult,0);

            while($courtdetailsrow = db_fetch_row($courtdetailsresult)) {
                    echo "<tr>\n";
                    echo "<td width=\"30\"></td>\n";
                    echo "<td>\n";


                    echo "<font class=normal>";
                    if($courtdetailsrow[5]==1 || $courtdetailsrow[5]==2){
                      echo "<a href=\"$wwwroot/users/report_scores.php?reservationid=$reservationID\">";
                    }
                     echo "".gmdate(" l F j h:i a",$courtdetailsrow[0])."";

                     if($courtdetailsrow[5]==1 || $courtdetailsrow[5]==2){
                         echo "</a>";
                     }

                    echo " $courtdetailsrow[1] $courtdetailsrow[2] and ";
                     $courtdetailsrow = db_fetch_row($courtdetailsresult);
                     echo "$courtdetailsrow[1] $courtdetailsrow[2] on $courtdetailsrow[3]</font>";

                     if($courtdetailsrow[5]==1){
                       echo "<img src=\"$imagedir\boxleague.gif\">";
                     }
                     if($courtdetailsrow[5]==4){
                       echo "<img src=\"$imagedir\lessonIcon.gif\">";
                     }
                     echo "</td>";
                     echo "</tr>\n";

             }


            }
            //If the score hasn't been reported
            else {
            $int = mysql_data_seek($courtdetailsresult,0);

                while($courtdetailsrow = db_fetch_row($courtdetailsresult)) {
                    echo "<tr>\n";
                    echo "<td width=\"30\"></td>\n";
                    echo "<td>\n";                    echo "<font class=normal>".gmdate(" l F j h:i a",$courtdetailsrow[0])."</a> $courtdetailsrow[1] $courtdetailsrow[2] and ";
                     $courtdetailsrow = db_fetch_row($courtdetailsresult);
                     echo "$courtdetailsrow[1] $courtdetailsrow[2] on $courtdetailsrow[3]</font>";

                     if($courtdetailsrow[5]==1){
                       echo "<img src=\"$imagedir\boxleague.gif\">";
                     }
                     if($courtdetailsrow[5]==4){
                       echo "<img src=\"$imagedir\lessonIcon.gif\">";
                     }
                     echo "</td>";
                     echo "</tr>\n";

                }
          }

   }
	
}

/**
 * Prints an past doubles reservation
 */
function printDoublesPastReservation($reservationID){
	
	$wwwroot = $_SESSION["CFG"]["wwwroot"];
	 $ddetailsquery = "SELECT reservationdetails.userid, users.firstname, users.lastname, reservationdetails.outcome, reservations.time, courts.courtname, reservations.matchtype
                              FROM tblUsers users, tblReservations reservations, tblkpUserReservations reservationdetails, tblkpTeams teamdetails, tblCourts courts
							  WHERE reservations.reservationid = reservationdetails.reservationid
                              AND teamdetails.teamid = reservationdetails.userid
                              AND users.userid = teamdetails.userid
                              AND courts.courtid = reservations.courtid
                              AND reservationdetails.reservationid=$reservationID
                              ORDER BY reservations.time";

            // run the query on the database
            $ddetailsresult = db_query($ddetailsquery);

            //Get the number of rows in the result
            $numrows = mysql_num_rows($ddetailsresult);

            if ($numrows!=2){

                 if ((mysql_result($ddetailsresult,0,3))==0 && (mysql_result($ddetailsresult,2,3))==0){

                    //reset the results pointer
                    $int = mysql_data_seek($ddetailsresult,0);
                    while($ddetailsrow = db_fetch_row($ddetailsresult)) {

                           echo "<tr>\n";
                           echo "<td width=\"30\"></td>\n";
                           echo "<td>\n";
                           echo "<font class=normal><a href=\"$wwwroot/users/report_scores.php?reservationid=$reservationID\">".gmdate(" l F j h:i a",$ddetailsrow[4])."</a> $ddetailsrow[1] $ddetailsrow[2] and ";
                           $ddetailsrow = db_fetch_row($ddetailsresult);
                           echo "$ddetailsrow[1] $ddetailsrow[2]";
                           $ddetailsrow = db_fetch_row($ddetailsresult);
                           echo " vs. ";
                           echo "$ddetailsrow[1] $ddetailsrow[2] and ";
                           $ddetailsrow = db_fetch_row($ddetailsresult);
                           echo "$ddetailsrow[1] $ddetailsrow[2] on $ddetailsrow[5] </font> \n";
                           echo "</td>";
                           echo "</tr>\n";
                   }


                 }
              }
         }
/*
 * Checks doubles reservations
 */
function isDoublesPartialReservation($resId){
	
	$partialQuery = "SELECT count(*) FROM tblkpUserReservations where reservationid = $resId and usertype <> 0";
	$partialResult  = db_query($partialQuery);
	$count = mysql_result($partialResult,0);
	
	if($count<2){
		return true;
	}
	else{
		return false;
	}
	
}


/**
 * Checks singles reservations
 */
 function isSinglesPartialReservation($resId){
 	
 	$partialQuery = "SELECT count(*) FROM tblkpUserReservations where reservationid = $resId and userid <> 0";
 	$partialResult  = db_query($partialQuery);
	$count = mysql_result($partialResult,0);
	
	if($count<2){
		return true;
	}
	else{
		return false;
	}
 }
 
?>
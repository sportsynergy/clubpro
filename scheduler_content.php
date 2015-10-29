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
* Classes list:
*/




require ($_SESSION["CFG"]["libdir"] . "/reservationlib.php");
require ($_SESSION["CFG"]["libdir"] . "/courtlib.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

//Load necessary libraries and set the wanturl to get back here.
$_SESSION["wantsurl"] = qualified_mewithq();
$siteprefs = getSitePreferences($siteid);
$_SESSION["siteprefs"] = $siteprefs;

if (isRequireLogin()) require_login();

//Only load the site ladders if the ranking scheme is configured as such

if (isLadderRankingScheme()) {
    $ladders = getClubSiteLadders($siteid);
    $_SESSION["ladders"] = $ladders;
}
$wwwroot = $_SESSION["CFG"]["wwwroot"];
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$courtGroupFromForm = $_REQUEST['courtGroupFromForm'];
$courtWindowStart = $_REQUEST['courtWindowStart'];

//Set the footer message

if (!isset($_SESSION["footermessage"])) {
    $footerMessage = getFooterMessage();
    $_SESSION["footermessage"] = $footerMessage;
}

//Get user log in the user in from the multiuser login form

if (isset($_POST["frompickform"])) {
    $user = load_user($_POST["userid"]);
    
    if ($user) {
        $_SESSION["user"] = $user;
    }
}

//Display the multiuser login form

if (isset($username) && isset($password) && !is_logged_in()) {
    $usersResult = getAllUsersWithIdResult($username, $clubid);
    
    if (mysql_num_rows($usersResult) > 1) {
        include ($_SESSION["CFG"]["templatedir"] . "/pick_user_form.php");
        die;
    } else {
        $user = verify_login($username, $password, false);
        
        if ($user) {
            $_SESSION["user"] = $user;
        } else {
            header("Location: $wwwroot/users/authenticationError.php");
        }
    }
}
$DOC_TITLE = "Sportsynergy Clubpro";
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");

// When a site has a court group configured set a session variable.  The first court group id will be the default.
// If a court group isn't found, just display the courts by displayorder (using the navigation arrows if necessary)

$grouping = "SELECT grouping.id from tblCourtGrouping grouping
							WHERE grouping.siteid = $siteid
							ORDER BY grouping.id";
$groupingResult = db_query($grouping);

//Update the court group session variable if set

if (isset($courtGroupFromForm)) {
    $_SESSION["courtGroup"][$siteid] = $courtGroupFromForm;
    unset($_SESSION["courtWindowStart"]);
}

// Set the Court Group ID

if (mysql_num_rows($groupingResult) > 0 && !isset($_SESSION["courtGroup"][$siteid])) {
    $_SESSION["courtGroup"][$siteid] = mysql_result($groupingResult, 0);
    unset($_SESSION["courtWindowStart"]);
}

//Unset the Court Group Id if no court groups found, this is needed if the data is updated while
// someone already has the some session variable set (very rare!)


if (mysql_num_rows($groupingResult) == 0) {
    unset($_SESSION["courtGroup"]);
}

//Get the courtWindowStart from the form (if exists) and set, otherwise get from session

if (isset($courtWindowStart)) {
    $_SESSION["courtWindowStart"][$siteid] = $courtWindowStart;
} else {
    $courtWindowStart = $_SESSION["courtWindowStart"][$siteid];
}

//If courtWindowStart is set get courts starting with that

if (isset($_SESSION["courtWindowStart"][$siteid])) {

    // Check to see if there is a grouping set up
    
    if (isset($_SESSION["courtGroup"][$siteid])) {
        $courtquery = "SELECT courts.* 
								FROM tblCourts courts, tblCourtGrouping courtgrouping, tblCourtGroupingEntry groupingentry
								WHERE courtgrouping.id = groupingentry.groupingid
								AND courtgrouping.id = " . $_SESSION["courtGroup"][$siteid] . "
								AND courts.courtid = groupingentry.courtid 
								AND courts.courtid >=$courtWindowStart
								AND courts.enable = 1
								ORDER BY courts.displayorder
								LIMIT 6";
    } else {
        $courtquery = "SELECT * 
							FROM tblCourts courts
							WHERE clubid=$clubid 
						    AND courtid >=$courtWindowStart
							AND siteid=$siteid 
							AND enable = 1
							ORDER BY courts.displayorder
						    LIMIT 6";
    }
}

// Get the courts for the defined group
elseif (isset($_SESSION["courtGroup"][$siteid])) {
    $courtquery = "SELECT courts.* 
				FROM tblCourts courts, tblCourtGrouping courtgrouping, tblCourtGroupingEntry groupingentry
				WHERE courtgrouping.id = groupingentry.groupingid
				AND courtgrouping.id = " . $_SESSION["courtGroup"][$siteid] . "
				AND courts.courtid = groupingentry.courtid 
				AND courts.enable = 1
				ORDER BY courts.displayorder
				LIMIT 6";
}

//If not set just get all of them (which should be under 6)
else {
    $courtquery = "SELECT * 
				FROM tblCourts courts
				WHERE clubid=$clubid 
				AND siteid=$siteid 
				AND enable = 1
				ORDER BY courts.displayorder
				LIMIT 6";
}
$currentCourtResult = db_query($courtquery);

//We are going to need this.
$totalCurrentCourts = mysql_num_rows($currentCourtResult);

//Get the Total Court Count.  If courts are grouped, get this, if not count all of the courts

if (isset($_SESSION["courtGroup"][$siteid])) {
    $totalCourtsQuery = "SELECT courts.* 
								FROM tblCourts courts, tblCourtGrouping courtgrouping, tblCourtGroupingEntry groupingentry
								WHERE courtgrouping.id = groupingentry.groupingid
								AND courtgrouping.id = " . $_SESSION["courtGroup"][$siteid] . "
								AND courts.courtid = groupingentry.courtid 
								AND courts.enable = 1
								ORDER BY courts.displayorder";
} else {
    $totalCourtsQuery = "SELECT court.courtid FROM tblCourts court where court.siteid = $siteid and court.enable = 1 ORDER BY court.displayorder";
}

//Get the total courts for the site (not all will be displayed)
$totalCourtResult = db_query($totalCourtsQuery);
$totalCourts = mysql_num_rows($totalCourtResult);

//Get General Club info
$clubquery = "SELECT * from tblClubs WHERE clubid='" . $clubid . "'";
$clubresult = db_query($clubquery);
$clubobj = db_fetch_object($clubresult);
$tzdelta = $clubobj->timezone * 3600;
$curtime = mktime() + $tzdelta;
$_SESSION["current_time"] = $curtime;
$simtzdelta = $clubobj->timezone;

//Allow Person to type in the date to load
$month = $_REQUEST['month'];
$date = $_REQUEST['date'];
$year = $_REQUEST['year'];
$daysahead = $_REQUEST['daysahead'];

if (isset($month) && isset($date) && isset($year)) {
    $currYear = $year;
    $currMonth = $month;
    $currDay = $date;
    $specDate = mktime(0, 0, 0, $month, $date, $year);
    $currDOW = getDOW(gmdate("l", $specDate));
}

//Set Current data and time
// set up some variables to identify the month, date and year to display

elseif (empty($daysahead) || !isset($daysahead)) {
    $currYear = gmdate("Y", $curtime);
    $currMonth = gmdate("n", $curtime);
    $currDay = gmdate("j", $curtime);
    $currDOW = getDOW(gmdate("l", $curtime));
} else {
    $currYear = gmdate("Y", $daysahead);
    $currMonth = gmdate("n", $daysahead);
    $currDay = gmdate("j", $daysahead);
    $currDOW = getDOW(gmdate("l", $daysahead));
}

//Check to see if an hour policy is available.
$hourspolicyQuery = "SELECT * from tblHoursPolicy WHERE siteid='$siteid'
                    AND year = $currYear
                    AND month = $currMonth
                    AND day = $currDay";
$hourPolicyResult = db_query($hourspolicyQuery);

if (mysql_num_rows($hourPolicyResult) > 0) {
    $policyArray = mysql_fetch_array($hourPolicyResult);
    $otimearray = explode(":", $policyArray[opentime]);
    $ctimearray = explode(":", $policyArray[closetime]);
    $ohour = $otimearray[0];
    $chour = $ctimearray[0];
}

//Get todays range and put it in an object
$todaystart = gmmktime(0, 0, 0, $currMonth, $currDay, $currYear);
$todayend = gmmktime(23, 0, 0, $currMonth, $currDay, $currYear);

// Calculate days ahead
$userdate = $curtime + (get_daysahead() * 86400);
$oYear = gmdate("Y", $userdate);
$oDay = gmdate("n", $userdate);
$oMonth = gmdate("j", $userdate);

//used in the date picker
$jsdate = "$oDay/$oMonth/$oYear";

if ($clubid) {
?>

<!-- Date and Drop Down Table begin -->

<table cellspacing="0" cellpadding="0" border="0"  width="100%" class="borderless">
  <tr>
    <td align="left" valign="top"><span class=bigbanner><? echo gmdate("l F j",$todaystart) ?></span>
      <? include($_SESSION["CFG"]["includedir"]."/include_datepicker.php") ?></td>
  </tr>
  
  <!-- Date and Drop Down Table end -->
</table>
</td>
</tr>
<?
		 
		 		//Check the display time
		 		if(  get_displaytime()  != null ){
		 			
	 		
					$displaytimearray = explode (":", get_displaytime() );
					$displayhour = $displaytimearray[0];
					$displayminute = $displaytimearray[1];
					
					$todaysYear = gmdate("Y", $curtime);
				    $todaysMonth = gmdate("n", $curtime);
				    $todaysDay = gmdate("j", $curtime);
	
				
					if( $todaycounter == ( $todaystart + 86400) 
						&& get_displaytime() != null
						&& !atleastof_priv(2)
						&& $curtime < mktime($displayhour,$displayminute, 0, $todaysMonth, $todaysDay,$todaysYear)+$tzdelta
						 ){
						
						$displaytime = mktime($displayhour,$displayminute, 0, $todaysMonth, $todaysDay,$todaysYear)+$tzdelta;
						?>
<tr>
  <td class="normal"><br>
    <br>
    The courts are not yet available.  
    Please check back later today after
    <?=gmdate("g",$displaytime)?>
    :
    <?=gmdate("i",$displaytime)?>
    <?=gmdate("a",$displaytime)?>
    . <br></td>
</tr>
<?
						
						print "</table>";
						include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
						die;
					}
				
		 		}
		 ?>
<?
	 		
	 		$grouping = "SELECT grouping.name, grouping.id 
							FROM tblCourtGrouping grouping
							WHERE grouping.siteid = $siteid
							ORDER BY grouping.id";
	
			 $groupingResult = db_query($grouping);
			 
			 if ( mysql_num_rows($groupingResult) > 0 ) { 
			 	
			 ?>
<tr>
  <td><table cellspacing="0" cellpadding="0" border="0"  width="100%" class="borderless">
      <tr height="15" >
        <td align="right" class="normal"><SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT">
							 function submitCourtGroupForm(action, groupid)
							{
						        document.courtGroupForm.courtGroupFromForm.value = groupid;
						        document.courtGroupForm.action = action;
						        document.courtGroupForm.submit();
							
							}
							</SCRIPT>
          <form name="courtGroupForm" method="post">
            <input type="hidden" name="courtGroupFromForm">
            <input type="hidden" name="daysahead" value="<?=$daysahead?>">
          </form>
          <?	$counter = 0;
							 	while($courtGroupRow = mysql_fetch_row($groupingResult)){ 
		
							 		if($counter > 0){
							 			print " | ";
							 		}
							 		
							 		// Don't display a link for the group that is loaded
							 		if($courtGroupRow[1] == $_SESSION["courtGroup"][$siteid] ){
							 			print $courtGroupRow[0];
							 		}else{ 

										//Print the link to the form
										
										print "<a STYLE=\"text-decoration:none\" href=\"javascript:submitCourtGroupForm('$wwwroot/clubs/".get_sitecode()."/index.php','$courtGroupRow[1]')\"> ".$courtGroupRow[0]."  </a>";
										
							 		 }
							 		
							 		++$counter;
		
							 	} ?>
          <br>
          <br></td>
      </tr>
    </table></td>
</tr>
<? }?>
<tr>
  <td><table cellspacing="0" cellpadding="0" border="0"  width="100%" class="borderless">
      <tr height="15">
        <td align="left" class="normal"><? printLeftCourtNavigationArrow($totalCourts, $totalCourtResult, $currentCourtResult, $totalCurrentCourts, $daysahead, $siteid); ?> <br></td>
        <td align="right" class="normal" ><? printRightCourtNavigationArrow($totalCourts, $totalCourtResult, $currentCourtResult, $totalCurrentCourts, $daysahead, $siteid); ?> <br></td>
      </tr>
    </table></td>
</tr>
<tr valign="top">
  <td><!-- Court Table Begin-->
    
    <table cellspacing="0" cellpadding="0" border="0"  width="100%" >
      <tr valign="top">
        <?
				
				
				//Reset the index (from the querys earlier)
				mysql_data_seek($currentCourtResult,0);

				$courtWidth = getCourtTableWidth($totalCurrentCourts);
				
                 while ($courtobj = db_fetch_object($currentCourtResult)) {


                  //Get Club Open time and assign time values if policy doesn't exist
                  $hoursquery = "SELECT * from tblCourtHours WHERE courtid='$courtobj->courtid' AND dayid ='$currDOW' ";
                  $hoursresult = db_query($hoursquery);
                  $hoursobj = db_fetch_object($hoursresult);
                  
                  
	                  
	              if(mysql_num_rows($hourPolicyResult)==0){
	                  $otimearray = explode (":", $hoursobj->opentime);
	                  $ctimearray = explode (":", $hoursobj->closetime);
	                  $ohour = $otimearray[0];
         		      $chour = $ctimearray[0];
                  }
         

                  //Get the Court Type Name
                  $ctquery = "SELECT * FROM tblCourtType WHERE courttypeid='".$courtobj->courttypeid."'";

                  // run the query on the database
                  $ctnameresult = db_query($ctquery);
                  $ctobj = db_fetch_object($ctnameresult);

                  //Get Reservations for the court
                  $curcourtquery = "SELECT time FROM tblReservations
                                   WHERE courtid = $courtobj->courtid
                                   AND time >=$todaystart
                                   AND time <= $todayend
								   AND enddate IS NULL";


                  $curcourtresult = db_query($curcourtquery);
                  $stack = array();

                  //Put all reservations for the court in an array
                  while ($curcourtrow = db_fetch_array($curcourtresult)){
                          array_push($stack, $curcourtrow['time']);
                  }

					
				  ?>
        <td ><table width=<?=$courtWidth?> cellpadding="0" cellspacing="0" align="center" class="scheduletable" >
          
 			<tr valign="top" >
              <th class="ct<?=$ctobj->courttypeid?>cl<?=$clubobj->clubid?>" style="height: 14px">
			 	<? if(isDisplayCourtTypeName()=='y'){ ?>
					<?=$ctobj->courttypename?>
				<? } ?>	
				</th>
            </tr>
            <tr>
              <?
                  /**
                   * Allow Program Administrators click on the court name to bring up event load screen, 
                   * As long as there is at least one event to book, that is.
                   */
                   if( 
	                   is_logged_in() //They have to be logged in to get role from session
	                   && get_roleid()=="2" //Only Program Administrators can do this
	                   && ( $curtime +  ($hoursobj->duration * 60 * 60) ) < ( (gmmktime ($chour,0,0,$currMonth,$currDay,$currYear) ) + ($hoursobj->hourstart * 60) )
	                 )  
                   {
                   	 
                   	 //	If a request parameter called daysahead is found, use this instead of the current time
					// When a page is being loaded for a time in the futre, this paramter is used.  If 
					// this isn't the case however, use the current time, unless daysahead is used to load
					// todays page, as daysahead is appeaded to when redirecting after any reservation is made.
                   	 
                   	if( isset($specDate)){
                   	 	$eventStartTime = $specDate;
                   	 }
                   	 else if( isset($daysahead)  ){
                   	 	$eventStartTime = $daysahead;
                   	 }
                   	 else{
                   	 	$eventStartTime = $curtime;
                   	 }
                   	 ?>
                   	 <th class="blackBackGround" id="courtname-header-<?=$courtobj->courtid?>"><a href="<?=$wwwroot?>/admin/event_load.php?time=<?=$eventStartTime?>&courtid=<?=$courtobj->courtid?>"><?=$courtobj->courtname?></a></th>

                   	 <?
                   }
                   else{ ?>
                   	 	<th id="courtname-header-<?=$courtobj->courtid?>"><?=$courtobj->courtname?></th>
                   <?
					}
                 
                  echo "</tr>\n";
				 
				  //To keep track of last timeslot displayed
				  $lastspot = 0;
				
                  for ( $i = gmmktime ($ohour,$hoursobj->hourstart,0,$currMonth,$currDay,$currYear); 
                  		$i< gmmktime ($chour,0,0,$currMonth,$currDay,$currYear); 
                  		$i = $i + $hoursobj->duration*3600){
								
						// Hours exception (override duration if applicable)
						$hoursQuery = "SELECT exception.duration FROM tblHoursException exception 
											WHERE exception.dayid = $currDOW 
											AND exception.siteid = $siteid
											AND exception.courtid = $courtobj->courtid
											AND exception.time = '".gmdate("G:i",$i)."'";
						
						$hoursResult = db_query($hoursQuery);
						
						if( mysql_num_rows($hoursResult) > 0){
							$hoursDuration = mysql_result($hoursResult,0);
							$i = $i + $hoursDuration * 60;
						}
										

                         //Check to see if the time is already reserved for this court.  If stack exists,
                         // in other words at least one reservation has been made for this court for
                         // this day.  The second thing we find out is if the current time is greater than
                         // the hourly interval.  When it is not we will do the following:
                          if ($stack)
                             {
                                 
                                 if ($curtime < $i)
                                 {
                                     
									//reservations made outside of the courts durations
									$current = current($stack);
								
									// Display reservation whether its been on the courts 
									// duration or not.
									if( $current > $lastspot  
										&& $current < $i){
										$i = $current;
									}

									// Make sure that all off-court duration reservations
									// are displayed.
									foreach ($stack as $stacktime){
 										
 										// if there is a value in the stack between i
 										// and what is $current, then set that to $i
 										if($i < $stacktime &&
 											$stacktime < $i + $hoursobj->duration*3600){
 											$i = $stacktime;
 											break;
 										}
 										
									}


								 if (in_array ($i, $stack)){
										
                                      //Get Reservation ID
                                      $residquery = "SELECT reservationid, eventid, usertype, guesttype, matchtype, lastmodifier, creator, locked, duration
                                                             FROM tblReservations
                                                             WHERE courtid=$courtobj->courtid
                                                             AND time=$i
															 AND enddate IS NULL";

                                       $residresult = db_query($residquery);
                                       $residobj = db_fetch_object($residresult);


                                       //If this is a doubles court get the team name
                                       // On the other hand if this is a singles court
                                       // just get the player names

                                      //************************************************************
                                      //Get the userids
                                      //************************************************************

                                      //Check if this is an event
                                      if($residobj->eventid){ 
                                      
                                      	printEvent($courtobj->courtid, $i, $residobj->eventid, $residobj->reservationid, false, $residobj->locked);
										$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                      
                                      } elseif($residobj->guesttype == 1){ 

		                                      	$guestquery = "SELECT name
		                                                         FROM tblkpGuestReservations 
		                                                         INNER JOIN tblReservations ON tblkpGuestReservations.reservationid = tblReservations.reservationid
		                                                         WHERE tblReservations.reservationid=".$residobj->reservationid;
                                      	 
                                                $guestresult = db_query($guestquery);
                                                $guestarray = mysql_fetch_array($guestresult);
                                                $guest1 = $guestarray['name'];
                                                $guest2 = "";
                                                	
	                                      	if( mysql_num_rows($guestresult) > 1){ 
				                                       $guestarray = mysql_fetch_array($guestresult); 
				                                       $guest2 = $guestarray['name'];
				                             }
      
											printGuestReservation($guest1, $guest2, $i, $courtobj->courtid, $residobj->matchtype, false );
											$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                      

                                      } else{ 

                                      	$useridresult = getSinglesReservationUser($residobj->reservationid);
                         			   
        
                                       //All singles reservations are made with a userype of
                                       //0. Doubles with a 1.
                                       if($residobj->usertype==0){

                                                 //Check to see if this was made needing a player
                                                 if ( mysql_num_rows($useridresult)==1){

                                                 	$useridarray = db_fetch_array($useridresult);
                                                 	$userid1 = $useridarray['userid']; 
                                                 	printPartialSinglesReservation($useridarray['userid'], $i, $courtobj->courtid, $residobj->matchtype, false, $residobj->locked, $residobj->creator, $useridarray['ranking']);
	$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
	                                                 
                                                     //Display two guys in the reservation
                                                  } else {
                                                  	
                                                  	$useridarray = db_fetch_array($useridresult);
                                                  	$userid1 = $useridarray['userid'];
                                                  	$useridarray = mysql_fetch_array($useridresult); 
                                                  	$userid2 = $useridarray['userid'];
                                                  	
                                                  		// a little defense for corrupt data
														if( empty($userid1) || empty($userid2) ){
															printEmptyReservation($i, $courtobj->courtid, false);
														} else{
                                                   	    printSinglesReservation($userid1, $userid2, $i, $courtobj->courtid, $residobj->matchtype, false, $residobj->locked, false, $residobj->creator, $residobj->reservationid);
														$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
														
		
                                               	    }
         
                                                  } 
                                                           
                                     } 

                                      //************************************************************
                                      //************************************************************
                                      //Get Doubles Team
                                      //************************************************************
                                      else{

	                                               $teamidquery = "SELECT userid, usertype
	                                                              FROM tblkpUserReservations
	                                                              WHERE reservationid=$residobj->reservationid
	                                                              ORDER BY usertype, userid";
	
	                                                $teamidresult = db_query($teamidquery);
	                                                $teamidarray = db_fetch_array($teamidresult);
                                                               
                                                  //Get First Team
                                                  if ( $teamidarray['userid']==0){
                                                         $teamidarray = db_fetch_array($teamidresult);
                                                        
                                                        // If usertype is 0 at this point then neither team is set.
                                                        if($teamidarray['usertype']==0){
                                                        	 printDoublesReservationSinglePlayer($teamidarray['userid'], $residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, false);
	$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                                        }
                                                        else{
															printDoublesReservationTeamWanted($teamidarray['userid'], $residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, false);
															$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                                     
                                                        }

                                                   }
                                                   else{
		                                                   //Check to see if this is single player or a team
		                                                   if($teamidarray['usertype']==1){
		                                                         
		                                                   	// team
		                                                     $teamOne = $teamidarray['userid'];
		
		                                                   }
		                                                   //If the usertype is set to 0 then we are talking about a single user.
		                                                   elseif($teamidarray['usertype']==0){
		                                                       
		                                                     	$playerOne = $teamidarray['userid'];
		                                                   }
		
		                                                   //Get Second Team
		                                                   $teamidarray = db_fetch_array($teamidresult);

                                                    	   // full team 2, regular reservation
		                                                   if($teamidarray['usertype']==1){          

				                                                   	// Print full reservation
				                                                   	if( isset($teamOne) ){
				                                                   		printDoublesReservationFull($teamOne, $teamidarray['userid'], $residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, false, false, $residobj->reservationid );
					$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
				                                                   	} 
				                                                   	// Print player wanted
				                                                   	else if( isset($playerOne) ){
				                                                   		
				                                                   		printDoublesReservationPlayerWanted($teamidarray['userid'], $playerOne, $residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, false );
					$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
				                                                   	}
		                                                   
		                                                   }elseif($teamidarray['usertype']==0){

		                                                   	  // print players wanted
		                                                   	  printDoublesReservationPlayersWanted($teamidarray['userid'],$playerOne, $residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, false );
			$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
		                                                 		
		                                                  }
				                                             
				                                          unset($teamOne);
				                                          unset($playerOne);   
                                                  }
                                         }
                                      
                                      }
                                      next($stack);
                                 } else{
                                    printEmptyReservation($i, $courtobj->courtid, false);
                                 }
								
                           }

                          //after current time but is in stack. So this means that the the court
                          // can be reserved but it is in a day with at least one other reservation
                          else{
                               

								//reservations made outside of the courts durations
								$current = current($stack);
								if( $current > $lastspot  && $current < $i){
									$i = $current;
								}
							
							if (in_array ($i, $stack)){

                                  //Get Reservation ID
                                  $residquery = "SELECT reservationid, eventid, usertype, guesttype, matchtype, creator, locked, duration
                                                 FROM tblReservations
                                                 WHERE courtid=$courtobj->courtid
                                                 AND time=$i
												 AND enddate IS NULL";

                                  $residresult = db_query($residquery);
                                  $residobj = db_fetch_object($residresult);

                                   //Get the userids
                                   if($residobj->guesttype == 0){
                                  
                                        //find out if the scores have been reported.  If they have, we
                                       // are going to use a different color as the tr background and
                                       // we will also not make it a link any more
                                       $isreportedquery ="SELECT sum(outcome)
                                                          FROM tblkpUserReservations
                                                          WHERE reservationid=$residobj->reservationid";

                                       $isreportedresult = db_query($isreportedquery);
                                       $outcome = mysql_result($isreportedresult,0);
                                       $scored = $outcome > 0 ? true : false;

                                     }
                                 

                                     if ($scored) {
                                        
                                         if($residobj->usertype==0){
                                         	
                                         	$useridresult = getSinglesReservationUser($residobj->reservationid);
										    $useridarray = db_fetch_array($useridresult);
										
                                         	$userid1 = $useridarray['userid'];
                                            $useridarray = mysql_fetch_array($useridresult); 
                                            $userid2 = $useridarray['userid'];
                                                
                                           printSinglesReservation($userid1, $userid2, $i, $courtobj->courtid, $residobj->matchtype, true, $residobj->locked, true, $residobj->creator, $residobj->reservationid);
$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                         	
                                     	} elseif($residobj->usertype==1) {
										
                                     		$teamidquery = "SELECT userid, usertype
	                                                              FROM tblkpUserReservations
	                                                              WHERE reservationid=$residobj->reservationid
	                                                              ORDER BY usertype, userid";
	
	                                    	$teamidresult = db_query($teamidquery);
	                                    	$teamidarray = db_fetch_array($teamidresult);
	                                    	$team1 = $teamidarray['userid'];
	                                    	$teamidarray = db_fetch_array($teamidresult);
	                                    	$team2 = $teamidarray['userid'];
	                                    	
                                     		printDoublesReservationFull($team1, $team2, $residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, true, true, $residobj->reservationid);
$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                     	
                                        }

                                           
                                        unset($scored);

                                  }
								 
                                  // There reservations have not been scored.
                                  else{

                                  
                                       if($residobj->eventid){ 
                                     
                                       		printEvent($courtobj->courtid, $i, $residobj->eventid, $residobj->reservationid, true, $residobj->locked);
											$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                       	
                                       }elseif($residobj->guesttype==1){
                                                    
                                              $guestquery = "SELECT name
	                                                 				FROM tblkpGuestReservations 
	                                                 				INNER JOIN tblReservations ON tblkpGuestReservations.reservationid = tblReservations.reservationid
	                                                 				WHERE tblReservations.reservationid=".$residobj->reservationid;
			                                  $guestresult = db_query($guestquery);
			                                  $guestarray = mysql_fetch_array($guestresult);  

			                                  $guest1 = $guestarray['name'];
			                                  $guestarray = mysql_fetch_array($guestresult);
			                                  $guest2 = $guestarray['name'];
			                                  
			                                  printGuestReservation($guest1, $guest2, $i, $courtobj->courtid, $courtobj->matchtype, true);
											  $i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
			                           
                                       } else{

		                                  //************************************************************
		                                  //Get the userids
		                                  //************************************************************
		                                  if($residobj->usertype==0){

		                                  		$useridresult = getSinglesReservationUser($residobj->reservationid);
     
                                                   if( mysql_num_rows($useridresult)==1 ) { 
                                                  		
                                                   		
                                                   	     $useridarray = db_fetch_array($useridresult);
                                                   	     $userid = $useridarray['userid'];	
                                                   	     printPartialSinglesReservation($userid, $i, $courtobj->courtid, $residobj->matchtype, true, $residobj->locked, $residobj->creator, $useridarray['ranking']);
$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                                   }
                                                   else{ 
                                                   
	                                                   	 $useridarray = db_fetch_array($useridresult);
                                                   	     $userid1 = $useridarray['userid'];		
                                                   	     $useridarray = db_fetch_array($useridresult);  
                                                   	     $userid2 = $useridarray['userid'];
	
														// a little defense for corrupt data
														if( empty($userid1) || empty($userid2) ){
															printEmptyReservation($i, $courtobj->courtid, true);
															
														} else{
                                                   	    printSinglesReservation($userid1, $userid2, $i, $courtobj->courtid, $residobj->matchtype, true, $residobj->locked, false, $residobj->creator, $residobj->reservationid);
$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                               	    }
                                                   		
                                                   } 
                                           
                                           
		                                  //************************************************************
		                                   //Get Doubles Team
		                                   //************************************************************
                                       		} elseif($residobj->usertype==1){
                                       
		                                       $teamidquery = "SELECT userid, usertype
			                                                       FROM tblkpUserReservations
			                                                       WHERE reservationid=$residobj->reservationid
			                                                       ORDER BY usertype, userid";
			
			                                    $teamidresult = db_query($teamidquery);
			                                    $teamidarray = db_fetch_array($teamidresult);
                                       
                                       			//Get First Team.  If one of the teams id is zero, either 1 or 2 players were looking 
                                                  if ( $teamidarray['userid']==0){
                                                  	
                                                  		$teamidarray = db_fetch_array($teamidresult);
                                                        
                                                        // If usertype is 0 at this point then neither team is set.
                                                        if($teamidarray['usertype']==0){
                                                        	printDoublesReservationSinglePlayer($teamidarray['userid'],$residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, true);
$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                                        }
                                                        else{                                                      	
                                                        	printDoublesReservationTeamWanted($teamidarray['userid'], $residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, true);
$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
                                                        }

                                                   }
                                                   else{
		                                                   //Check to see if this is single player or a team
		                                                   if($teamidarray['usertype']==1){
		                                                          
			                                                   	// team
			                                                   	$teamOne = $teamidarray['userid'];
		                                                   }
		                                                   //If the usertype is set to 0 then we are talking about a single user.
		                                                   elseif($teamidarray['usertype']==0){
		                                                       
			                                                   	// player
			                                                   	$playerOne = $teamidarray['userid'];
		                                                   }
		
		                                                   //Get Second Team
		                                                   $teamidarray = db_fetch_array($teamidresult);
		                                                   
		                                                   // full team 2, regular reservation
		                                                   if($teamidarray['usertype']==1){          

				                                                   	// Print full reservation
				                                                   	if( isset($teamOne) ){
				                                                   		printDoublesReservationFull($teamOne, $teamidarray['userid'], $residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, true, false, $residobj->reservationid );
				$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
				                                                   	} 
				                                                   	// Print player wanted
				                                                   	else if( isset($playerOne) ){
				                                                   		printDoublesReservationPlayerWanted($teamidarray['userid'], $playerOne, $residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, true );
				$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
				                                                   	}
		                                                   
		                                                   }elseif($teamidarray['usertype']==0){

		                                                   	  // print players wanted
		                                                   	  printDoublesReservationPlayersWanted($playerOne, $teamidarray['userid'],$residobj->locked, $residobj->matchtype, $i, $courtobj->courtid, $residobj->creator, true );
		$i = resetReservationPointer($courtobj->variableduration, $hoursobj->duration, $residobj->duration, $i);
		                                                 		
		                                                  }
		                                                  
		                                                  unset($teamOne);
		                                                  unset($playerOne);
		                                                  
                                                  }
                                       

                                       } 
               }
               } 
               
					next($stack);
					
				} else{
                               		printEmptyReservation($i, $courtobj->courtid, true);
                               }

                           } 
								
								
               
				} 

                         //if stack is not set.  This means that the particular resource does not have
                         // any reservations for this day
                              else {
                                    if ($curtime < $i) {
                                         printEmptyReservation($i, $courtobj->courtid, false);
                                    } else {
                                   		
                                   		printEmptyReservation($i, $courtobj->courtid, true);
                                   	
                                   }

                        } 
?>

					<? $lastspot = $i;	?>	
              <?  } ?>
          </table></td>
        <? unset($stack); ?>
        <? } ?>
        
        <!-- Court Table End--> 
      </tr>
    </table>
    <div style="padding-top: 2em;"></div>
    <table class="legend">
      <tr>
        <td class="seekingmatchcl<?=$clubid?>">Needs Players</td>
        <td class="eventcourt">Special Event</td>
        <td class="reportscorecl<?=$clubid?>">Record Score</td>
        <td class="reservecourtcl<?=$clubid?>">Plain Old Reservation</td>
      </tr>
    </table>
    <?php
}
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

?>

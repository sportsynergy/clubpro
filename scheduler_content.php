<?
/*
 * $LastChangedRevision: 856 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 13:29:36 -0500 (Mon, 14 Mar 2011) $

*/
require($_SESSION["CFG"]["libdir"]."/reservationlib.php");

 //Load necessary libraries and set the wanturl to get back here.
$_SESSION["wantsurl"] = qualified_mewithq();
$siteprefs = getSitePreferences($siteid);
$_SESSION["siteprefs"] = $siteprefs;
$wwwroot = $_SESSION["CFG"]["wwwroot"];

$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$courtGroupFromForm = $_REQUEST['courtGroupFromForm'];
$courtWindowStart = $_REQUEST['courtWindowStart'];

//Set the footer message
if( !isset($_SESSION["footermessage"]) ){
	$footerMessage = getFooterMessage();
	$_SESSION["footermessage"] = $footerMessage;
}


//Get user log in the user in from the multiuser login form
 if( isset($_POST["frompickform"] ) ){
    	$user = load_user($_POST["userid"] );
    	if($user){
			$_SESSION["user"] = $user;
    	}
  }
  
//Display the multiuser login form
if(isset($username) && isset($password) && !is_logged_in()  ){
	
	$usersResult = getAllUsersWithIdResult($username, $clubid);
	if( mysql_num_rows($usersResult) > 1  ){
        	 include($_SESSION["CFG"]["templatedir"]."/pick_user_form.php"); 
        	 die;
     }else{
	$user = verify_login($username, $password, false);
		if($user){
			$_SESSION["user"] = $user;
    	}else{
    		
    		header ("Location: $wwwroot/users/authenticationError.php");
    		
    	}
    	
	}
}
	
	
$DOC_TITLE = "Sportsynergy ClubPro"; 
include($_SESSION["CFG"]["templatedir"]."/header_yui.php");



// When a site has a court group configured set a session variable.  The first court group id will be the default.
// If a court group isn't found, just display the courts by displayorder (using the navigation arrows if necessary)

$grouping = "SELECT grouping.id from tblCourtGrouping grouping
							WHERE grouping.siteid = $siteid
							ORDER BY grouping.id";
	
$groupingResult = db_query($grouping);

//Update the court group session variable if set
if( isset($courtGroupFromForm) ){
	$_SESSION["courtGroup"][$siteid] = $courtGroupFromForm;
	unset($_SESSION["courtWindowStart"]);	
}

// Set the Court Group ID
if ( mysql_num_rows($groupingResult) > 0 && !isset($_SESSION["courtGroup"][$siteid] ) ) { 
	$_SESSION["courtGroup"][$siteid] = mysql_result($groupingResult,0);	
	unset($_SESSION["courtWindowStart"]);
}

//Unset the Court Group Id if no court groups found, this is needed if the data is updated while
// someone already has the some session variable set (very rare!)
if ( mysql_num_rows($groupingResult) == 0){
	unset($_SESSION["courtGroup"]);
}

//Get the courtWindowStart from the form (if exists) and set, otherwise get from session
if( isset($courtWindowStart) ){
	$_SESSION["courtWindowStart"][$siteid] = $courtWindowStart;	
}else{
	$courtWindowStart = $_SESSION["courtWindowStart"][$siteid];
}

//If courtWindowStart is set get courts starting with that
if( isset($_SESSION["courtWindowStart"][$siteid]) ){

	
	// Check to see if there is a grouping set up
	if( isset($_SESSION["courtGroup"][$siteid]) ){
			$courtquery = "SELECT courts.* 
								FROM tblCourts courts, tblCourtGrouping courtgrouping, tblCourtGroupingEntry groupingentry
								WHERE courtgrouping.id = groupingentry.groupingid
								AND courtgrouping.id = ".$_SESSION["courtGroup"][$siteid]."
								AND courts.courtid = groupingentry.courtid 
								AND courts.courtid >=$courtWindowStart
								AND courts.enable = 1
								ORDER BY courts.displayorder
								LIMIT 6";
	}
	else{
		
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
elseif( isset($_SESSION["courtGroup"][$siteid]) ){
	
	$courtquery = "SELECT courts.* 
				FROM tblCourts courts, tblCourtGrouping courtgrouping, tblCourtGroupingEntry groupingentry
				WHERE courtgrouping.id = groupingentry.groupingid
				AND courtgrouping.id = ".$_SESSION["courtGroup"][$siteid]."
				AND courts.courtid = groupingentry.courtid 
				AND courts.enable = 1
				ORDER BY courts.displayorder
				LIMIT 6";
}
//If not set just get all of them (which should be under 6)
else{
	
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


	if( isset($_SESSION["courtGroup"][$siteid]) ){
		
		$totalCourtsQuery = "SELECT courts.* 
								FROM tblCourts courts, tblCourtGrouping courtgrouping, tblCourtGroupingEntry groupingentry
								WHERE courtgrouping.id = groupingentry.groupingid
								AND courtgrouping.id = ".$_SESSION["courtGroup"][$siteid]."
								AND courts.courtid = groupingentry.courtid 
								AND courts.enable = 1
								ORDER BY courts.displayorder";
	}
	else{
		$totalCourtsQuery = "SELECT court.courtid FROM tblCourts court where court.siteid = $siteid and court.enable = 1 ORDER BY court.displayorder";
	}

//Get the total courts for the site (not all will be displayed)

$totalCourtResult = db_query($totalCourtsQuery);
$totalCourts = mysql_num_rows($totalCourtResult);



//Get General Club info
$clubquery = "SELECT * from tblClubs WHERE clubid='".$clubid."'";
$clubresult = db_query($clubquery);
$clubobj = db_fetch_object($clubresult);

$tzdelta = $clubobj->timezone*3600;
$curtime =   mktime()+$tzdelta;
$simtzdelta = $clubobj->timezone;

//Allow Person to type in the date to load
$month = $_REQUEST['month'];
$date = $_REQUEST['date'];
$year = $_REQUEST['year'];
$daysahead = $_REQUEST['daysahead'];

if(isset($month) && isset($date) && isset($year)){
	
	 $currYear = $year;
     $currMonth = $month;
     $currDay = $date;
     $specDate = mktime(0, 0, 0, $month, $date, $year)+$tzdelta;
     $currDOW = getDOW(gmdate("l", $specDate));
}
//Set Current data and time
// set up some variables to identify the month, date and year to display
elseif (empty($daysahead) || !isset($daysahead)   ){

      $currYear = gmdate("Y", $curtime);
      $currMonth = gmdate("n", $curtime);
      $currDay = gmdate("j", $curtime);
      $currDOW = getDOW(gmdate("l", $curtime));

}
else{
     $currYear = gmdate("Y",$daysahead);
     $currMonth = gmdate("n",$daysahead);
     $currDay = gmdate("j",$daysahead);
     $currDOW = getDOW(gmdate("l", $daysahead));

}
//Check to see if an hour policy is available.
$hourspolicyQuery = "SELECT * from tblHoursPolicy WHERE siteid='$siteid'
                    AND year = $currYear
                    AND month = $currMonth
                    AND day = $currDay";

$hourPolicyResult = db_query($hourspolicyQuery);


         if(mysql_num_rows($hourPolicyResult)>0){

                   $policyArray = mysql_fetch_array($hourPolicyResult);
                   $otimearray = explode (":", $policyArray[opentime]);
                   $ctimearray = explode (":", $policyArray[closetime]);
                   $ohour = $otimearray[0];
         		   $chour = $ctimearray[0];
         }
        


//Get todays range and put it in an object
$todaystart = gmmktime (0,0,0,$currMonth,$currDay,$currYear);
$todayend =  gmmktime (23,0,0,$currMonth,$currDay,$currYear);


if ($clubid){


?>
     

                   <!-- Date and Drop Down Table begin -->
                   <table cellspacing="0" cellpadding="0" border="0"  width="100%" class="borderless">
                          <tr>
       						
       						 <td align="left" valign="top">
                              <font class=bigbanner><? echo gmdate("l F j",$todaystart) ?></font><br>
                             </td>
								
							 <td colspan="3" align="center" valign="top">
								<form name="entryform" method="post" action="<?=$MEWQ?>">

			                              <select name="daysahead">
			                              <?
			
			                                //set the todaycounter
			                               $todaycounter = gmmktime (0,0,0,gmdate("n", $curtime),gmdate("j", $curtime),gmdate("Y", $curtime));
			                               $todaycounter -= get_daysahead() * 86400;
			                               
			                                //Set the Days Behind
			                               for ( $days = 0; $days < get_daysahead(); $days++){
			                               echo "<option value=$todaycounter>". gmdate(" l F j",$todaycounter) . "</option>";	
			                               $todaycounter = $todaycounter+86400;
			                               }
			                               
			                               //This is today.
			                              echo "<option value=$todaycounter selected>". gmdate(" l F j",$todaycounter) . "</option>";
			                               $todaycounter = $todaycounter+86400;
			                               $todaytime = $todaycounter;
			                               
			                               //Set the Days ahead. Oh, by the way, if the club administrator is logged in double the daysahead
			                               if(get_roleid()==2 && get_clubid()==$clubobj->clubid){
			
			                                   // Set the Days Ahead
			                                   for ( $days = 0; $days < 14; $days++){
			                                   echo "<option value=$todaycounter >". gmdate(" l F j",$todaycounter) . "</option>";
			                                   $todaycounter = $todaycounter+86400;
			                                   }
			                                }
			                               else{
			                                    for ( $days = 0; $days < get_daysahead(); $days++){
			                                    echo "<option value=$todaycounter >". gmdate(" l F j",$todaycounter) . "</option>";
			                                    $todaycounter = $todaycounter+86400;
			
			
			                                    }
			
			                                }
			
			                              ?>
			                              </select>
			
			                              <input type="submit" value="Submit">
	                              </form>
                      			</td>
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
						<tr><td class="normal">
						<br><br>
						The courts are not yet available.  
						Please check back later today after <?=gmdate("g",$displaytime)?>:<?=gmdate("i",$displaytime)?> <?=gmdate("a",$displaytime)?>.
						<br>
						</td></tr>
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
		 			<td>
		 				<table cellspacing="0" cellpadding="0" border="0"  width="100%" class="borderless">
							<tr height="15" >
							<td align="right" class="normal">
							<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT">
							 function submitCourtGroupForm(action, groupid)
							{
						        document.courtGroupForm.courtGroupFromForm.value = groupid;
						        document.courtGroupForm.action = action;
						        document.courtGroupForm.submit();
							
							}
							</SCRIPT>
					 			<form name="courtGroupForm" method="post">
					 			<input type="hidden" name="courtGroupFromForm">
					 			<input type="hidden" name="daysahead" value="<?=$daysahead?>"></form>
										
							 <?	$counter = 0;
							 	while($courtGroupRow = mysql_fetch_row($groupingResult)){ 
		
							 		if($counter > 0){
							 			print " | ";
							 		}
							 		
							 		if( isDebugEnabled(1) ) logMessage("Here is courtgrouprow" . $courtGroupRow[1] ." and here is whats in session " . $_SESSION["courtGroup"][$siteid] );
							 		
							 	
							 		
							 		// Don't display a link for the group that is loaded
							 		if($courtGroupRow[1] == $_SESSION["courtGroup"][$siteid] ){
							 			print $courtGroupRow[0];
							 		}else{ 

										//Print the link to the form
										
										print "<a STYLE=\"text-decoration:none\" href=\"javascript:submitCourtGroupForm('$wwwroot/clubs/".get_sitecode()."/index.php','$courtGroupRow[1]')\"> ".$courtGroupRow[0]."  </a>";
										
							 		 }
							 		
							 		++$counter;
		
							 	} ?>
							<br><br></td>
		 					</tr>
						</table>
 					</td>
 				</tr> 
			<? }?>
						
		 		<tr>
		 			<td>
		 				<table cellspacing="0" cellpadding="0" border="0"  width="100%" class="borderless">
							<tr height="15">
								<td align="left" class="normal"> <? printLeftCourtNavigationArrow($totalCourts, $totalCourtResult, $currentCourtResult, $totalCurrentCourts, $daysahead, $siteid); ?> <br></td>
								<td align="right" class="normal" >  <? printRightCourtNavigationArrow($totalCourts, $totalCourtResult, $currentCourtResult, $totalCurrentCourts, $daysahead, $siteid); ?> <br></td>
								
							</tr>
						</table>
		 			
		 			</td>
		 		</tr>
 	
 		
 		
         <tr valign="top">

             <td>

             <!-- Court Table Begin-->
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
				  
                  <td >
                  <table width=<?=$courtWidth?> cellpadding="0" cellspacing="0" align="center" class="scheduletable" >
                  
                  <tr valign="top" >
                  <th class="ct<?=$ctobj->courttypeid?>cl<?=$clubobj->clubid?>"><?=$ctobj->courttypename?></th>
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
                   	 
                   	 if( isset($daysahead)  ){
                   	 	$eventStartTime = $daysahead;
                   	 }
                   	 else{
                   	 	$eventStartTime = $curtime;
                   	 }
                   	 
                   	 echo "<th class=\"blackBackGround\"><a href=\"$wwwroot/admin/event_load.php?time=$eventStartTime&courtid=$courtobj->courtid\">$courtobj->courtname</a></th>";
                   }
                   else{
                   	 echo "<th>$courtobj->courtname</th>";
                   }
                 
                  echo "</tr>\n";


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
                         // in other words at least one reservation has been made for this resource for
                         // this day.  The second thing we find out is if the current time is greater than
                         // the hourly interval.  When it is not we will do the following:
                          if ($stack)
                             {
                                 if ($curtime < $i)
                                 {
                                     if (in_array ($i, $stack)){

                                      //Get Reservation ID
                                      $residquery = "SELECT reservationid, eventid, usertype, guesttype, matchtype, lastmodifier, creator
                                                             FROM tblReservations
                                                             WHERE courtid=$courtobj->courtid
                                                             AND time=$i
															 AND enddate IS NULL";

                                       $residresult = db_query($residquery);
                                       $residobj = db_fetch_object($residresult);

                                      if($residobj->guesttype == 0){
                         					$useridresult = getSinglesReservationUser($residobj->reservationid);
                         					$useridarray = db_fetch_array($useridresult);
                                       }
                                       elseif($residobj->guesttype == 1){

                                                $guestquery = "SELECT name
                                                               FROM tblkpGuestReservations INNER JOIN tblReservations ON tblkpGuestReservations.reservationid = tblReservations.reservationid
                                                               WHERE (((tblReservations.reservationid)=".$residobj->reservationid."))";
                                                $guestresult = db_query($guestquery);
                                                $guestarray = mysql_fetch_array($guestresult);
                                        }
                                       //If this is a doubles court get the team name
                                       // On the other hand if this is a singles court
                                       // just get the player names

                                      //************************************************************
                                      //Get the userids
                                      //************************************************************

                                      //Check if this is an event
                                      if($residobj->eventid){ ?>

                                                    <tr class="eventcourt">
                                                    <td align=center><font class="normalsm1" >
                                                    <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>">
                                                    <?=gmdate("g:i",$i)?><br>
                                                    
                                                    <?
                                                     $eventquery = "SELECT eventname FROM tblEvents
                                                                   WHERE eventid = $residobj->eventid";

                                                    $eventresult = db_query($eventquery);
                                                    $eventval = mysql_result($eventresult, 0);
                                                    
                                                    ?>
                                                    <?=$eventval?><br>
                                      <? }
                                      elseif($residobj->guesttype == 1){ ?>

                                                <tr class=reservecourtcl<?=$clubid?>>
                                                 <td align=center><font class="normalsm1">
                                                 <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>&cmd=cancelall">
                                                 <?=gmdate("g:i",$i)?><br>
                                                 <?=$guestarray['name']?><br>
                                                 <? if($residobj->matchtype != 5){ ?>
	                                                 vs.<br>
			                                         <?        $guestarray = mysql_fetch_array($guestresult); ?>
			                                         <?=$guestarray['name']?><br>
                                           		<? } ?>
		                                                 
                                      <? } else{ ?>

                                      <? //$useridarray = db_fetch_array($useridresult);
                                       //All singles reservations are made with a userype of
                                       //0. Doubles with a 1.
                                       if($residobj->usertype==0){

                                                            //Check to see if this was made needing a player
                                                            if ( mysql_num_rows($useridresult)==1){

                                                                	if($residobj->matchtype == 5){ ?>
	                                                                	<tr class=reservecourtcl<?=$clubid?>>
	                                                                    <td align=center><font class="normalsm1">
	                                                                    <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>&cmd=cancelall">
	                                                                    <?=gmdate("g:i",$i)?><br>
	                                                                     <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>
                                                                	<? }
	                                                                /*
	                                                                As of 042005 the club administrator will have the ability to
	                                                                make a reservation looking for a lesson.  This only applies to
	                                                                singles courts
	                                                                */
                                                                    elseif($residobj->matchtype == 4){ ?>
	                                                                    <tr class=seekingmatchcl<?=$clubid?>>
	                                                                    <td align=center><font class="normalsm1">
	                                                                    <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lessonIcon.gif">
	                                                                    <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>&userid=<?=$useridarray['userid']?>">
	                                                                     <?=gmdate("g:i",$i)?><br>
	                                                                     <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>
	                                                                    is available for a lesson
                                                                 
                                                                  <?  } else{ ?>
                                                                     <tr class=seekingmatchcl<?=$clubid?>>
                                                                     <td align="center"><font class="normalsm1">
                                                                     
                                                                      <? if($useridarray['matchtype']==1){ ?>
                                                                     		<img src="<?=$_SESSION["CFG"]["imagedir"]?>/boxleague.gif">
                                                                      <? } ?>
                                                                       <a title=Ranking:<?=$useridarray['ranking']?> href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>&userid=<?=$useridarray['userid']?>">
                                                                       <?=gmdate("g:i",$i)?><br>
                                                                       
                                                                        <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>
                                                                    	is looking for a match
                                                                   <? } ?>
                                                                    
                                                                <? 
                                                                //Display two guys in the reservation
                                                                } else { ?>

	                                                                 <tr class=reservecourtcl<?=$clubid?>>
	                                                                 <td align="center"><font class="normalsm1">
	                                                                 
		                                                                 <? if($useridarray['matchtype']==1){ ?>
		                                                                 	<img src="<?=$_SESSION["CFG"]["imagedir"]?>/boxleague.gif">
		                                                                 <? } if($useridarray['matchtype']==4){ ?>
		                                                                 	<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lessonIcon.gif">
		                                                                 <? } ?>
	                                                                 <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>">
	                                                                 <?=gmdate("g:i",$i)?><br>
	                                                                 <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>
	                                                                 vs.<br>
	                                                                 <? //Get the next guy
	                                                                    $useridarray = mysql_fetch_array($useridresult); ?>
	                                                                     <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>
                                                           		 <? } ?>
                                                            </a>
                                    <?  } 

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
                                                        	
                                                        	 $fullNameResult = getFullNameResultForUserId($teamidarray['userid']);
															  $userArray = mysql_fetch_array($fullNameResult); 
                                                        	
                                                        	?>
                                                        	
	                                                        <tr class=seekingmatchcl<?=$clubid?>>
	                                                        <td align=center><font class=normalsm1>
	                                                        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>&userid=<?=$teamidarray['userid']?>">
	                                                        <?=gmdate("g:i",$i)?><br>
	                                                        <?=printPlayer($userArray[0], $userArray[1],$teamidarray['userid'], $residobj->creator)?><br>
	                                                        is up for some doubles</a>
                                                        	<?
                                                        }
                                                        else{
															?>
	                                                        <tr class="seekingmatchcl<?=$clubid?>">
	                                                        <td align="center"><font class="normalsm1">
	                                                        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>&userid=<?=$teamidarray[0]?>">
	                                                        <?=gmdate("g:i",$i)?><br>
	                                                        <?=printTeam($teamidarray['userid'],  $residobj->creator)?>
	                                                        are looking for a match</a>
	                                                       <?
                                                        }

                                                   }
                                                   else{
		                                                   //Check to see if this is single player or a team
		                                                   if($teamidarray['usertype']==1){
		                                                          ?>   
		                                                           <tr class=reservecourtcl<?=$clubid?>>
		                                                           <td align=center><font class="normalsm1">
		                                                           <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>">
		                                                           <?=gmdate("g:i",$i)?><br>
		                                                           <?=printTeam($teamidarray['userid'],  $residobj->creator)?><br>
																	 vs. <br>
		                                                           <?
		
		                                                   }
		                                                   //If the usertype is set to 0 then we are talking about a single user.
		                                                   elseif($teamidarray['usertype']==0){
		                                                       
		                                                       $fullNameResult = getFullNameResultForUserId($teamidarray['userid']);
															  $userArray = mysql_fetch_array($fullNameResult); 
		                                                       
		                                                       ?>
		                                                        <tr class=seekingmatchcl<?=$clubid?>>
		                                                        <td align="center"><font class="normalsm1">
		                                                        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$i?>&courtid=<?=$courtobj->courtid?>&userid=<?=$teamidarray[0]?>">
		                                                        <?=gmdate("g:i",$i)?><br>
		                                                      	<?=printPlayer($userArray[0], $userArray[1], $teamidarray['userid'], $residobj->creator)?><br>
		                                                        is looking for a partner<br> vs. <br>
		                                                        <?
		                                                   }
		
		                                                   //Get Second Team
		                                                   $teamidarray = db_fetch_array($teamidresult);
		                                                   if($teamidarray['usertype']==1){
		                                                                
		                                                        printTeam($teamidarray['userid'], $residobj->creator);?></a>
		
		                                                  <?} elseif($teamidarray['usertype']==0){
		                                                    
		                                                    $fullNameResult = getFullNameResultForUserId($teamidarray['userid']);
															$userArray = mysql_fetch_array($fullNameResult); 
		                                                    
		                                                     ?>
		                                                        	<?=printPlayer($userArray[0], $userArray[1], $teamidarray['userid'], $residobj->creator)?><br>
		                                                            is looking for a partner</a>
		                                                     <?
		
		                                                  }
                                                  }
                                         }
                                      //************************************************************
                                      }
                                      }
                                      else{
                                       echo "<tr class=preopencourtcl$clubid>\n";
                                       echo " <td align=center><font class=normalsm1>
                                       <a href=$wwwroot/users/court_reservation.php?time=".sprintf("%d" , $i )."&courtid=$courtobj->courtid>
                                       " . gmdate("g:i",$i) ."<br>\n";
                                       }
                           }

                          //after current time but is in stack. So this means that the the resource
                          // can be reserved but it is in a day with at least one other reservation
                          else{
                               if (in_array ($i, $stack)){

                                  //Get Reservation ID
                                  $residquery = "SELECT reservationid, eventid, usertype, guesttype, matchtype, creator
                                                 FROM tblReservations
                                                 WHERE courtid=$courtobj->courtid
                                                 AND time=$i
												 AND enddate IS NULL";

                                  $residresult = db_query($residquery);
                                  $residobj = db_fetch_object($residresult);

                                  //Get the userids
                                  if($residobj->guesttype == 0){
                                  
                                        $useridresult = getSinglesReservationUser($residobj->reservationid);
										$useridarray = db_fetch_array($useridresult);
										
                                        //find out if the scores have been reported.  If they have, we
                                       // are going to use a different color as the tr background and
                                       // we will also not make it a link any more
                                       $isreportedquery ="SELECT outcome
                                                          FROM tblkpUserReservations
                                                          WHERE reservationid=$residobj->reservationid";

                                       $isreportedresult = db_query($isreportedquery);
                                       while ($isreportedarray = db_fetch_array($isreportedresult)){

                                                   if ( $isreportedarray[0]  > 0){
                                                         $scored = 1;
                                                   }
                                       }

                                   }
                                  elseif($residobj->guesttype == 1){
                                  $guestquery = "SELECT name
                                                 FROM tblkpGuestReservations INNER JOIN tblReservations ON tblkpGuestReservations.reservationid = tblReservations.reservationid
                                                 WHERE (((tblReservations.reservationid)=".$residobj->reservationid."))";
                                  $guestresult = db_query($guestquery);
                                  $guestarray = mysql_fetch_array($guestresult);
                                  }


                                     if (isset($scored)){
                                        //************************************************************
                                        //Get the userids
                                       //************************************************************

                                        echo "<tr class=reportedcourtcl$clubid>\n";
                                        echo "<td align=center><font class=normalsm1>\n";

                                         if($residobj->usertype==0){

                                             if($residobj->matchtype==1){ ?>
                                                 <img src="<?=$_SESSION["CFG"]["imagedir"]?>/boxleague.gif">
                                          
                                          <? }elseif($residobj->matchtype==4){ ?>
                                                 <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lessonIcon.gif">
                                             <? } ?>
                                            <?=gmdate("g:i",$i)?><br>
                                            <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>
                                            vs.<br>
                                            <? $useridarray = db_fetch_array($useridresult) ?>
                                            <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>										

										<?
                                       //************************************************************
                                      //Get Doubles Team
                                      //************************************************************
                                     } elseif($residobj->usertype==1) {

                                            		$teamidquery = "SELECT userid, usertype
	                                                              FROM tblkpUserReservations
	                                                              WHERE reservationid=$residobj->reservationid
	                                                              ORDER BY usertype, userid";
	
	                                                $teamidresult = db_query($teamidquery);
	                                                $teamidarray = db_fetch_array($teamidresult);
                                            
                                            ?> 
                                            
                                            <?=gmdate("g:i",$i)?><br>   
											 <?= printTeam($teamidarray['userid'],  $residobj->creator) ?><br>
                                             vs. <br>
                                             <?  //Get Second Team
                                             $teamidarray = db_fetch_array($teamidresult) ?>
											 <?= printTeam($teamidarray['userid'],  $residobj->creator) ?>
											
                                     <?      }

                                           unset($scored);

                                  }
								 // There reservations have not been scored.
                                  else{

                                    //if this is an event print it out otherwise don't print the
                                    //reservation

                                       if($residobj->eventid){ ?>
                                      		<tr class="eventcourt">
                                       	    <td align="center"><font class="normalsm1">

                                       <?=gmdate("g:i",$i)?><br>
                                       <? $eventquery = "SELECT eventname FROM tblEvents
                                                    WHERE eventid = $residobj->eventid";

                                       $eventresult = db_query($eventquery);
                                       $eventval = mysql_result($eventresult, 0); ?>
                                       <?=$eventval?><br>

										<?}elseif($residobj->guesttype==1){ ?>
                                                    <tr class=reportscorecl<?=$clubid?>>
                                                    <td align="center"><font class="normalsm1">
                                                    <?=gmdate("g:i",$i)?><br>
                                                     <?=$guestarray['name']?><br>
                                                     
                                                     <? //If its not a solo
                                                     if($residobj->matchtype != 5){ ?>
                                                     	 vs.<br>
                                                     	<? $guestarray = mysql_fetch_array($guestresult); ?>
                                                     	<?=$guestarray['name']?><br>
                                                    <? } ?>
                                                    
                                      <? } else{

		                                  //************************************************************
		                                  //Get the userids
		                                  //************************************************************
		                                  if($residobj->usertype==0){

                                                    /*
                                                     * Simply print out the player for solo reservations
                                                     */
                                                    if( $residobj->matchtype == 5){ ?>
		                                                    <tr class=postopencourt>
		                                                    <td align=center><font class=normalsm1>
		                                                    <?=gmdate("g:i",$i)?><br>
		                                                    <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>	
                                                   <?}elseif( mysql_num_rows($useridresult)==1 ) { ?>
                                                  			<tr class=reportscorecl<?=$clubid?>>
		                                                    <td align=center><font class=normalsm1>
		                                                    <?=gmdate("g:i",$i)?><br>
                                                   			<?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>	
                                                   			was looking for a match
                                                   <?}else{ ?>

		                                                   <tr class=reportscorecl<?=$clubid?>>
		                                                   <td align=center><font class=normalsm1>
		                                                   
		                                                   <? //If its a boxleague, display icon
		                                                   if($residobj->matchtype==1){ ?>
		                                                   		<img src="<?=$_SESSION["CFG"]["imagedir"]?>/boxleague.gif">
		                                                  
		                                                   <? //If its a lesson, display icon
		                                                   } if($residobj->matchtype==4){ ?>
		                                                      <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lessonIcon.gif">                
		                                                  
		                                                   <?  //take out the link to record the score if this is a lesson
		                                                  }   if($residobj->matchtype!=4){ ?>
		                                                         <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_scores.php?reservationid=<?=$residobj->reservationid?>">
		                                                  <? } ?>
                                                            <?=gmdate("g:i",$i)?><br>
                                                            <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?><br>
                                                             vs.<br>
                                                            <? $useridarray = db_fetch_array($useridresult); ?>
                                                            <?=printPlayer($useridarray[2], $useridarray[3], $useridarray[4], $residobj->creator)?>
                                                            
                                                            
                                                            <?  //again if this is a lesson, don't recod the score.
                                                            if($residobj->matchtype!=4){ ?>
                                                                </a>
                                                            <? } ?>
                                                   <? } 
                                           
                                           
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
                                       
                                       //Get First Team
                                                  if ( $teamidarray['userid']==0){
                                                         $teamidarray = db_fetch_array($teamidresult);
                                                        
                                                        // If usertype is 0 at this point then neither team is set.
                                                        if($teamidarray['usertype']==0){
                                                        	
                                                        	//Get the lone guy that didn't find any one to play with on the doubles court
                                                        	  $fullNameResult = getFullNameResultForUserId($teamidarray['userid']);
															  $userArray = mysql_fetch_array($fullNameResult); 

                                                        	?>
	                                                        <tr class=reportedcourtcl<?=$clubid?>>
	                                                        <td align=center><font class=normalsm1>
	                                                        <?=gmdate("g:i",$i)?><br>
	                                                        <?=printPlayer($userArray[0],  $userArray[1], $teamidarray['userid'], $residobj->creator)?><br>
	                                                        was up for some doubles</a>
                                                        	<?
                                                        }
                                                        else{
															?>
	                                                        <tr class="reportedcourtcl<?=$clubid?>">
	                                                        <td align=center><font class=normalsm1>
	                                                        <?=gmdate("g:i",$i)?><br>
	                                                        <?=printTeam($teamidarray['userid'],  $residobj->creator)?>
	                                                        were looking for a match</a>
	                                                       <?
                                                        }

                                                   }
                                                   else{
		                                                   //Check to see if this is single player or a team
		                                                   if($teamidarray['usertype']==1){
		                                                          ?>   
		                                                           <tr class=reportedcourtcl<?=$clubid?>>
		                                                           <td align="center"><font class="normalsm1">
		                                                           <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_scores.php?reservationid=<?=$residobj->reservationid?>">
		                                                           <?=gmdate("g:i",$i)?><br>
		                                                           <?=printTeam($teamidarray['userid'],  $residobj->creator)?><br>
																	 vs. <br>
		                                                           <?
		
		                                                   }
		                                                   //If the usertype is set to 0 then we are talking about a single user.
		                                                   elseif($teamidarray['usertype']==0){
		                                                       
		                                                      $fullNameResult = getFullNameResultForUserId($teamidarray['userid']);
															  $userArray = mysql_fetch_array($fullNameResult); 
		                                                       
		                                                       ?>
		                                                        <tr class=reportedcourtcl<?=$clubid?>>
		                                                        <td align=center><font class=normalsm1>
		                                                        <?=gmdate("g:i",$i)?><br>
		                                                      	<?=printPlayer($userArray[0], $userArray[1], $teamidarray['userid'], $residobj->creator)?><br>
		                                                        was looking for a partner<br> vs. <br>
		                                                        <?
		                                                   }
		
		                                                   //Get Second Team
		                                                   $teamidarray = db_fetch_array($teamidresult);
		                                                   if($teamidarray['usertype']==1){          
		                                                        printTeam($teamidarray['userid'], $residobj->creator);?>
		                                                        </a>
		                                                 <? }
		                                                  elseif($teamidarray['usertype']==0){
		                                                     
		                                                      $fullNameResult = getFullNameResultForUserId($teamidarray['userid']);
															  $userArray = mysql_fetch_array($fullNameResult); 
		                                                     
		                                                     
		                                                     ?>
		                                                        	<?=printPlayer($userArray[0], $userArray[1], $teamidarray['userid'], $residobj->creator)?><br>
		                                                            was looking for a partner
		                                                     <?
		
		                                                  }
                                                  }
                                       




                                       } ?>
                                  


                                   <? } ?>
                                    <? } ?>
                               <? } else{ ?>
                                       <tr class=postopencourt>
                                       <td align=center><font class=normalsm1>
                                       <?=gmdate("g:i",$i)?><br>
                                <? } ?>

                          <?  } ?>


                              <? } 

                         //if stack is not set.  This means that the particular resource does not have
                         // any reservations for this day
                              else {
                                    if ($curtime < $i)
                                         { ?>

                                          <tr class=preopencourtcl<?=$clubid?>>
                                          <td align="center"><font class="normalsm1" >
                                          <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=sprintf("%d" , $i )?>&courtid=<?=$courtobj->courtid?>">
                                          <?=gmdate("g:i",$i)?></a><br>
                                   <? } else { ?>
                                          <tr class="postopencourt">
                                          <td align="center"><font class="normalsm1">
                                          <?=gmdate("g:i",$i) ?><br>

                                  <? } ?>

                        <? } ?>

                         </font>
                         </td>
                         </tr>

                      <?  } ?>

                  </table>
                  </td>
                 <? unset($stack); ?>



                 <? } ?>


                <!-- Court Table End-->
               </tr>
               </table>
               

<?
}


include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function getCourtTableWidth($totalCurrentCourts){
	
	$width = 0;
	
	if($totalCurrentCourts == 1){
		$width = 450;
	}
	elseif($totalCurrentCourts == 2){
		$width = 300;
	}

	else{
		$width = 100;
	}
	
	return $width;
}

/*
 * Displays a hyperlink to navigation to the next/previous set of courts.
 * This function determine:
 *  a) if the link is to be displayed
 *  b) what paramter to pass in as to start the court display window ($courtWindowStart)
 * 
 * This will always display a full screen of courts.  So if there are 7 courts, clicking the
 * next arrow will then display courts 2-7 not just 7.  The reason for this because one
 * court on the page doesn't really do anyone any good, plus it looks weird.  As you might imagine
 * both of these function work the same, but do the inverse of the other one or whatever.
 * 
 */
 
/**
 * Prints out the left court navigation arrow
 */
function printLeftCourtNavigationArrow($totalCourts, $totalCourtResult, $currentCourtResult, $totalCurrentCourts,$daysahead, $siteid){
	
	$wwwroot = $_SESSION["CFG"]["wwwroot"];
	
	if( $totalCourts <= 6 ){
		return;
	}
	
	//If already displaying first court dont't print the link
	$veryFirstCourt = mysql_result($totalCourtResult,0);
	$firstCourtDisplayed = mysql_result($currentCourtResult,0);
	
	if($veryFirstCourt == $firstCourtDisplayed){
		return;
	}
	
	//Get 6 less than the first court or the very first court, which ever is less
	$nextCourtWindow = "SELECT court.courtid FROM tblCourts court WHERE court.siteid= $siteid AND court.courtid < $firstCourtDisplayed AND court.enable = 1 ORDER BY court.courtid DESC LIMIT 6";
	$nextCourtWindowResult = db_query($nextCourtWindow);

	while( $courtidArray = mysql_fetch_array($nextCourtWindowResult) ){

		$startCourtId = $courtidArray[0];
	}
	
	//Print the Form
	print "<form name=\"windowForm\" method=\"post\"><input type=\"hidden\" name=\"courtWindowStart\" value=\"$startCourtId\"><input type=\"hidden\" name=\"daysahead\" value=\"$daysahead\"></form>";	
	//Print the link to the form
	print "<br><a STYLE=\"text-decoration:none\" href=\"javascript:submitFormWithAction('windowForm','$wwwroot/clubs/".get_sitecode()."/index.php')\"> < Previous </a><br>";
	
}


/**
 * Print out the right court navigation array
 */
function printRightCourtNavigationArrow($totalCourts, $totalCourtResult, $currentCourtResult, $totalCurrentCourts, $daysahead, $siteid){
	
	$wwwroot = $_SESSION["CFG"]["wwwroot"];
	
	if( $totalCourts <= 6 ){
		return;
	}

	//If already displaying last court dont't print the link
	$veryLastCourt = mysql_result($totalCourtResult,$totalCourts-1);
	$firstCourtDisplayed = mysql_result($totalCourtResult,0);
	$lastCourtDisplayed = mysql_result($currentCourtResult,$totalCurrentCourts-1);
	
	// The courts will be in numerical order but not necessarily sequential.  If the 
	// last court is within the courts that are displayed, then there is no need to 
	// display the right navigation link.
	if($veryLastCourt >= $firstCourtDisplayed && $veryLastCourt <= $lastCourtDisplayed){
		return;
	}
	
	//Get 6 more than the last court or the very last court, which ever is bigger
	$nextCourtWindow = "SELECT court.courtid 
							FROM tblCourts court 
							WHERE court.siteid= $siteid 
							AND court.courtid > $lastCourtDisplayed 
							ORDER BY court.courtid ASC LIMIT 6";
							
	$nextCourtWindowResult = db_query($nextCourtWindow);
	$numberInNextWindow = mysql_num_rows($nextCourtWindowResult);
	

	//If the next window does not contain a full 6 courts, set the setCourtId to
	// 6 back from th elast one.
	if($numberInNextWindow < 6 ){
		$startCourtId = mysql_result($currentCourtResult,$numberInNextWindow);
	}
	//Otherwise set this to the first
	else{
		$startCourtId = mysql_result($nextCourtWindowResult,0);
	}
	//Print the Form
	print "<form name=\"windowForm\" method=\"post\"><input type=\"hidden\" name=\"courtWindowStart\" value=\"$startCourtId\"><input type=\"hidden\" name=\"daysahead\" value=\"$daysahead\"></form>";	
	//Print the link to the form
	print "<br><a STYLE=\"text-decoration:none\" href=\"javascript:submitFormWithAction('windowForm','$wwwroot/clubs/".get_sitecode()."/index.php')\"> Next >  </a><br>";
	
	
}




?>


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
* - getCourtTypeIdForCourtId()
* - getCourtTypeName()
* - getCourtTableWidth()
* - printLeftCourtNavigationArrow()
* - printRightCourtNavigationArrow()
* - printEvent()
* Classes list:
*/

/**
* Where the court is setup with variable duration, this will override the duration set on the court hour
* @param unknown_type $courtId
*/
function resetReservationPointer($variableduration, $courtdur, $reservationdur, $time){
	
		//existing reservations on these courts mightn't have the reservationdur set
		if($reservationdur == null){
			return $time;
		}
		
			$time = $time - $courtdur*3600;
			$time = $time + $reservationdur;
		
		return $time;
}

/**
 *
 * @param unknown_type $courtId
 */
function getCourtTypeIdForCourtId($courtId) {
    
    if (isDebugEnabled(1)) logMessage("courtlib.getCourtTypeIdForCourtId: Getting courttypeid for court $courtId");

    //Get the tblcourttype id for this court
    $courttypequery = "SELECT courttype.courttypeid
                                            FROM tblCourts courts, tblCourtType courttype
                                            WHERE courts.courttypeid = courttype.courttypeid
                                            AND courts.courtid ='$courtId'";
    $courttyperesult = db_query($courttypequery);
    $courttypearray = mysqli_fetch_array($courttyperesult);
    return $courttypearray[0];
}


function getReservationTypeIdForCourtId($courtId) {

    if (isDebugEnabled(1)) logMessage("courtlib.getReservationTypeIdForCourtId: Getting reservationtypeid for court $courtId");

    //Get the tblcourttype id for this court
    $courttypequery = "SELECT courttype.reservationtype
                            FROM tblCourts courts
                            INNER JOIN tblCourtType courttype ON courts.courttypeid = courttype.courttypeid
                                WHERE courts.courttypeid = courttype.courttypeid
                                AND courts.courtid ='$courtId'";
    $courttyperesult = db_query($courttypequery);
    $courttypearray = mysqli_fetch_array($courttyperesult);
    return $courttypearray[0];


}
/**
 * Simple getter that gets court type name
 */
function getCourtTypeName($courtTypeId) {
    $query = "SELECT courttype.courttypename 
				FROM tblCourtType courttype
				WHERE courttype.courttypeid = $courtTypeId";
    $result = db_query($query);
    $resultArray = mysqli_fetch_array($result);
    return $resultArray[0];
}

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function getCourtTableWidth($totalCurrentCourts) {
    $width = 0;
    
    if ($totalCurrentCourts == 1) {
        $width = 450;
    } elseif ($totalCurrentCourts == 2) {
        $width = 450;
    } elseif ($totalCurrentCourts == 3 || $totalCurrentCourts == 4) {
        $width = 150;
    } else {
        $width = 150;
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
function printLeftCourtNavigationArrow($totalCourts, $totalCourtResult, $currentCourtResult, $totalCurrentCourts, $daysahead, $siteid) {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    
    if ($totalCourts <= 6) {
        return;
    }

    //If already displaying first court dont't print the link
    $veryFirstCourtArray = mysqli_fetch_array($totalCourtResult);
    $veryFirstCourt = $veryFirstCourtArray[0];

    $firstCourtDisplayedArray = mysqli_fetch_array($currentCourtResult);
    $firstCourtDisplayed = $firstCourtDisplayedArray[0];
    
    if ($veryFirstCourt == $firstCourtDisplayed) {
        return;
    }

    //Get 6 less than the first court or the very first court, which ever is less
    $nextCourtWindow = "SELECT court.courtid FROM tblCourts court WHERE court.siteid= $siteid AND court.courtid < $firstCourtDisplayed AND court.enable = 1 ORDER BY court.courtid DESC LIMIT 6";
    $nextCourtWindowResult = db_query($nextCourtWindow);
    while ($courtidArray = mysqli_fetch_array($nextCourtWindowResult)) {
        $startCourtId = $courtidArray[0];
    }

    //Print the Form
    print "<form name=\"windowForm\" method=\"post\"><input type=\"hidden\" name=\"courtWindowStart\" value=\"$startCourtId\"><input type=\"hidden\" name=\"daysahead\" value=\"$daysahead\"></form>";

    //Print the link to the form
    print "<br><a STYLE=\"text-decoration:none\" href=\"javascript:submitFormWithAction('windowForm','$wwwroot/clubs/" . get_sitecode() . "/index.php')\"> < Previous </a><br>";
}
/**
 * Print out the right court navigation array
 */
function printRightCourtNavigationArrow($totalCourts, $totalCourtResult, $currentCourtResult, $totalCurrentCourts, $daysahead, $siteid) {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    
    if ($totalCourts <= 6) {
        return;
    }

    //If already displaying last court dont't print the link
    $veryLastCourt = mysqli_result($totalCourtResult, $totalCourts - 1);
    $firstCourtDisplayed = mysqli_result($totalCourtResult, 0);
    $lastCourtDisplayed = mysqli_result($currentCourtResult, $totalCurrentCourts - 1);
 
    
    // The courts will be in numerical order but not necessarily sequential.  If the last court is within the courts that are displayed, then there is no need to display the right navigation link.

    
    if ($veryLastCourt >= $firstCourtDisplayed && $veryLastCourt <= $lastCourtDisplayed) {
        return;
    }

    //Get 6 more than the last court or the very last court, which ever is bigger
    $nextCourtWindow = "SELECT court.courtid 
							FROM tblCourts court 
							WHERE court.siteid= $siteid 
							AND court.courtid > $lastCourtDisplayed 
							ORDER BY court.courtid ASC LIMIT 6";
    $nextCourtWindowResult = db_query($nextCourtWindow);
    $numberInNextWindow = mysqli_num_rows($nextCourtWindowResult);

    //If the next window does not contain a full 6 courts, set the setCourtId to 6 back from the last one.

    if ($numberInNextWindow < 6) {
        
        $startCourtId = mysqli_result($currentCourtResult, $numberInNextWindow);
    }

    //Otherwise set this to the first
    else {
        
        $startCourtIdArray = mysqli_fetch_array($nextCourtWindowResult);
        $startCourtId = $startCourtIdArray[0];
    }

    //Print the Form
    print "<form name=\"windowForm\" method=\"post\"><input type=\"hidden\" name=\"courtWindowStart\" value=\"$startCourtId\"><input type=\"hidden\" name=\"daysahead\" value=\"$daysahead\"></form>";

    //Print the link to the form
    print "<br><a STYLE=\"text-decoration:none\" href=\"javascript:submitFormWithAction('windowForm','$wwwroot/clubs/" . get_sitecode() . "/index.php')\"> Next >  </a><br>";
}
/**
 *
 * Takes the necessary parameters and prints the event marked up with HTML
 *
 * @param $courtid
 * @param $time
 * @param $eventid
 * @param $reservationid
 * @param $ispast
 * @param $locked
 */
function printEvent($courtid, $time, $eventid, $reservationid, $ispast, $locked) {
    $clubid = get_clubid();
    $eventquery = "SELECT events.eventname, events.playerlimit, events.eventid
              				FROM tblEvents events
              				WHERE events.eventid = $eventid ";
    $eventresult = db_query($eventquery);
    $eventarray = mysqli_fetch_array($eventresult);

    // if this is unlocked and needs players and is !ispast set the color to seeking match, otherwise event
    // $eventclass = $eventarray['playerlimit'] > 0 && !$ispast && $locked=="n" ? "seekingmatchcl$clubid" : "eventcourt";

    // Get the club participants

    $query = "SELECT user.firstname, user.lastname
             	 	FROM tblCourtEventParticipants participant, tblUsers user
             		WHERE participant.reservationid = $reservationid 
             		AND participant.enddate IS NULL
             		AND participant.userid = user.userid";
    $result = db_query($query);
    $spotsleft = $eventarray['playerlimit'] - mysqli_num_rows($result);
    $eventid = $eventarray['eventid'];
    
    if ($ispast) {
        $eventclass = "postopencourt";
    } else 
    if ($eventarray['playerlimit'] > 0 && $spotsleft > 0 && $locked == "n") {
        $eventclass = "seekingmatchcl$clubid event-$eventid";
    } else {
        $eventclass = "eventcourt event-$eventid";
    }


    $printtime = $ispast ? "$time" : "";
?>
	
	
	<tr class="<?=$eventclass?>">
        <td align="center" title="<?=$printtime?>">
        <span class="normalsm1" >
                                  
            <?
                                                 
              if( $eventarray['playerlimit']==0){
                    
              	//only provide links to administrators, who then have the option to cancel or change
              	if( (get_roleid()==2 || get_roleid()==4) && !$ispast ){ ?>
              		 
              	 <a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$time?>&courtid=<?=$courtid?>">
                 <? if($locked=="y"){ ?>
                       <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
                 <? }?>  
              	<?=gmdate("g:i",$time)?><br>
              		<?=$eventarray['eventname']?><br>  
              	 </a>
              		
              <?	}else{ ?>
              	<? if($locked=="y"){ ?>
                       <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
                 <? }?> 
              		<?=gmdate("g:i",$time)?><br>
              		<?=$eventarray['eventname']?><br>  
              		
              <? } ?>
                                      
             <? } else{ 
             
             	if($ispast){ ?>
             	 <? if($locked=="y"){ ?>
                       <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
                 <? }?>  
             		<?=gmdate("g:i",$time)?><br>
					 <?=$eventarray['eventname']?> <br>
             	<? } else{ ?>
             		<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$time?>&courtid=<?=$courtid?>">
	             <? if($locked=="y"){ ?>
                       <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
                 <? }?>       
             	<?=gmdate("g:i",$time)?><br>
					 <?=$eventarray['eventname']?> <br>
					 </a>
             	<?} 
             	
             	while($array = mysqli_fetch_array($result)){ ?>
             		<div class="normalsm"> <? print "$array[firstname] $array[lastname]";?></div>
             	<? 
             	}
             	
             	if(!$ispast && $locked=="n"){ ?>
             		<span class="italitcsm"><?=$spotsleft?> <?=$spotsleft==1?"spot":"spots"?> left</span>
             	<? }  ?>
             	
             	
             	
             	
            <? } ?>
          </span>
          </td>
          </tr>
           
         
  <? 
}


function printResourceReservation($courtid, $time, $creatorid, $reservationid, $inpast){
    
    $clubid = get_clubid();
    $fullNameResult = getFullNameResultForUserId($creatorid);
    $userArray = mysqli_fetch_array($fullNameResult); 

    if($inpast){
        $trclass = "postopencourt"; 
    } else {
        $trclass =  "reservecourtcl$clubid";
    }
    
    ?>
         <tr class="<?=$trclass?>">
                <td align="center" title="<?=$printtime?>">
                    <span class="normalsm1">
                         
                           <? if(!$inpast) { ?>
                                <a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$time?>&courtid=<?=$courtid?>">
                            <? } ?>
                            
                          <?=gmdate("g:i",$time)?><br>
                          <?=printPlayer($userArray[0],  $userArray[1], $playerOne, $creator)?>
                            
                          <? if(!$inpast ) { ?>
                                </a>
                            <? } ?>                                      
                        
                    </span>
                </td>
         </tr>
   <?

}


function printDoublesReservationSinglePlayer($userid, $lock, $matchType, $time, $courtid, $creator, $inpast){
	
	
		if( isDebugEnabled(1) ) logMessage("scheduler_content.printDoubles single player where lock is $lock");
		
		$clubid = get_clubid();
		$fullNameResult = getFullNameResultForUserId($userid);
		$userArray = mysqli_fetch_array($fullNameResult); 

		$trclass = $inpast ? "postopencourt" : "seekingmatchcl$clubid";
		$verb = $inpast ? "needed" : "needs";
        $printtime = $inpast ? "$time" : "";

		  ?>
            <tr class="<?=$trclass?>">
               <td align="center" title="<?=$printtime?>">
	               <span class="normalsm1">
	                    <? if($lock=="y"){ ?>
	                     <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
	                      <? }?>
	                      
	                      <? if(!$inpast) { ?>
	                      		<a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$time?>&courtid=<?=$courtid?>&userid=<?=$userid?>">
	                      <? } ?>
	                      
                          <?=gmdate("g:i",$time)?><br>
	                      <?=printPlayer($userArray[0],  $userArray[1], $playerOne, $creator)?>
	                      
                          <? if( is_logged_in() || isShowPlayerNames()){ ?>
                          <?=$verb?> some players
	                      <? } ?>

	                       <? if(!$inpast) { ?>
	                      		</a>
	                      <? } ?>
	                      
	                 </span>
                 </td>
             </tr>
<? 

} 

/**
 * 
 * @param $teamid
 * @param $lock
 * @param $matchType
 * @param $time
 * @param $courtid
 * @param $creator
 * @param $inpast
 */
function printDoublesReservationTeamWanted($teamid, $lock, $matchType, $time, $courtid, $creator, $inpast){
	
	if( isDebugEnabled(1) ) logMessage("scheduler_content.printDoubles team wanted");
	
	$clubid = get_clubid();
	$trclass = $inpast ? "postopencourt" : "seekingmatchcl$clubid";
	$verb = $inpast ? "needed" : "need";
	$printtime = $inpast ? "$time" : "";

	?>
	 <tr class="<?=$trclass?>">
	       <td align="center" title="<?=$printtime?>">
	         <span class="normalsm1">
	          <? if($lock=="y"){ ?>
				     <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
			  <? }?>
			  
			  <? if( !$inpast){ ?>
			  	<a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$time?>&courtid=<?=$courtid?>&userid=<?=$teamid?>">
			  <? } ?>
			  
			  <?=gmdate("g:i",$time)?><br>
	          <?=printTeam($teamid,  $creator)?>

              <? if( is_logged_in() || isShowPlayerNames()){ ?>
	         	<?=$verb?> some players
	         <? } ?>	

	          <? if( !$inpast){ ?>
			  	</a>
			  <? } ?>	
	         	
	        </span>
	     </td>
	  </tr>

<?

}

/**
 * 
 * Prints a reservation where only one player is needed
 * 
 * @param $teamid
 * @param $userid
 * @param $lock
 * @param $matchType
 * @param $time
 * @param $courtid
 * @param $creator
 * @param $inpast
 */
function printDoublesReservationPlayerWanted($teamid, $userid, $lock, $matchType, $time, $courtid, $creator, $inpast){
	
	if( isDebugEnabled(1) ) logMessage("courtlib.printDoublesReservationPlayerWanted: teamid $teamid userid $userid courtid $courtid");
	
	$clubid = get_clubid();
	$fullNameResult = getFullNameResultForUserId($userid);
	$userArray = mysqli_fetch_array($fullNameResult); 
	
	$verb = $inpast ? "needed" : "needs";
	    
	$trclass = $inpast ? "postopencourt" : "seekingmatchcl$clubid";
    $printtime = $inpast ? "$time" : "";
		                                                     
	?>
		 <tr class=<?=$trclass?>>
		        <td align="center" title="<?=$printtime?>">
		        	<span class="normalsm1">
					<? if($lock=="y"){ ?>
					        <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
					<? }?> 
					
					<? if(!$inpast) { ?>
                       <a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$time?>&courtid=<?=$courtid?>&userid=<?=$userid?>">
                     <? } ?>
                     
		             <?=gmdate("g:i",$time)?><br>
		             <?=printTeam($teamid, $creator)?>
				     <?=printPlayer($userArray[0], $userArray[1], $userid, $creator)?>
                    
                    <? if( is_logged_in() || isShowPlayerNames()){ ?>
					        <?=$verb?> a partner</br>
                     <? } ?>

		    		<? if(!$inpast) { ?>
                       </a>
                     <? } ?>
		      		</span>
		    </td>
		   </tr>
		<?
}

/**
 * 
 * Prints out a reservation where two people were looking for partners
 * 
 * @param $userid1
 * @param $userid2
 * @param $lock
 * @param $matchType
 * @param $time
 * @param $courtid
 * @param $creator
 * @param $inpast
 */
function printDoublesReservationPlayersWanted($userid1, $userid2, $lock, $matchType, $time, $courtid, $creator, $inpast){
	
	if( isDebugEnabled(1) ) logMessage("courtlib.printDoublesReservationPlayersWanted: userid $userid1 userid2 $userid2 courtid $courtid");
	
	  $fullNameResult = getFullNameResultForUserId($userid1);
	  $userArray = mysqli_fetch_array($fullNameResult); 
	  $clubid = get_clubid();
	  $verb = $inpast ? "needed" : "needs";
	  
	  $trclass = $inpast ? "postopencourt" : "seekingmatchcl$clubid";
      $printtime = $inpast ? "$time" : "";
		                                                     
	?>
		 <tr class=<?=$trclass?>>
		        <td align="center" title="<?=$printtime?>">
		        	<span class="normalsm1">
					<? if($lock=="y"){ ?>
					        <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
					<? }?> 
					
				   <? if(!$inpast) { ?>
                       <a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$time?>&courtid=<?=$courtid?>&userid=<?=$userid1?>">
                     <? } ?>
					
		             <?=gmdate("g:i",$time)?><br>
				    <?=printPlayer($userArray[0], $userArray[1], $userid1, $creator)?>
					 
                     <? if( is_logged_in() || isShowPlayerNames()){ ?>       
                            <?=$verb?> a partner</br>
				     <? } ?> 

                    <?
				    $fullNameResult = getFullNameResultForUserId($userid2);
				    $userArray = mysqli_fetch_array($fullNameResult); 
				    
				    ?>
				    
				    <?=printPlayer($userArray[0], $userArray[1], $userid2, $creator)?>
				    
                    <? if( is_logged_in() || isShowPlayerNames()){ ?>      
                            <?=$verb?> a partner</br> 
                   <? } ?>  
					
					<? if(!$inpast) { ?>
                       </a>
                     <? } ?>        
					            
		      		</span>
		    </td>
		   </tr>
		<?
		                                                     
		                                                     
}

/**
 * Prints a reservation when there are four players
 * 
 * @param $teamid1
 * @param $teamid2
 * @param $lock
 * @param $matchType
 * @param $time
 * @param $courtid
 * @param $creator
 * @param $inpast
 */
function printDoublesReservationFull($teamid1, $teamid2, $lock, $matchType, $time, $courtid, $creator, $inpast, $scored, $reservationid){
	

	$clubid = get_clubid();

	if($inpast){
		
		if($scored){
			$trclass = "reportedcourtcl$clubid";
		} else if($matchType==0 || $matchType==1 || $matchType==2 || $matchType==3) {
			$trclass = "reportscorecl$clubid";
		} else {
			$trclass = "postopencourt";
		}
		
	} else {
		$trclass =  "reservecourtcl$clubid";
	}
	
	$printtime = $inpast ? "$time" : "";

	?>
		 <tr class="<?=$trclass?>">
		        <td align="center" title="<?=$printtime?>">
		        	<span class="normalsm1">
		        		 <? if($matchType==4){ ?>
		          				<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lessonIcon.gif">
		    			<? } 
		    			
			            if($lock=="y"){ ?>
							<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
						<? }?>
					     
					       <? if(!$inpast) { ?>
                       			<a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$time?>&courtid=<?=$courtid?>">
                     		<? } else if(!$scored && ($matchType==0 || $matchType==1 || $matchType==2 || $matchType==3)) {?>
                     			<a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_scores.php?reservationid=<?=$reservationid?>">
                     		<?}  ?>
                     		
		                  <?=gmdate("g:i",$time)?><br>
		                  <?=printTeam($teamid1, $creator)?>
						  <?=printTeam($teamid2, $creator)?>	
						  
						  <? if(!$inpast && !$scored && ($matchType==0 || $matchType==1 || $matchType==2 || $matchType==3)) { ?>
                       			</a>
                     		<? } ?>										 
		                
		        	</span>
		        </td>
		 </tr>
   <?
}
 

/**
 * Prints a slot for a reservation
 * 
 * @param unknown_type $time
 * @param unknown_type $courtid
 * @param unknown_type $inpast
 */
function printEmptyReservation($time, $courtid, $inpast){
	
	$clubid = get_clubid();
	$trclass = $inpast ? "postopencourt" : "preopencourtcl$clubid";
	$printtime = $inpast ? "$time" : "";

    ?>
    
	<tr class="<?=$trclass?>">
     	<td align="center" title="<?=$printtime?>">
     		<span class="normalsm1">
     			<? if(!$inpast) { ?>
                      <a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<? print(sprintf("%d" , $time ))?>&courtid=<?=$courtid?>">
                 <? } ?>
                 <? print (gmdate("g:i",$time)."<br>")?>
                 <? if(!$inpast){ ?>
                 </a>
                 <? } ?>
               
            </span>
          </td>
      </tr>
          <?

}

/**
 * Prints out a guest reservation
 * 
 * @param unknown_type $guest1
 * @param unknown_type $guest2
 * @param unknown_type $time
 * @param unknown_type $courtid
 * @param unknown_type $inpast
 */
function printGuestReservation($guest1, $guest2, $time, $courtid, $matchtype, $inpast){
	
	$clubid = get_clubid();
	$trclass = $inpast ? "postopencourt" : "reservecourtcl$clubid";
	$printtime = $inpast ? "$time" : "";

	?>
	  <tr class="<?=$trclass?>">
            <td align="center" title="<?=$printtime?>">
            <span class="normalsm1">
            	<? if(!$inpast){?>
                 <a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$time?>&courtid=<?=$courtid?>&cmd=cancelall">
                <? } ?>  
            	
                <?=gmdate("g:i",$time)?><br>
                  <?=$guest1?><br>
                  <? if($matchtype != 5){ ?>
			            <?=$guest2?><br>
                  <? } ?>
                  
                  <? if(!$inpast){?>
                  </a>
                  <? } ?>
             </span>
             </td>
       </tr>      
 <?                  
}


/**
 * Prints out a singles reservation
 * 
 * @param $userid1
 * @param $userid2
 * @param $time
 * @param $courtid
 * @param $matchtype
 * @param $inpast
 */
function printSinglesReservation($userid1, $userid2, $time, $courtid, $matchtype, $inpast, $locked, $scored, $creator, $reservationid){
	

	$clubid = get_clubid();
	
	// Set the css
	if($inpast){
		
		if($scored){
			$trclass = "reportedcourtcl$clubid";
		} else if($matchtype==0 || $matchtype==1 || $matchtype==2 || $matchtype==3) {
			$trclass = "reportscorecl$clubid";
		} else {
			$trclass = "postopencourt";
		}
		
	} else {
		 $trclass =  "reservecourtcl$clubid";
	}
	
	
	 $fullName1Result = getFullNameResultForUserId($userid1);
	 $user1Array = mysqli_fetch_array($fullName1Result); 
	 
	 $fullName2Result = getFullNameResultForUserId($userid2);
	 $user2Array = mysqli_fetch_array($fullName2Result);
	
     $printtime = $inpast ? "$time" : "";
	
	?>
		<tr class="<?=$trclass?>">
            <td align="center" title="<?=$printtime?>">
            <? if($matchtype==4){ ?>
		          <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lessonIcon.gif">
		    <? } else if($matchtype==1){ ?>
                 <img src="<?=$_SESSION["CFG"]["imagedir"]?>/boxleague.gif">
            <? } 
            if($locked=="y"){ ?>
				<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
			<? }?>
            
            <span class="normalsm1">
            	
            	<? if(!$inpast) { ?>
                       <a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$time?>&courtid=<?=$courtid?>">
                 <? } else if(!$scored && ($matchtype==0 ||$matchtype==1 || $matchtype==2 || $matchtype==3)) {?>
                     	<a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_scores.php?reservationid=<?=$reservationid?>">
                 <?}  ?>
                
                <?=gmdate("g:i",$time)?><br/>
                <?=printPlayer($user1Array[0], $user1Array[1], $userid1, $creator)?>
                <?=printPlayer($user2Array[0], $user2Array[1], $userid2, $creator)?>
                
                 <? if(!$inpast && !$scored && ($matchtype==0 || $matchtype==1 || $matchtype==2 || $matchtype==3)) { ?>
                       </a>
                 <? } ?>	
                  
              </span>
              </td>
           </tr>
	 <? 
}



/**
 * Prints out a singles reservation if there wasn't more that one person signed up
 */
function printPartialSinglesReservation($userid, $time, $courtid, $matchtype, $inpast, $locked, $creator, $ranking){
	
	$clubid = get_clubid();

	if($inpast){
		$trclass = "postopencourt";
	} elseif($matchtype == 5){
		$trclass = "reservecourtcl$clubid";
	} else {
		$trclass = "seekingmatchcl$clubid";
	}
	
	 $fullNameResult = getFullNameResultForUserId($userid);
	 $userArray = mysqli_fetch_array($fullNameResult); 
	 
	 $lessontext = $inpast ? "was available for a lesson" : "is available for a lesson";
	 $portext = $inpast ? "needed a player" : "needs a player";
	 $buddytext = $inpast ? "needed a buddy" : "needs a buddy";
	
      $printtime = $inpast ? "$time" : "";
	?>
		<tr class="<?=$trclass?>">
            <td align="center" title="<?=$printtime?>">
             
	       <? if($matchtype==4){ ?>
		          <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lessonIcon.gif">
		    <? } else if($matchtype==1){ ?>
                 <img src="<?=$_SESSION["CFG"]["imagedir"]?>/boxleague.gif">
            <? } 
            
            if($locked=="y"){ ?>
				<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
			<? }?>
			
            <span class="normalsm1">
            
             <? if(!$inpast){?>
                 
                  <? if($matchtype==5) { ?>
                 	<a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php?time=<?=$time?>&courtid=<?=$courtid?>&cmd=cancelall" title="ranking: <?=$ranking?>">
                 <? } else {  ?>
	                 <a class="r-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?=$time?>&courtid=<?=$courtid?>&userid=<?=$userid?>" title="ranking: <?=$ranking?>">
	              <? } ?> 
	                
             <? } ?>
             
             <?=gmdate("g:i",$time)?><br> 
             <?=printPlayer($userArray[0], $userArray[1], $userid, $creator)?>
             
              <?

              if( is_logged_in() || isShowPlayerNames()){   
                  if($matchtype==4){ 
                 	print $lessontext;
                  } 
                  else if($matchtype==0 || $matchtype==1|| $matchtype==2){
                  	print $portext;
                  }
                  else if($matchtype==3){
                  		print $buddytext;
                  }
             } 
              
              if(!$inpast){?>
                  </a>
              <? } ?>
              
              </span>
            </td>
         </tr>
             
            
  <?
	

}
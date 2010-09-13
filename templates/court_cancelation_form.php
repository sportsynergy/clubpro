<?php
/*
 * $LastChangedRevision: 774 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-09-11 11:30:35 -0500 (Sat, 11 Sep 2010) $

*/
?>
<script language="Javascript">
<!--
// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
  if (document.getElementById) {
   for (var sch = 0; sch < dform.length; sch++) {
    if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
   }
  }
return true;
}

//-->


function disable(disableIt)
{
        document.entryform.name1.disabled = disableIt;
        document.entryform.name2.disabled = disableIt;
}


function unsetPlayer(id)
{

    id.value = "";
       

}

function enable()
{
     document.entryform.name1.disabled = "";
     document.entryform.name2.disabled = "";
}
function enableEvent()
{
	document.entryform.events.disabled = "";
	
}
function disableevent(disableIt)
{
        document.entryform.events.disabled = disableIt;

}
function disabledoubles(disableIt)
{
        document.entryform.name1.disabled = true;
        document.entryform.name2.disabled = true;
        document.entryform.name3.disabled = true;
        document.entryform.name4.disabled = true;

}

function enabledoubles()
{
     document.entryform.name1.disabled = "";
     document.entryform.name2.disabled = "";
     document.entryform.name3.disabled = "";
     document.entryform.name4.disabled = "";
}

document.onkeypress = function (aEvent)
{

	    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
          return false; // this will prevent bubbling ( sending it to children ) the event!
    }
  	
}


</script>


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>





<table cellspacing="0" cellpadding="20" width="400" >
  <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class=generictable>
     <form name="entryform" method="post" action="./court_cancelation.php" onSubmit="SubDisable(this);" autocomplete="off">
     <table width="500">

     <?
      
      //Program administrators have the option of rearranging a doubles reservation
      //after it has already occured.  This variable is used for enabling/disabling
      //functions to support this feature.
      $isPageBeingLoadedForPastReservation = isInPast($time);

       //Get the players from the reservation (doubles will be teams, singles will be players)
       $playersQuery = "SELECT reservationdetails.userid, reservationdetails.usertype, reservations.usertype
                        FROM tblReservations reservations, tblkpUserReservations reservationdetails
                        WHERE reservations.reservationid = reservationdetails.reservationid
                        AND reservations.time=$time
						AND reservations.courtid=$courtid
						AND reservations.enddate IS NULL
						ORDER BY reservationdetails.usertype DESC, reservationdetails.userid DESC";
		
		
       $playersResult = db_query($playersQuery);

	     $playerRow = mysql_fetch_array($playersResult);
	     $player1Type = $playerRow[1];
	     
	     if( $player1Type == 1){
	     	
	     	$teamArray = getUserIdsForTeamId($playerRow[0]);
	     	$player1Id = $teamArray[0];
	     	$player2Id = $teamArray[1];
	     	
	     }
	     else{

	     	$player1Id = $playerRow[0];
	     }
	     	
		 //Get Next
	     $playerRow = mysql_fetch_array($playersResult);
	     $player2Type = $playerRow[1];
	     
	      if( $player2Type == 1){

	     	$teamArray = getUserIdsForTeamId($playerRow[0]);
	     	$player3Id = $teamArray[0];
	     	$player4Id = $teamArray[1];
	     	
	     	
	     }
	     else{

			//if this is a double reservation and there are two singles 
			// the second guy is actually considered player 3
			if($playerRow[2] == 1){
				$player3Id = $playerRow[0];
			}
			else{
				$player2Id = $playerRow[0];
			}
	     	
	     }

       $courtTypeQuery = "SELECT * from tblReservations WHERE time = $time and courtid = $courtid AND enddate IS NULL";
       $courtTypeResult = db_query($courtTypeQuery);
       $courtTypeArray = mysql_fetch_array($courtTypeResult);
       $default = "";
       $lessonordisabled = "";
       $lessonordefault = "";
       $lessoneedisabled = "";
       $lessondefault = "";


       	//Get the email addresses of the players to put into the mailto
	    $emailArray = getEmailAddressesForReservation($courtTypeArray['reservationid']);
	    $emailString = implode(",", $emailArray);
                 

       if($courtTypeArray['eventid']!=0){
       	?>
       	<tr><td class="normal">
       	
       	
       		<? if(isReoccuringReservation($time, $courtid)){ ?>
       		This is a reoccuring event.  What do you want to do?<br><br>
       		<input type="radio" name="cancelall" value="3" onclick="disableevent(this.checked)" checked>Cancel just this occurrence <br>	
       		<input type="radio" name="cancelall" value="9" onclick="disableevent(this.checked)" >Cancel all occurrences <br>
       			
       		<? } else{ ?>
       		<input type="radio" name="cancelall" value="3" onclick="disableevent(this.checked)" checked>Cancel the event <br>	
       		<? } ?>
       	 
       	 
	   	 <input type="radio" name="cancelall" value="10" onclick="javascript:enableEvent()">Update the event
			<select name="events" disabled>
               

                <?
                //Get Club Players
                 $eventDrpDown = get_site_events(get_siteid());
                 
                 $eventQuery = "SELECT eventid from tblReservations 
									WHERE time=$time AND courtid=$courtid";
                 
                 $eventIdResult = db_query($eventQuery);
                 $eventId = mysql_result($eventIdResult,0);
                 
                 while($row = mysql_fetch_row($eventDrpDown)) {

					$selected = "";
                      
				 	if($row[0] == $eventId){
	                    $selected = "selected";
	                 }
					 
					 echo "<option value=\"$row[0]\" $selected>$row[1]</option>\n";
                     unset($selected);
                     
                 }            
       ?>
       </select>
         </td>
       </tr>
        <?
       	
       }
       //if singles (courtype 0)
       elseif($courtTypeArray['usertype']==0){   ?>


				<?
				 // Give Administrators option to change reservation around that they are not in.
		       if( isCAButNotinReservation($courtid, $time) ){
		       	$AMPAORCANOTINRESERVATION = true;
		       }
				
				?>
                <tr>
                <td colspan="2" class="normal">
                <input type="radio" name="cancelall" value="3" onclick="disable(this.checked)" <? if($isPageBeingLoadedForPastReservation){?>disabled <? }else{ ?> checked <? }?>>Cancel the whole frickin' reservation <br>
                <input type="radio" name="cancelall" value="4" onclick="javascript:enable()" <? if($isPageBeingLoadedForPastReservation){?>checked <? } ?>>Change players in the reservation
                </td>
                </tr>

                 <tr>
                 <td>
                 
	             <input id="name1" name="name1" type="text" size="30" class="form-autocomplete" value="<?=getFullNameForUserId($player1Id)?>" <? if(!$isPageBeingLoadedForPastReservation){?>disabled <? } ?>  />
	              <?err($errors->name1)?>
	             <input id="id1" name="player1" type="hidden" size="30" value="<?=$player1Id?>"/>
	    			<script>
	                <?
	                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
	                 pat_autocomplete( array(
							'baseUrl'=> "$wwwroot/users/ajaxServer.php",
							'source'=>'name1',
							'target'=>'id1',
							'className'=>'autocomplete',
							'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
							'progressStyle'=>'throbbing',
							'minimumCharacters'=>3,
							));
	                 ?>

                </script>
                 
                </td>
                 <td>
					<input id="name2" name="name2" type="text" size="30" class="form-autocomplete" value="<?=getFullNameForUserId($player2Id)?>" <? if(!$isPageBeingLoadedForPastReservation){?>disabled <? } ?> />
	             	 <?err($errors->name2)?>
	             	<input id="id2" name="player2" type="hidden" size="30" value="<?=$player2Id?>"/>
		    		<script>
		                <?
		                 
		                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
		                 pat_autocomplete( array(
								'baseUrl'=> "$wwwroot/users/ajaxServer.php",
								'source'=>'name2',
								'target'=>'id2',
								'className'=>'autocomplete',
								'parameters'=> "action=autocomplete&name={name2}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
								'progressStyle'=>'throbbing',
								'minimumCharacters'=>3,
								));
		           
		                 ?>
	                </script>
                </td>
                </tr>
               
                <? if(!$isPageBeingLoadedForPastReservation){?>
                <tr>
                	<td class="italitcsm" colspan="2">To Remove someone from the reservation, just delete their name<br><br></td>
                </tr>
                <? }?>
                <? 
                //Only display the note if there are email addresses to send to.
                if(count($emailArray)>0){?>
                <tr>
                	<td colspan="2" class="normal"><a href="mailto:<?=$emailString?>">Send Note</a></td>
                </tr>
                <? } ?>
                       
                 
     <?  }
       //else this is a doubles reservation
       elseif($courtTypeArray['usertype']==1) {


       ?>

               <tr>
                 <td colspan="2" class="normal">

                <input type="radio" name="cancelall" value="3" onclick="disabledoubles(this.checked)" <? if($isPageBeingLoadedForPastReservation){?>disabled <? }else{ ?> checked <? }?>>Cancel the whole frickin' reservation <br>
                <input type="radio" name="cancelall" value="8" onclick="javascript:enabledoubles()" <? if($isPageBeingLoadedForPastReservation){?>checked <? } ?>>Change players in the reservation

                </td>
                </tr>

              
                 <tr>
                 <td>
                 <?
                  $teamArray = getUserIdsForTeamId($player1Id);
                 ?>
                 
                    <input id="name1" name="name1" type="text" size="30" class="form-autocomplete" value="<?=getFullNameForUserId($player1Id)?>" <? if(!$isPageBeingLoadedForPastReservation){?>disabled <? } ?> onchange="unsetPlayer(id1);"/>
	             	 <?err($errors->name1)?>
	             	<input id="id1" name="player1" type="hidden" value="<?=$player1Id?>"/>
		    		<script>
		                <?
		                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
		                 pat_autocomplete( array(
								'baseUrl'=> "$wwwroot/users/ajaxServer.php",
								'source'=>'name1',
								'target'=>'id1',
								'className'=>'autocomplete',
								'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
								'progressStyle'=>'throbbing',
								'minimumCharacters'=>3,
								));
		           
		                 ?>
	                </script>
                </td>
                 <td>
                 
                <input id="name2" name="name2" type="text" size="30" class="form-autocomplete" value="<?=getFullNameForUserId($player2Id)?>" <? if(!$isPageBeingLoadedForPastReservation){?>disabled <? } ?> onchange="unsetPlayer(id2);"/>
	             <?err($errors->name2)?>
	            <input id="id2" name="player2" type="hidden" value="<?=$player2Id?>"/>
		    		<script>
		                <?
		                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
		                 pat_autocomplete( array(
								'baseUrl'=> "$wwwroot/users/ajaxServer.php",
								'source'=>'name2',
								'target'=>'id2',
								'className'=>'autocomplete',
								'parameters'=> "action=autocomplete&name={name2}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
								'progressStyle'=>'throbbing',
								'minimumCharacters'=>3,
								));
		           
		                 ?>
	                </script>
                </td>
                </tr>


                <tr>
                 <td>
                
                <input id="name3" name="name3" type="text" size="30" class="form-autocomplete" value="<?=getFullNameForUserId($player3Id)?>" <? if(!$isPageBeingLoadedForPastReservation){?>disabled <? } ?> onchange="unsetPlayer(id3);"/>
	              <?err($errors->name3)?>
	            <input id="id3" name="player3" type="hidden" value="<?=$player3Id?>"/>
		    		<script>
		                <?
		                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
		                 pat_autocomplete( array(
								'baseUrl'=> "$wwwroot/users/ajaxServer.php",
								'source'=>'name3',
								'target'=>'id3',
								'className'=>'autocomplete',
								'parameters'=> "action=autocomplete&name={name3}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
								'progressStyle'=>'throbbing',
								'minimumCharacters'=>3,
								));
		           
		                 ?>
	                </script> 
                
                </td>
                 <td>
                 
                <input id="name4" name="name4" type="text" size="30" class="form-autocomplete" value="<?=getFullNameForUserId($player4Id)?>" <? if(!$isPageBeingLoadedForPastReservation){?>disabled <? } ?> onchange="unsetPlayer(id4);"/>
	            <?err($errors->name4)?>
	            <input id="id4" name="player4" type="hidden" value="<?=$player4Id?>"/>
		    		<script>
		                <?
		                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
		                 pat_autocomplete( array(
								'baseUrl'=> "$wwwroot/users/ajaxServer.php",
								'source'=>'name4',
								'target'=>'id4',
								'className'=>'autocomplete',
								'parameters'=> "action=autocomplete&name={name4}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
								'progressStyle'=>'throbbing',
								'minimumCharacters'=>3,
								));
		           
		                 ?>
	                </script> 
                 
                </td>
                </tr>
                
                <? if(!$isPageBeingLoadedForPastReservation){?>
                <tr>
                	<td class="italitcsm" colspan="2">To Remove someone from the reservation, just delete their name<br><br></td>
                </tr>
                <? }?>
                
                <? 
                //Only display the note if there are email addresses to send to.
                if(count($emailArray)>0){?>
                <tr>
                	<td colspan="2" class="normal"><a href="mailto:<?=$emailString?>">Send Note</a></td>
                </tr>
                <? } ?>
       <?


        }

     ?>

   
      <tr>

       <td>
	       <br>
	       <input type="submit" name="submit" value="Submit">
	       <input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'">
	       <input type="hidden" name="reservationid" value="<?=$courtTypeArray['reservationid']?>">
	       <input type="hidden" name="usertype" value="<?=$courtTypeArray['usertype']?>">
	       <input type="hidden" name="courtid" value="<?=$courtid?>">
	       <input type="hidden" name="time" value="<?=$time?>">
       </td>
      </tr>


     </table>
     </form>
     </td>
</tr>


</table>



</td>
</tr>
</table>
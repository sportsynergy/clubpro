<?

/*
 * 
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 * 
 * 
 * The following variables are required before loading this form:
 * 
 * 		$userid
 * 		$time
 * 		$courtid
 * 		$reservationid
 * 
 * 
*/
      //Program administrators have the option of rearranging a doubles reservation
      //after it has already occured.  This variable is used for enabling/disabling
      //functions to support this feature.
      $isPageBeingLoadedForPastReservation = isInPast($time);
      
      
      //Get the players from the reservation (doubles will be teams, singles will be players)
       $playersQuery = "SELECT reservationdetails.userid, reservationdetails.usertype, users.firstname, users.lastname, reservations.reservationid, reservations.locked
                        FROM tblReservations reservations, tblkpUserReservations reservationdetails, tblUsers users
                        WHERE reservations.reservationid = reservationdetails.reservationid
                        AND reservations.time=$time
						AND reservations.courtid=$courtid
						AND reservations.enddate IS NULL
						AND users.userid = reservationdetails.userid
						ORDER BY reservationdetails.usertype DESC, reservationdetails.userid DESC";
		
		
       	$playersResult = db_query($playersQuery);
		$playerRow = mysql_fetch_array($playersResult);
		$locked = $playerRow['locked'];
		
	   	$player1Id = $playerRow['userid'];
	    $player1FullName = "$playerRow[firstname] $playerRow[lastname]";
	    $reservationid = $playerRow['reservationid'];
	     
	   
		$playerRow = mysql_fetch_array($playersResult);
		$player2Id = $playerRow['userid'];
		
		$player2FullName = "";
		if(! empty($player2Id)) {
			$player2FullName = "$playerRow[firstname] $playerRow[lastname]";
		}
		
	     
		$emailArray = getEmailAddressesForReservation($reservationid);
		$emailString = implode(",", $emailArray);
      
?>

<script language="Javascript">


document.onkeypress = function (aEvent)
{
 if(!aEvent) aEvent=window.event;
	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
 if( key == 13 ) // enter key
 {
     return false; // this will prevent bubbling ( sending it to children ) the event!
 }
	
}


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




function disable(disableIt)
{
        document.entryform.name1.disabled = disableIt;
        document.entryform.name2.disabled = disableIt;
        document.entryform.lock.disabled = disableIt;
}



function enable()
{
     document.entryform.name1.disabled = "";
     document.entryform.name2.disabled = "";
     document.entryform.lock.disabled = "";
}

	

</script>


<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php" onSubmit="SubDisable(this);" autocomplete="off">
	


<table cellspacing="0" cellpadding="20" width="400" class="generictable">
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center">
    		<? if($locked=='y'){ ?>
	    	 	<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
	    	<?}?>
			<? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>
		<table>
		 <tr>
                <td colspan="2" class="normal">
                <input type="radio" name="cancelall" value="3" onclick="disable(this.checked);" <? if($isPageBeingLoadedForPastReservation){?>disabled <? }else{ ?> checked <? }?>>&nbsp;Cancel the whole reservation <br>
                <input type="radio" name="cancelall" value="4" onclick="enable();" <? if($isPageBeingLoadedForPastReservation){?>checked <? } ?>>&nbsp;Update the reservation
                </td>
                </tr>

                 <tr>
                 <td>
                 
             <input id="name1" name="name1" type="text" size="30" class="form-autocomplete" value="<?=$player1FullName?>" <? if(!$isPageBeingLoadedForPastReservation){?>disabled <? } ?>  />
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
				<input id="name2" name="name2" type="text" size="30" class="form-autocomplete" value="<?=$player2FullName?>" <? if(!$isPageBeingLoadedForPastReservation){?>disabled <? } ?> />
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
                
                 <? if( get_roleid()==2 || get_roleid() ==4){ 
                 
                 	$selected="";
                 	if($locked=='y'){
                 		$selected = "checked=checked"; 
                 	}
                 	
                 ?>
                
               
			    <tr>
			    	<td>
			    		<input type="checkbox" name="lock" <?=$selected?> <? if(!$isPageBeingLoadedForPastReservation){?>disabled <? } ?>/>
			    		<span class="normal">Lock reservation</span>
			    	</td>
			    </tr>
			    <?}?>
                
                <? 
                //Only display the note if there are email addresses to send to.
                if(count($emailArray)>0){?>
                <tr>
                <td colspan="2" class="normal"><a href="mailto:<?=$emailString?>">Send Note</a></td>
                </tr>
                <? } ?>
                
		</table>
		
		</td>
		</tr>
		<tr>
       <td>
	       <br>
	        <?
	       //if its locked and its just a player disable the submit button
	       $disabled="";
	       if( $locked=='y' && get_roleid()==1){
	       	
	       	$disabled = "disabled=disabled";
	       }
	       
	       ?>
	       <input type="submit" name="submit" value="Submit"  <?=$disabled?>>
	       <input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'">

       </td>
      </tr> 
	</table>
		
	
<input type="hidden" name="reservationid" value="<?=$reservationid?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="matchtype" value="<?=$matchtype?>">
<input type="hidden" name="guylookingformatch" value="<?=$userid?>">
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
		
</form>

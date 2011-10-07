<?
/*
 * 
 * $LastChangedRevision: 843 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-28 12:15:07 -0600 (Mon, 28 Feb 2011) $
 * 
 * 
 * The following variables are required before loading this form:
 * 
 * 		$userid
 * 		$time
 * 		$courtid
 * 		$reservation
 */


  $eventQuery = "SELECT events.eventid, events.playerlimit from tblReservations reservations, tblEvents events
									WHERE reservations.time=$time AND reservations.courtid=$courtid
									AND events.eventid = reservations.eventid";
                 
                 $eventIdResult = db_query($eventQuery);
                 $eventArray = mysql_fetch_array($eventIdResult);
                 
?>

<script>

function addMeToReservation()
{

      document.manageform.action.value = 'add';
      document.manageform.submit();
}

function removeMeFromReservation()
{

      document.manageform.action.value = 'remove';
      document.manageform.submit();
}

function enableEvent()
{
	document.entryform.events.disabled = "";   
}

function disableevent(disableIt)
{
  document.entryform.events.disabled = disableIt;
 	
}
</script>


<form name="manageform" method="post" action="<?=$ME?>">
<input type="hidden" name="action"/>
<input type="hidden" name="courtid" value="<?=$courtTypeArray['courtid']?>"/>
<input type="hidden" name="time" value="<?=$courtTypeArray['time']?>"/>
<input type="hidden" name="userid" value="<?=get_userid()?>"/>
<input type="hidden" name="reservationid" value="<?=$courtTypeArray['reservationid']?>"/>
<input type="hidden" name="cmd" value="managecourtevent"/>
</form>

<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php" onSubmit="SubDisable(this);" autocomplete="off">


<table cellspacing="0" cellpadding="20" width="400" class="generictable">
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class=whiteh1>
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>
    
		<table>
		 <? 
		 
		 $enableupdate = "";
		 
		 if(  $eventArray['playerlimit'] > 0  ){ 
		 
		 	$eventplayerResult = getCourtEventParticipants($courtTypeArray['reservationid']);
		 	$amISignedup = isCourtEventParticipant($eventplayerResult);
		 	
		 	?>
				 <?
				 if(isInPast( $courtTypeArray['time'])){
				 ?>
				 <tr>
				 	<td>
				 		<span class="label">Here is who signed up for this: </span>
				 		
				 	</td> 
				  </tr>
				 
				 <? } else { ?>
				 <tr>
				 	<td>
				 		<span class="label">Here is who is coming to this: </span>
				 		<? if($eventArray['playerlimit'] != mysql_num_rows($eventplayerResult )
				 		|| $amISignedup 
				 		) { ?>
				 		<span class="normalsm">
				 		<?if($amISignedup){ ?>
				 			<a href="javascript:removeMeFromReservation();">Take me out</a></span>
				 		<? }else{ ?>
				 			<a href="javascript:addMeToReservation();">Put me down, I will be there!</a></span>
				 		<? } ?>
				 		</span>
				 		
				 		<? } ?>
				 	</td> 
				  </tr>
						  
				 <?
				 }
				 
				 	if( mysql_num_rows($eventplayerResult) > 0 ){ 
				 		
				 		//If anyone has signed up, don't let the administrator change the event
				 		$enableupdate = "disabled";
				 		
						while($player = mysql_fetch_array($eventplayerResult)){ ?>
							<tr>
								<td style="padding: 1px"><?=$player['firstname']?> 
								<?=$player['lastname']?></td>
							</tr>
						
		
				 	<? } ?>
				 
				
				 <? }  else{ ?>
				 	
				 	<tr>
						 	<td>
						 		<span class="normal">
						 		<?=isInPast( $courtTypeArray['time'])?"There were no takers":"Noone has signed up yet "?>
						 		
						 		
						 		</span>
						 	</td> 
						  </tr>
				 	 
				 	  <?	}?>
				 	 
				 	  
				 
				 <?	}   ?>
		
		 
			
       		<? 
       		//Only display this to administrators
       		if( get_roleid()==2 || get_roleid==4){  ?>
       		<tr>
		 		<td><hr/></td>
		 	</tr> 
		 	<tr>
		       <td>
       		<?
       		
       			if(isReoccuringReservation($time, $courtid)){ ?>
       		This is a reoccuring event.  What do you want to do?<br><br>
       		<input type="radio" name="cancelall" value="3" onclick="disableevent(this.checked)" checked>&nbsp; Cancel just this occurrence <br>	
       		<input type="radio" name="cancelall" value="9" onclick="disableevent(this.checked)" >&nbsp; Cancel all occurrences <br>
       			
       		<? } else{ ?>
       		<input type="radio" name="cancelall" value="3" onclick="disableevent(this.checked)" checked> &nbsp;Cancel the event <br>	
       		<? } ?>
       	 
       	 
	   	 <input type="radio" name="cancelall" value="10" onclick="javascript:enableEvent()" disabled="<?=$enableupdate?>">&nbsp;Update the event
			<select name="events" disabled>
               

                <?
                //Get Club Players
                 $eventDrpDown = get_site_events(get_siteid());
                 
               
                 
                 while($row = mysql_fetch_row($eventDrpDown)) {

					$selected = "";
                      
				 	if($row[0] == $eventArray['eventid']){
	                    $selected = "selected";
	                 }
					 
					 echo "<option value=\"$row[0]\" $selected>$row[1]</option>\n";
                     unset($selected);
                     
                 }            
       ?>
       </select>
         </td>
       </tr>
        <tr>
       <td>
	       <br>
	       <input type="submit" name="submit" value="Submit">
	       <input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'">
	       <input type="hidden" name="reservationid" value="<?=$reservationid?>">
	       <input type="hidden" name="courtid" value="<?=$courtid?>">
	       <input type="hidden" name="time" value="<?=$time?>">
       </td>
      </tr> 
      <? } else{ ?>
       <tr style="height: 30px">
       	<td></td>
       </tr>
       <tr>
       	<td>
       		<input type="button" value="Back to Court Reservations" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'">
	   </td>
       </tr>
         
      	
     <?  }?>       
	</table>
	
	</td>
	</tr>
	
      
  
</table>	


</form>

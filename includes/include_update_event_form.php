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
?>

<script>


function enableEvent()
{
	document.entryform.events.disabled = "";   
}

function disableevent(disableIt)
{
  document.entryform.events.disabled = disableIt;
 	
}
</script>


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
		 <tr>
		 <td>

       		<? if(isReoccuringReservation($time, $courtid)){ ?>
       		This is a reoccuring event.  What do you want to do?<br><br>
       		<input type="radio" name="cancelall" value="3" onclick="disableevent(this.checked)" checked>&nbsp; Cancel just this occurrence <br>	
       		<input type="radio" name="cancelall" value="9" onclick="disableevent(this.checked)" >&nbsp; Cancel all occurrences <br>
       			
       		<? } else{ ?>
       		<input type="radio" name="cancelall" value="3" onclick="disableevent(this.checked)" checked> &nbsp;Cancel the event <br>	
       		<? } ?>
       	 
       	 
	   	 <input type="radio" name="cancelall" value="10" onclick="javascript:enableEvent()">&nbsp;Update the event
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
                
	</table>
	
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
</table>	


</form>

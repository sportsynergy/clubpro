<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */
?>

<script type="text/javascript">
		
function removeClubEvent(eventid)
{
	document.removeClubEventForm.clubeventid.value = eventid;
	document.removeClubEventForm.submit();
}

function manageClubEvent(eventid)
{
	document.manageClubEventForm.clubeventid.value = eventid;
	document.manageClubEventForm.submit();
}
		
</script>


	
<form name="removeClubEventForm" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_events.php">
   <input type="hidden" name="clubeventid" value="">
   <input type="hidden" name="cmd" value="remove">
</form>	
	
<form name="manageClubEventForm" method="post" action ="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_club_event.php">
   <input type="hidden" name="clubeventid" value="">
   <input type="hidden" name="cmd" value="manage">
</form>	
	
		
<? 
if(  mysqli_num_rows($clubEvents) == 0 ){ ?>
<div>
	There aren't any special events loaded.  Why don't you <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_club_event.php">add one now</a>?
</div>

<? } else { ?>

<div style="padding-bottom: 10px"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_club_event.php">Add another Club Event</a></div>

<table cellpadding="20" width="550" class="generictable">
<tr class="clubid<?=get_clubid()?>th">
 <th height="25"><span class="whitenorm">Name </span></th>
<th height="25"><span class="whitenorm">Date</span></th>
<th></th>
</tr>

<? 
	  $rownum = mysqli_num_rows($clubEvents);
      while ($clubEventsArray = mysqli_fetch_array($clubEvents)) {
			$rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow"; ?>
 		
 		<tr class="<?=$rc?>" >
 			<td width="55%"><?=$clubEventsArray['name']?></td>
 			<td width="30%"><?=formatDateString($clubEventsArray['eventdate'])?></td>
 			<td class="normal" align="left" style="vertical-align: top;" width="75px">
                     <a href="javascript:manageClubEvent(<?=$clubEventsArray['id']?>)">Edit</a>
                      | <a href="javascript:removeClubEvent(<?=$clubEventsArray['id'] ?>);">Delete</a>
                      
              </td>
 		</tr>
  		<? 
  			$rownum = $rownum - 1;
            }  
        ?>
</table>

<? } ?>
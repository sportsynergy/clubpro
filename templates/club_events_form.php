

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
	
<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

		
<? 
if(  mysqli_num_rows($clubEvents) == 0 ){ ?>
<div>
	There aren't any special events loaded.  Why don't you <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_club_event.php">add one now</a>?
</div>

<? } else { ?>

<div style="padding-bottom: 10px"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_club_event.php">Add another Club Event</a></div>

<table  class="table table-striped">
	<thead>
<tr>
 <th>Name</th>
<th>Date</th>
<th></th>
</tr>
</thead>
<tbody>

<? 
	  $rownum = mysqli_num_rows($clubEvents);
      while ($clubEventsArray = mysqli_fetch_array($clubEvents)) {  ?>
 		
 		<tr>
 			<td ><?=$clubEventsArray['name']?></td>
 			<td ><?=formatDateString($clubEventsArray['eventdate'])?></td>
 			<td>
                     <a href="javascript:manageClubEvent(<?=$clubEventsArray['id']?>)">Edit</a>
                      | <a href="javascript:removeClubEvent(<?=$clubEventsArray['id'] ?>);">Delete</a>
                      
              </td>
 		</tr>
  		<? 
  			$rownum = $rownum - 1;
            }  
        ?>
		</tbody>
</table>

<? } ?>
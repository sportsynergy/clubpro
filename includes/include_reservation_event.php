


<form name="event_reservation_form" method="post" action="<?=$ME?>"  autocomplete="off">
  
  <div class="mb-3">
  <label for="eventid" class="form-label">Event</label>
  <select class="form-select" aria-label="Eventid" name="eventid" id="eventid">
     <? 
               $eventDrpDown = get_site_events(get_siteid());

                 while($row = mysqli_fetch_row($eventDrpDown)) {
                  echo "<option value=\"$row[0]\">$row[1]</option>";
                 }
                 ?>
        </select>
        <? is_object($errors) ? err($errors->eventid) : ""?>
</div>

<div class="mb-3">
<label for="repeat" class="form-label">Repeat</label>
<select name="repeat" class="form-select" aria-label="Eventid" onchange="disableEventOptions(this)">
          <option value="norepeat">None</option>
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="biweekly">Bi-Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
        <? is_object($errors) ? err($errors->repeat) : ""?>
</div>

  <div class="mb-3">
        <label for="frequency" class="form-label">Frequency</label>
        <select name="frequency" disabled="true" class="form-select" aria-label="Frequency" >
            <option value="week">For a Week</option>
            <option value="month">For a Month</option>
            <option value="year">For a Year</option>
          </select>   
          
          <? is_object($errors) ? err($errors->duration) : ""?>

  </div>

  <div class="form-check">
	  <input class="form-check-input" type="checkbox" name="lock" />
	  <label for="lock" class="form-label">Lock Reservation</label>
	</div>

   <div class="mb-3">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Make Reservation</button>
    <button type="submit" class="btn btn-secondary" onclick="onEventCancelButtonClicked()">Cancel</button>
  </div> 
     
  <input type="hidden" name="courttype" value="event">
  <input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
  <input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
  <input type="hidden" name="action" value="create">
  <input type="hidden" name="matchtype" value="0">
</form>


<script language="JavaScript">


function onEventSubmitButtonClicked(){
	submitForm('event_reservation_form');
}

function onEventCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
}
</script>
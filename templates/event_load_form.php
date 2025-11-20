<script language="Javascript">

function disableEventOptions(repeat)
{      
	if(repeat.value == "norepeat"){
		document.entryform.reoccurringduration.disabled = true;
	}
	else{
		document.entryform.reoccurringduration.disabled = "";
	}   
}

function updateDurationOptions(){

	if(document.entryform.repeat.value == 'daily'){
	
		removeAllOptions(document.entryform.reoccurringduration);
		addOption(document.entryform.reoccurringduration,"Select Option", "");
		addOption(document.entryform.reoccurringduration,"----------------------------", "");
		addOption(document.entryform.reoccurringduration,"For Two Days", "twodays");
		addOption(document.entryform.reoccurringduration,"For Three Days", "threedays");
		addOption(document.entryform.reoccurringduration,"For Four Days", "fourdays");
		addOption(document.entryform.reoccurringduration,"For Five Days", "fivedays");
		addOption(document.entryform.reoccurringduration,"For Six Days", "sixdays");
		addOption(document.entryform.reoccurringduration,"For a Week", "week");
		addOption(document.entryform.reoccurringduration,"For a Month", "month");
		addOption(document.entryform.reoccurringduration,"For a Year", "year");
		
	}
	else if(document.entryform.repeat.value == 'weekly'){
		removeAllOptions(document.entryform.reoccurringduration);
		addOption(document.entryform.reoccurringduration,"Select Option", "");
		addOption(document.entryform.reoccurringduration,"----------------------------", "");
		addOption(document.entryform.reoccurringduration,"For a Month", "month");
		addOption(document.entryform.reoccurringduration,"For a Year", "year");
		
	}
	else if(document.entryform.repeat.value == 'monthly'){
		removeAllOptions(document.entryform.reoccurringduration);
		addOption(document.entryform.reoccurringduration,"Select Option", "");
		addOption(document.entryform.reoccurringduration,"----------------------------", "");
		addOption(document.entryform.reoccurringduration,"For a Year", "year");
		
	}
	else{
		removeAllOptions(document.entryform.reoccurringduration);
		addOption(document.entryform.reoccurringduration,"Select Option", "");
		addOption(document.entryform.reoccurringduration,"----------------------------", "");
		addOption(document.entryform.reoccurringduration,"For a Week", "week");
		addOption(document.entryform.reoccurringduration,"For a Month", "month");
		addOption(document.entryform.reoccurringduration,"For a Year", "year");
	}

}

function removeAllOptions(selectbox){
	var i;
	for(i=selectbox.options.length-1;i>=0;i--)
	{
	selectbox.remove(i);
	}
}

function addOption(selectbox,text,value )
{
	var optn = document.createElement("OPTION");
	optn.text = text;
	optn.value = value;
	selectbox.options.add(optn);
}

function onSubmitButtonClicked(){
	submitForm('entryform');
}

function onCancelButtonClicked(){

    <?
    $mytime = $_SESSION["current_time"];
    ?>
	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$mytime+get_tzdelta() ),gmdate("j", $mytime+get_tzdelta()),gmdate("Y", $mytime+get_tzdelta())) ?>'
 }

</script>


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$ME?>">

	 <div class="mb-3">
      <label for="eventid" class="form-label">Event:</label>
	  
      <select name="eventid" class="form-select" aria-label="Event">
		<option value="">Select Option</option>
		<option value="">----------------------------</option>

			<?  //Get Club Players
		$query = "SELECT eventid, eventname
					FROM tblEvents
					WHERE siteid = ".get_siteid()."
					ORDER BY eventname";

		// run the query on the database
		$result = db_query($query);
			while($row = mysqli_fetch_row($result)) {
				echo "<option value=\"$row[0]\">$row[1]</option>";
			} ?>
		</select>
		<? is_object($errors) ? err($errors->eventid) : ""?>
    </div>

	<div class="mb-3">
      <label for="starttime" class="form-label">First Reservation:</label>
      <select name="starttime" class="form-select" aria-label="First Reservation">
			<option value="">Select Option</option>
			<option value="">----------------------------</option>
			<?  for($i=0; $i<count($reservationWindowArray); ++$i){ ?>
				<option value="<?=$reservationWindowArray[$i]?>"><?=gmdate("g:i",$reservationWindowArray[$i])?></option>
			<? } ?>	
		</select>
		<? is_object($errors) ? err($errors->starttime) : ""?>
    </div>

	<div class="mb-3">
      <label for="endtime" class="form-label">Last Reservation:</label>
      <select name="endtime" class="form-select" aria-label="Last Reservation">
			<option value="">Select Option</option>
			<option value="">----------------------------</option>
			<? 
			for($i=0; $i<count($reservationWindowArray); ++$i){ ?>
				<option value="<?=$reservationWindowArray[$i]?>"><?=gmdate("g:i",$reservationWindowArray[$i])?></option>	
			<? } ?>
		</select>
		<? is_object($errors) ? err($errors->endtime) : ""?>
    </div>

	<div class="mb-3">
      <label for="repeat" class="form-label">Repeat</label>
      <select name="repeat" onchange="updateDurationOptions();disableEventOptions(this);" class="form-select" aria-label="Repeat">
     
                <option value="norepeat">None</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="biweekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
                </select>
				<? is_object($errors) ? err($errors->repeat) : ""?>
    </div>

	<div class="mb-3">
      <label for="reoccurringduration" class="form-label">Duration </label>
      <select name="reoccurringduration" class="form-select" aria-label="Duration" disabled>
                <option value="">Select Option</option>
                <option value="">----------------------------</option>
                <option value="week">For a Week</option>
                <option value="month">For a Month</option>
                <option value="year">For a Year</option>
                </select>	
			<? is_object($errors) ? err($errors->reoccurringduration) : ""?>	
    </div>
	
     
<div class="form-check">
  <input class="form-check-input" type="checkbox" name="cancelconflicts" id="cancelconflicts">
  <label class="form-check-label" for="cancelconflicts">
    Remove any existing reservations. </label>
   <div id="reservationHelp" class="form-text"> By leaving this checkbox unchecked any reservations that were already out there will be left alone.</div>
     
</div>

<div class="form-check">
  <input class="form-check-input" type="checkbox" name="lock" id="lock">
  <label class="form-check-label" for="lock">Lock reservation
  </label>
</div>

<div class="mt-5">
    <button type="submit" class="btn btn-primary" >Load Events</button>
	<button type="submit" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
  </div>
       		
<?

// The court reservation span is how long a resevation is made for, this is a 
// fixed period.  Here the duration is derived by taking the difference
// between the first and the 

$courtduration = "";
if(count($reservationWindowArray)>1){
	$courtduration = $reservationWindowArray[1]-$reservationWindowArray[0];
}
?>

<input type="hidden" name="courtduration" value="<?=$courtduration?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="submitme" value="submitme">

</form>


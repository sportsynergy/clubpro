
<?
	$divisionchecked = $frm["registerdivision"]=='y' ? 'checked' : '';
	$teamchecked = $frm["registerteam"]=='y' ? 'checked' : '';

	//don't allow updates for registration variables
	$disabled = $frm["id"] ? 'disabled' : '';
?>


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>



<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


 <div class="mb-3">
    <label for="name" class="form-label">Event Name:</label>
	<input type="text" name="name" id="name" size="35" value="<?=$frm["name"] ?>" maxlength="30" class="form-control" aria-label="Username"> 
    <? is_object($errors) ? err($errors->subject) : ""?>
  </div>

  <div class="mb-3">
	<label for="eventdate" class="form-label">Event Date:</label>
	<input type="text" name="eventdate" id="eventdate" value="<?=convertToDateSlashes($frm["eventdate"])?>" class="form-control" aria-label="Event Date" style="width:50%; display: inline" readonly>
	<img id="calico" src="<?=$_SESSION["CFG"]["imagedir"]?>/cal.png" alt="Open the Calendar control">
	<div id="mycal" style="position:absolute;z-index:10;"></div>
	<? is_object($errors) ? err($errors->eventdate) : ""?>
  </div>

  <div class="mb-3">
	<div class="form-check">
	  <input class="form-check-input" type="checkbox" name="registerteam"  id="registerteam"  <?=$teamchecked?> <?=$disabled?>  onclick="handleClick(this);"/>
	  <label for="lock" class="form-label">Register as Team</label>
	  <div class="form-text">People registering for this event will have to register with a partner.</div>
	</div>
</div>

<div class="mb-3">
	<div class="form-check">
	  <input class="form-check-input" type="checkbox" name="registerdivision" id="registerdivision" <?=$divisionchecked?> <?=$disabled?> />
	  <label for="lock" class="form-label">Register Division</label>
	  <div class="form-text">People registering for this event will have the option to register for a division.</div>
	</div>
</div>

<div class="mb-3">
<label for="description" class="form-label">Description</label>
	<div>
		<textarea rows="5" cols="50" name="description" id="description" class="form-control" aria-label="Description"><?=$frm["description"] ?></textarea>
		 <? is_object($errors) ? err($errors->description) : ""?>		
</div>
  </div>

  <div class="mb-3">
			<? if(isset($clubEventArray["id"])){ ?>
					<button type="submit" name="submit" id="submitbutton" class="btn btn-primary" onclick="onSubmitButtonClicked()">Update Club Event</button>
				<? } else {?>
					<button type="submit" name="submit" id="submitbutton" class="btn btn-primary" onclick="onSubmitButtonClicked()">Add Club Event</button>
				<? } ?>
		
				<button type="button" name="cancel" id="cancelbutton" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
				<input type="hidden" name="submitme" value="submitme" >
				<input type="hidden" name="id" value="<?=$frm["id"]?>" >
	</div>
	
</form>

<script type="text/javascript">

//create the namespace object for this example
YAHOO.namespace("yuibook.calendar");
//define the lauchCal function which creates the calendar
YAHOO.yuibook.calendar.launchCal = function() {
	//create the calendar object, specifying the container
	var myCal = new YAHOO.widget.Calendar("mycal");
	//draw the calendar on screen
	myCal.render();
	//hide it again straight away
	myCal.hide();
	
	//define the showCal function which shows the calendar
	var showCal = function() {
	//show the calendar
	myCal.show();
	}
	//attach listener for click event on calendar icon
	YAHOO.util.Event.addListener("calico", "click", showCal);

	//define the ripDate function which gets the selected date
	var ripDate = function(type, args) {
		//get the date components
		var dates = args[0];
		var date = dates[0];
		var theYear = date[0];
		var theMonth = date[1];
		var theDay = date[2];
		var theDate = theMonth + "/" + theDay + "/" + theYear;

		//get a reference to the text field
		var field = YAHOO.util.Dom.get("eventdate");
		//insert the formatted date into the text field
		field.value = theDate;
		//hide the calendar once more
		myCal.hide();
				
	}
	//subscribe to the select event on Calendar cells
	myCal.selectEvent.subscribe(ripDate);
	
}

//create calendar on page load
YAHOO.util.Event.onDOMReady(YAHOO.yuibook.calendar.launchCal);


function onSubmitButtonClicked(){
	submitForm('entryform');
}

 function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_events.php';
 }
 

</script>
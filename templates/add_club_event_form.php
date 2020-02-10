
<?
	$divisionchecked = $frm["registerdivision"]=='y' ? 'checked' : '';
	$teamchecked = $frm["registerteam"]=='y' ? 'checked' : '';

	//don't allow updates for registration variables
	$disabled = $frm["id"] ? 'disabled' : '';
?>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

<table cellspacing="0" cellpadding="20" width="600" class="generictable" id="formtable">
 <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center"><?=$DOC_TITLE?></div>
    	</span>
    </td>
 </tr>

	<tr>
		<td>


		<table width="550" >
			<tr>
				<td class="label">Name: <td>
					<input type="text" name="name" size="35" value="<?=$frm["name"] ?>" maxlength="30"> <?err($errors->subject)?>
				</td>
			</tr>
			<tr>
				<td class="label">Date:<td>
					<input type="text" name="eventdate" id="eventdate" value="<?=convertToDateSlashes($frm["eventdate"])?>">
					<img id="calico" src="<?=$_SESSION["CFG"]["imagedir"]?>/cal.png" alt="Open the Calendar control"><?err($errors->eventdate)?>
					<div id="mycal" style="position:absolute;z-index:10;"></div>
				</td>
			</tr>
			<tr>
				<td class="label"><span title="People registering for this event will have to register with a partner.">Register as Team:</span></td>
				<td>
					<input type="checkbox" id="registerteam" name="registerteam" <?=$teamchecked?> <?=$disabled?>  onclick="handleClick(this);" > 
				</td>
			</tr>
			<tr>
				<td class="label"><span title="People registering for this event will have the option to register for a division">Register Division</span></td>
				<td>
					<input type="checkbox" id="registerdivision" name="registerdivision" <?=$divisionchecked?> <?=$disabled?> > 
				</td>
			</tr>
			<tr>
				<td class="label">
					Description:
				<td>
					<textarea rows="5" cols="50" name="description" ><?=$frm["description"] ?></textarea> <?err($errors->description)?>
				</td>
			</tr>
			
			<tr>
				<td> </td>
				<td>
				<? if(isset($clubEventArray["id"])){ ?>
					<input type="button" name="submit" value="Update Club Event" id="submitbutton">
				<? } else {?>
					<input type="button" name="submit" value="Add Club Event" id="submitbutton">
				<? } ?>
					<input type="button" value="Cancel" id="cancelbutton">
					<input type="hidden" name="submitme" value="submitme" >
					<input type="hidden" name="id" value="<?=$frm["id"]?>" >
				</td>
			</tr>
		</table>
		
		</td>
	
	</tr>

	</table>
	
	
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


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbutton1value" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbutton1value" });   
        oCancelButton.on("click", onCancelButtonClicked);
	});
	
	//diable add division if team is not checked
	//document.getElementById("registerdivision").disabled = true;
	

} ();





function onSubmitButtonClicked(){
	submitForm('entryform');
}

 function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_events.php';
 }
 

</script>
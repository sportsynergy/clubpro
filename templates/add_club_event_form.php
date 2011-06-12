<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/


?>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">



<table cellspacing="0" cellpadding="20" width="600" class="generictable">
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
					<input type="submit" name="submit" value="Update Club Event">
				<? } else {?>
					<input type="submit" name="submit" value="Add Club Event">
				<? } ?>
					<input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_events.php'">
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

</script>




<div class="mb-5">
<p class="bigbanner">
	<? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php" onSubmit="SubDisable(this);" autocomplete="off">


<div class="mb-3" style="width: 70%">

<label for="partnername" class="form-label">Partner Name</label>
 <input id="partnername" name="partnername" type="text" size="30" class="form-autocomplete form-control" />
             <input id="partnerid" name="partnerid" type="hidden"  />
    			<script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'partnername',
						'target'=>'partnerid',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={partnername}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
                 ?>
                </script>
        <div id="teamsignupHelp" class="form-text">To search for a name type in the player's first or last name.  To just put your name in and look for a partner, leave this box empty and click 'Make Reservation'.  If you are looking for one or more players you will be 
prompted for how you would like to advertise this match on the next screen.</div>
</div>




<div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()" >Make Reservation</button>
	  <button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
</div>



<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="courttype" value="doubles">
<input type="hidden" name="userid" value="<?=$userid?>">
<input type="hidden" name="action" value="addteam">
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
<input type="hidden" name="creator" value="<?=$creator?>">


</form>


<script language="Javascript">

document.getElementById('partnername').focus();
document.getElementById('partnername').setAttribute("autocomplete", "off");

document.onkeypress = function(aEvent)
{
    
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
}

//please keep these lines on when you copy the source
//made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
if (document.getElementById) {
for (var sch = 0; sch < dform.length; sch++) {
 if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
}
}
return true;
}

function onSubmitButtonClicked(){
	submitForm('entryform');
}

 function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }
 
</script>

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
	<input type="text" name="eventdate" id="eventdate" value="<?=convertToDateSlashes($frm["eventdate"])?>" class="form-control">
	<img id="calico" src="<?=$_SESSION["CFG"]["imagedir"]?>/cal.png" alt="Open the Calendar control">
	 <? is_object($errors) ? err($errors->eventdate) : ""?>
	<div id="mycal" style="position:absolute;z-index:10;"></div>
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
		
					<input type="hidden" name="submitme" value="submitme" >
					<input type="hidden" name="id" value="<?=$frm["id"]?>" >
	</div>
	
</form>

<script type="text/javascript">


function onSubmitButtonClicked(){
	submitForm('entryform');
}

 function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_events.php';
 }
 

</script>
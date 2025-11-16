
<script language="Javascript">

// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

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



<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">


<div class="mb-3">
Are you sure you want to cancel this reservation?
</div>

  <div class="mt-5">
    <button type="submit" class="btn btn-primary" id="submitbutton" onclick="onSubmitButtonClicked()">Yes</button>
    <button type="button" class="btn btn-secondary" id="cancelbutton" onclick="onCancelButtonClicked()">No</button>
  </div>
           
<input type="hidden" name="cancelall" value="3">
<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
</form>



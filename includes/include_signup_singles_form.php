
<script type="text/javascript">



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

<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php" onSubmit="SubDisable(this);" autocomplete="off">
  
<div class="">

Do you want to sign up for this court?

</div>

  <div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Yes</button>
    <button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">No, go back</button>
         <?
		       //if its locked and its just a player disable the submit button
		       $disabled="";
		       if( $locked=='y' && get_roleid()==1){
		       	
		       	$disabled = "disabled=disabled";
		       }
		       
		       ?>
    </div>         
  
  <input type="hidden" name="time" value="<?=$time?>">
  <input type="hidden" name="courtid" value="<?=$courtid?>">
  <input type="hidden" name="guylookingformatch" value="<?=$userid?>">
  <input type="hidden" name="courttype" value="singles">
  <input type="hidden" name="matchtype" value="<?=$matchtype?>">
  <input type="hidden" name="action" value="addpartner">
  <input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
</form>

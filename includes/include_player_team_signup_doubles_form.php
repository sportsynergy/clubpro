<?php


//reservation_doubles_wanted_form.php
$DOC_TITLE = "Doubles Court Reservation";

// Get the first and last name of the player who needs a partner
$getfirstandlastquery = "SELECT tblUsers.firstname, tblUsers.lastname
 	                     FROM tblUsers
 	                     WHERE tblUsers.userid=$userid";
$getfirstandlastresult = db_query($getfirstandlastquery);
$getfirstandlastobj = db_fetch_object($getfirstandlastresult);
?>

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

function disablePartnerList(disableIt)
{
        document.entryform.partnername.disabled = disableIt;
        document.entryform.partnername.disabled = disableIt;
}

function enablePartnerList()
{
        document.entryform.partnername.disabled = "";
        document.entryform.partnername.disabled = "";
}

document.onkeypress = function (aEvent)
{
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
  	
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


<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">


<div class="mb-3">

<div class="form-check">
  <input class="form-check-input" type="radio"  id="playwith" name="playwith" value="1" checked="checked" onclick="disablePartnerList(this.checked)">
   		Play with <?print "$getfirstandlastobj->firstname $getfirstandlastobj->lastname"?>?
</div>


<div class="form-check">
  <input class="form-check-input" type="radio"  id="playwith" name="playwith" value="2"  onclick="enablePartnerList()">
   		Play with Partner:

    <input id="name1" name="partnername" type="text" size="30" class="form-autocomplete form-control" disabled="disabled" style="width: 70%"/>
		          <input id="id1" name="partner" type="hidden" />
		    			<script>
		                <?
		                $wwwroot = $_SESSION["CFG"]["wwwroot"];
		                 pat_autocomplete( array(
								'baseUrl'=> "$wwwroot/users/ajaxServer.php",
								'source'=>'name1',
								'target'=>'id1',
								'className'=>'autocomplete',
								'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
								'progressStyle'=>'throbbing',
								'minimumCharacters'=>3,
								));
		                 ?>
		                </script>

			<?
	       //if its locked and its just a player disable the submit button
	       $disabled="";
	       if( $locked=='y' && get_roleid()==1){
	       	
	       	$disabled = "disabled=disabled";
	       } ?>

		</div>
</div>

 <div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Make Reservation</button>
	<button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
  </div>

<input type="hidden" name="submitme" value="submitme"/>
<input type="hidden" name="time" value="<?=$time?>"/>
<input type="hidden" name="courtid" value="<?=$courtid?>"/>
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
<input type="hidden" name="courttype" value="doubles"/>
<input type="hidden" name="userid" value="<?=$userid?>">
<input type="hidden" name="matchtype" value="<?=$matchtype?>">
<input type="hidden" name="action" value="addplayerorteam">
<input type="hidden" name="creator" value="addplayerorteam">
<input type="hidden" name="creator" value="<?=$creator?>">
        	
        	
</form>
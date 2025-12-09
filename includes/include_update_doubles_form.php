<?php


$isPageBeingLoadedForPastReservation = isInPast($time);


//Get the players from the reservation (doubles will be teams, singles will be players)
$teamQuery = "SELECT reservationdetails.userid, reservationdetails.usertype, reservations.reservationid, reservations.locked
                        FROM tblReservations reservations, tblkpUserReservations reservationdetails
                        WHERE reservations.reservationid = reservationdetails.reservationid
                        AND reservations.time=$time
						AND reservations.courtid=$courtid
						AND reservations.enddate IS NULL
						ORDER BY reservationdetails.usertype DESC, reservationdetails.userid DESC";
$teamResult = db_query($teamQuery);
$teamRow = mysqli_fetch_array($teamResult);
$locked = $teamRow['locked'];

if ($teamRow['usertype'] == 1) {
    $teamPlayerResult = getUserIdsForTeamId($teamRow['userid']);
    $teamPlayerRow = mysqli_fetch_array($teamPlayerResult);
    $player1Id = $teamPlayerRow['userid'];
    $player1FullName = "$teamPlayerRow[firstname] $teamPlayerRow[lastname]";
	$player1FullName =  htmlspecialchars($player1FullName);

    $teamPlayerRow = mysqli_fetch_array($teamPlayerResult);
    $player2Id = $teamPlayerRow['userid'];
    $player2FullName = "$teamPlayerRow[firstname] $teamPlayerRow[lastname]";
	$player2FullName =  htmlspecialchars($player2FullName);
	
    $reservationid = $teamRow['reservationid'];
} else {
    $player1Id = $teamRow['userid'];
    $player1FullName = getFullNameForUserId($teamRow['userid']);
	$player1FullName =  htmlspecialchars($player1FullName);
	
    $player2Id = "";
    $player2FullName = "";
    $reservationid = $teamRow['reservationid'];
}
$teamRow = mysqli_fetch_array($teamResult);
$emailArray = getEmailAddressesForReservation($reservationid);

if ($teamRow['usertype'] == 1) {
    $teamPlayerResult = getUserIdsForTeamId($teamRow['userid']);
    $teamPlayerRow = mysqli_fetch_array($teamPlayerResult);
    $player3Id = $teamPlayerRow['userid'];
    $player3FullName = "$teamPlayerRow[firstname] $teamPlayerRow[lastname]";
	$player3FullName =  htmlspecialchars($player3FullName);

    $teamPlayerRow = mysqli_fetch_array($teamPlayerResult);
    $player4Id = $teamPlayerRow['userid'];
    $player4FullName = "$teamPlayerRow[firstname] $teamPlayerRow[lastname]";
	$player4FullName = htmlspecialchars($player4FullName);
} else {
    $player3Id = $teamRow['userid'];
    $player3FullName = getFullNameForUserId($teamRow['userid']);
	$player3FullName =  htmlspecialchars($player3FullName);
    $player4Id = "";
    $player4FullName = "";
}
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

function enabledoubles()
{
     document.entryform.name1.disabled = "";
     document.entryform.name2.disabled = "";
     document.entryform.name3.disabled = "";
     document.entryform.name4.disabled = "";
     document.entryform.lock.disabled = "";
}

function disabledoubles(disableIt)
{
        document.entryform.name1.disabled = true;
        document.entryform.name2.disabled = true;
        document.entryform.name3.disabled = true;
        document.entryform.lock.disabled = true;

}

function unsetPlayer(id)
{
    id.value = "";
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

	document.entryform.cancelall.value=8;
	submitForm('entryform');
}

function onCancelReservationButtonClicked(){

	document.entryform.cancelall.value=3;
	submitForm('entryform');
}

 function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }

</script>


<div class="mb-5">
<p class="bigbanner">
	<? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php" onSubmit="SubDisable(this);" autocomplete="off">
	
<div class="mb-3"  >
	<label for="username" class="form-label">Team 1:</label>
	<input id="name1" name="name1" type="text" size="35" class="form-control form-autocomplete" value="<?=$player1FullName?>"  style="width: 30%; display: inline;"/>
	<input id="id1" name="player1" type="hidden" value="<?=$player1Id?>"/>
	<script>
		<?
			$wwwroot = $_SESSION["CFG"]["wwwroot"];
			pat_autocomplete( array(
				'baseUrl'=> "$wwwroot/users/ajaxServer.php",
				'source'=>'name1',
				'target'=>'id1',
				'className'=>'autocomplete',
				'parameters'=> "action=autocomplete&name={name1}&userid=0&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
				'progressStyle'=>'throbbing',
				'minimumCharacters'=>3,
				));
	
			?>
	</script>

	<input id="name2" name="name2" type="text" size="35" class="form-control form-autocomplete" value="<?=$player2FullName?>" style="width: 30%; display: inline;"/>
	<input id="id2" name="player2" type="hidden" value="<?=$player2Id?>"/>
		<script>
			<?
				$wwwroot = $_SESSION["CFG"]["wwwroot"];
				pat_autocomplete( array(
					'baseUrl'=> "$wwwroot/users/ajaxServer.php",
					'source'=>'name2',
					'target'=>'id2',
					'className'=>'autocomplete',
					'parameters'=> "action=autocomplete&name={name2}&userid=0&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
					'progressStyle'=>'throbbing',
					'minimumCharacters'=>3,
					));
		
				?>
		</script>
</div>





<div class="mb-3">
	<label for="username" class="form-label">Team 2:</label>
	<input id="name3" name="name3" type="text" size="35" class="form-control form-autocomplete" value="<?=$player3FullName?>" style="width: 30%; display: inline;" />
	<input id="id3" name="player3" type="hidden" value="<?=$player3Id?>"/>
	<script>
		<?
			$wwwroot = $_SESSION["CFG"]["wwwroot"];
			pat_autocomplete( array(
				'baseUrl'=> "$wwwroot/users/ajaxServer.php",
				'source'=>'name3',
				'target'=>'id3',
				'className'=>'autocomplete',
				'parameters'=> "action=autocomplete&name={name3}&userid=0&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
				'progressStyle'=>'throbbing',
				'minimumCharacters'=>3,
				));
	
			?>
	</script> 
	<input id="name4" name="name4" type="text" size="35" class="form-control form-autocomplete" value="<?=$player4FullName?>" style="width: 30%; display: inline;" />
	<input id="id4" name="player4" type="hidden" value="<?=$player4Id?>" />
		<script>
			<?
				$wwwroot = $_SESSION["CFG"]["wwwroot"];
				pat_autocomplete( array(
					'baseUrl'=> "$wwwroot/users/ajaxServer.php",
					'source'=>'name4',
					'target'=>'id4',
					'className'=>'autocomplete',
					'parameters'=> "action=autocomplete&name={name4}&userid=0&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
					'progressStyle'=>'throbbing',
					'minimumCharacters'=>3,
					));
		
				?>
		</script> 
</div>



<div class="mb-3">

 	<? if( get_roleid()==2 || get_roleid() ==4){ 
                 
		$selected="";
		if($locked=='y'){
			$selected = "checked=checked"; 
		}
	}
		?>
	<div class="form-check">
		<input class="form-check-input" name="lock" type="checkbox" id="lock" <?=$selected?>  <? if($isPageBeingLoadedForPastReservation){?>disabled <? } ?> />
		<label class="form-check-label" for="lock">
			Lock reservation
		</label>
	</div>
</div>


<div class="mb-3">
 <? if(!$isPageBeingLoadedForPastReservation){?>
    <div id="reservationHelp" class="form-text">To remove someone from the reservation, just delete their name.</div>            
  <? }?>

		<? 
	//Only display the note if there are email addresses to send to.
	if(count($emailArray)>0){ ?>
	<div class="mb-3">
	<a href="mailto:<?=$emailString?>">Send Note</a>
	</div>
	<? } ?>

		<? //if its locked and its just a player disable the submit button
	$disabled="";
	if( $isPageBeingLoadedForPastReservation || ($locked=='y' && get_roleid()==1) ){
		$disabled = "disabled=disabled";
	} ?>

	<div class="mt-5">
    	<button type="submit" id="submitbutton" class="btn btn-primary" onclick="onSubmitButtonClicked()">Update Reservation</button>
		<button type="button" id="cancelbutton" class="btn btn-secondary" onclick="onCancelReservationButtonClicked()">Cancel Reservation</button>
		<button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
	</div>


<input type="hidden" name="cancelall" value="">
<input type="hidden" name="reservationid" value="<?=$reservationid?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
<input type="hidden" name="creator" value="<?=$creator?>">
	
	
</form>

             
                 
                    
        
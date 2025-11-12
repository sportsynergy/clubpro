<?php

$eventQuery = "SELECT events.eventid, events.playerlimit, reservations.locked FROM tblReservations reservations, tblEvents events
									WHERE reservations.time=$time AND reservations.courtid=$courtid
									AND events.eventid = reservations.eventid
									AND reservations.enddate IS NULL";
$eventIdResult = db_query($eventQuery);
$eventArray = mysqli_fetch_array($eventIdResult);
?>

<script>


function addToReservation(userid)
{

      document.manageform.action.value = 'add';
      document.manageform.userid.value = userid;
      document.manageform.submit();
}

function removeFromReservation(userid)
{
	  YAHOO.clubevent.container.wait.show();		
      document.manageform.action.value = 'remove';
      document.manageform.userid.value = userid;
      document.manageform.submit();
}

function enableEvent(updateEvent)
{

	if(updateEvent){
		var events = document.getElementById('events');
		events.disabled = ""; 
	}

	var lock = document.getElementById('lock');
	lock.disabled = "";   
}

function disableevent(disableIt)
{
  
  var events = document.getElementById('events');
  events.disabled = disableIt;
  
  var lock = document.getElementById('lock');
  lock.disabled = disableIt;
 	
}

document.onkeypress = function (aEvent)
{
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
  	
};




function onSubmitButtonClicked(){
	submitForm('entryform');
}

function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }

</script>


<form name="manageform" method="post" action="<?=$MEWQ?>" autocomplete="off">
<input type="hidden" name="action"/>
<input type="hidden" name="courtid" value="<?=$courtTypeArray['courtid']?>"/>
<input type="hidden" name="time" value="<?=$courtTypeArray['time']?>"/>
<input type="hidden" name="userid" />
<input type="hidden" name="reservationid" value="<?=$courtTypeArray['reservationid']?>"/>
<input type="hidden" name="cmd" value="managecourtevent"/>
</form>

<div class="mb-5">

<p class="bigbanner">
	<? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php" onSubmit="SubDisable(this);" autocomplete="off">
    	
		 <?  $allowChangeEvent = true;
		 
		 if(  $eventArray['playerlimit'] > 0  ){ 
		 
		 	$eventplayerResult = getCourtEventParticipants($courtTypeArray['reservationid']);
		 	$amISignedup = isCourtEventParticipant($eventplayerResult); ?>
			
			
			<? if($eventArray['playerlimit'] != mysqli_num_rows($eventplayerResult ) || $amISignedup) { ?>
			
			
			<? if( get_roleid() ==2 || get_roleid() ==4) {?>
				<a data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Add Player</a>

				<div class="collapse" id="collapseExample">

						<form method="POST" action="<?=$ME?>" name="addeventplayer">
							<input id="name1" name="playeronename" type="text" size="30" class="form-control form-autocomplete" style="width:50%; display: inline" />
							<input id="id1" name="userid" type="hidden" />
							<input type="hidden" name="cmd" value="managecourtevent">
							<input type="hidden" name="action" value="add">
							<input type="hidden" name="user" value="admin">
							<input type="hidden" name="reservationid" value="<?=$courtTypeArray['reservationid']?>">
							<script>
								<?
								$wwwroot =$_SESSION["CFG"]["wwwroot"] ;
								pat_autocomplete( array(
										'baseUrl'=> "$wwwroot/users/ajaxServer.php",
										'source'=>'name1',
										'target'=>'id1',
										'className'=>'autocomplete',
										'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&siteid=".get_siteid()."&clubid=".get_clubid()."",
										'progressStyle'=>'throbbing',
										'minimumCharacters'=>3,
										));
								?>
									
									document.getElementById('name1').setAttribute("autocomplete", "off");

								function onSubmitButtonClicked(){
									submitForm('addeventplayer');
								}
							</script>
							<button type="submit" class="btn btn-primary btn-sm" onclick="onSubmitButtonClicked()">Add New User</button>	
						</form>
						</div>
			<? } else {?>
					<?if($amISignedup){ ?>
						<a href="javascript:removeFromReservation(<?=get_userid()?>);">Take me out</a></span>
					<? }else{ ?>
						<a href="javascript:addToReservation(<?=get_userid()?>);">Put me down, I will be there!</a></span>
					<? } ?>
				<? } ?>
			
			<? } ?>
				 	
				 <?  if( mysqli_num_rows($eventplayerResult) > 0 ){ 
				 		
				 		//If anyone has signed up, don't let the administrator change the event
				 		$allowChangeEvent = false;
				 		
						while($player = mysqli_fetch_array($eventplayerResult)){ ?>
							
								<?=$player['firstname']?> <?=$player['lastname']?>
								<? if( get_roleid() ==2 || get_roleid() ==4){ ?>
								
								  <a href="javascript:removeFromReservation(<?=$player['userid']?>);">
								 	<img src="<?=$_SESSION["CFG"]["imagedir"]?>/recyclebin_empty.png" >
								</a>
								<? }?>
				 	<? } ?>
				 <? } ?>	
				 <?	}  ?>

       		<? 
       		//Only display this to administrators
       		if( get_roleid()==2 || get_roleid()==4){  
       		
       			if(isReoccuringReservation($time, $courtid)){ ?>
					This is a reoccuring event.  What do you want to do?<br><br>
					<input class="form-check-input" type="radio" name="cancelall" value="3" onclick="disableevent(this.checked)" checked>&nbsp; Cancel just this occurrence <br>	
					<input class="form-check-input" type="radio" name="cancelall" value="9" onclick="disableevent(this.checked)" >&nbsp; Cancel all occurrences <br>
						
       		<? } else{ ?>
       		
				<div class="mt-5">
					<div class="form-check">
						<input class="form-check-input" value="3" onclick="disableevent(this.checked)" type="radio" name="cancelall" id="cancelall" checked>
						<label class="form-check-label" for="flexRadioDefault1">
							Cancel the event
						</label>
					</div>

					<div class="form-check">
						<input class="form-check-input" value="10"  type="radio" name="cancelall" id="cancelall10" onclick="javascript:enableEvent('<?=$allowChangeEvent?true:false ?>')">
						<label class="form-check-label" for="flexRadioDefault1">
							Update this event occurrence
						</label>
					</div>

			</div>
			
			<div class="mb-3" style="width: 50%">
			<select name="events" id="events" class="form-select" disabled>
                <?
                //Get Club Players
                 $eventDrpDown = get_site_events(get_siteid());
                 
                 while($row = mysqli_fetch_row($eventDrpDown)) {
					$selected = ""; 
				 	if($row[0] == $eventArray['eventid']){
	                    $selected = "selected";
	                 }
					 echo "<option value=\"$row[0]\" $selected>$row[1]</option>\n";
                     unset($selected);
                     
                 }   ?>
       		</select>
				</div>
		
			<? } ?>
       	 
       			<?
                	// Set set if its locked	
       				$selected="";
                 	if($eventArray['locked']=='y'){
                 		$selected = "checked=checked"; 
                 	}
                 	
                 ?>
       
				<div class="form-check">
				<input class="form-check-input" name="lock" id="lock" type="checkbox" <?=$selected?> disabled="disabled" id="lockreservation">
					<label class="form-check-label" for="lockreservation">
						Lock reservation
					</label>
				</div>

	    		
	    	  <div class="mt-5">
				<button type="submit" class="btn btn-primary" id="submitbutton" onclick="onSubmitButtonClicked()">Update Court Reservation</button>
				<button type="button" class="btn btn-secondary" id="cancelbutton" onclick="onCancelButtonClicked()">Back to Court Reservations</button>
				
				<input type="hidden" name="reservationid" value="<?=$courtTypeArray['reservationid']?>">
				<input type="hidden" name="courtid" value="<?=$courtid?>">
				<input type="hidden" name="time" value="<?=$time?>">
			</div>
						  
	
      <? } else { ?>  
      <div class="">
      		<button type="button" class="btn btn-secondary" id="cancelbutton" onclick="onCancelButtonClicked()">Back to Court Reservations</button>
	  </div>
      <? } ?>
          
</form>



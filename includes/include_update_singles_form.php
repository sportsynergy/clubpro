<?php

/**
* Class and Function List:
* Function list:
* Classes list:
*/
/*
 *
 * The following variables are required before loading this form:
 *
 * 		$userid
 * 		$time
 * 		$courtid
 * 		$reservationid
 *
 *
*/

//Program administrators have the option of rearranging a doubles reservation after it has already occured.  This variable is used for enabling/disabling functions to support this feature.

$isPageBeingLoadedForPastReservation = isInPast($time);

//Get the players from the reservation (doubles will be teams, singles will be players)

$playersQuery = "SELECT reservationdetails.userid, reservationdetails.usertype, concat(users.firstname,' ',users.lastname) AS fullname,
 reservations.locked,reservations.reservationid, reservations.matchtype
                        FROM tblReservations reservations, tblkpUserReservations reservationdetails, tblUsers users           
            WHERE reservations.reservationid = reservationdetails.reservationid
                        AND reservations.time=$time
						AND reservations.courtid=$courtid
						AND reservations.enddate IS NULL
						AND users.userid = reservationdetails.userid
						ORDER BY reservationdetails.usertype DESC, reservationdetails.userid DESC";
						
$playersResult = db_query($playersQuery);
$playerRow = mysqli_fetch_array($playersResult);
$locked = $playerRow['locked'];
$player1Id = $playerRow['userid'];
$player1FullName = "$playerRow[fullname]";
$player1FullName =  htmlspecialchars($player1FullName);
$matchtype = $playerRow['matchtype'];

$reservationid = $playerRow['reservationid'];
$playerRow = mysqli_fetch_array($playersResult);
$player2Id = $playerRow['userid'];
$player2FullName = "";

if (!empty($player2Id)) {
    $player2FullName = "$playerRow[fullname]";
	$player2FullName =  htmlspecialchars($player2FullName);
}

$emailArray = getEmailAddressesForReservation($reservationid);
$emailString = implode(",", $emailArray);

?>
<script language="Javascript">


document.onkeypress = function (aEvent)
{
 if(!aEvent) aEvent=window.event;
	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
 if( key == 13 ) // enter key
 {
     return false; // this will prevent bubbling ( sending it to children ) the event!
 }
	
}

  document.getElementById('name1').setAttribute("autocomplete", "off");
  document.getElementById('name2').setAttribute("autocomplete", "off");

       
	//Default names
	document.entryform.name1.value = "<?= addslashes($player1FullName) ?>";
	document.entryform.name2.value = "<?= addslashes($player2FullName) ?>";
		
function onSubmitButtonClicked(){
	var myButton = YAHOO.widget.Button.getButton('submitbutton'); 		
	myButton.set('disabled', true);
	
	document.entryform.cancelall.value=4;
	submitForm('entryform');
}

function onCancelReservationButtonClicked(){
	document.entryform.cancelall.value=3;
	submitForm('entryform');
}

function onCancelAllReservationButtonClicked(){
  document.entryform.cancelall.value=9;
  submitForm('entryform');
}


 function onCancelButtonClicked(){
	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }


function disable(disableIt)
{
        document.entryform.name1.disabled = disableIt;
        document.entryform.name2.disabled = disableIt;
        document.entryform.lock.disabled = disableIt;
}



function enable()
{
     document.entryform.name1.disabled = "";
     document.entryform.name2.disabled = "";
    <? if( get_roleid()==2 || get_roleid() ==4){ ?>
 	document.entryform.lock.disabled = "";
	<?}?>
}


    


      

</script>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php" autocomplete="off">
 
 <div class="mb-3">
        <input id="name1" name="name1" type="text" size="35" class="form-control form-autocomplete"  value="<?=$player1FullName?>" />
          <input id="id1" name="player1" type="hidden" value="<?=$player1Id?>"/>
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
</div>

<div class="mb-3">
<input id="name2" name="name2" type="text" size="35" class="form-control form-autocomplete" value="<?=$player2FullName?>"  />
              <input id="id2" name="player2" type="hidden"  value="<?=$player2Id?>"/>
              <script>
	                <?
	                 
	                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
	                 pat_autocomplete( array(
							'baseUrl'=> "$wwwroot/users/ajaxServer.php",
							'source'=>'name2',
							'target'=>'id2',
							'className'=>'autocomplete',
							'parameters'=> "action=autocomplete&name={name2}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
							'progressStyle'=>'throbbing',
							'minimumCharacters'=>3,
							));
	           
	                 ?>
                </script>

          <? if(!$isPageBeingLoadedForPastReservation){?>
         <div class="mb-3">
              <div id="emailHelp" class="form-text">To Remove someone from the reservation, just delete their name</div>
          </div>
          <? }?>
          <? if( get_roleid()==2 || get_roleid() ==4){ 
                 
                 	$selected="";
                 	if($locked=='y'){
                 		$selected = "checked=checked"; 
                 	}
                 	
                 ?>

<div class="mb-3">
  <label for="workphone" class="form-label">Match Type</label>
   <select name="matchtype" class="form-select">
                      
              <option value="2" <?= $matchtype=="2" ? "selected=selected" : "" ?> >Challenge</option>

              <? if( isSiteBoxLeageEnabled() && isLadderRankingScheme()){ ?>
              <option value="1" <?= $matchtype=="1" ? "selected=selected" : "" ?> >Box League</option>
              <? } ?>
              <option value="0" <?= $matchtype=="0" ? "selected=selected" : "" ?> >Practice</option>
          </select>             
</div>

<div class="form-check">
  <input class="form-check-input" type="checkbox"  id="lock" name="lock" <?=$selected?> <? if($isPageBeingLoadedForPastReservation){?>disabled <? } ?>/>
  <label class="form-check-label" for="lock">
    Lock reservation
  </label>
</div>

             
 <?}?>


    <? 
    //Only display the note if there are email addresses to send to.
    if(count($emailArray)>0){?>
    <div class="mb-3">
      <a href="mailto:<?=$emailString?>">Send Note</a>
    </div>
    <? } ?>
       
        <?
	       //if its locked and its just a player disable the submit button
	       $disabled="";
	       if( $isPageBeingLoadedForPastReservation || ($locked=='y' && get_roleid()==1)){
	       	
	       	$disabled = "disabled=disabled";
	       }
	       
	       ?>

<div class="mb-3"> 

     <button type="submit" class="btn btn-primary" name="submit" onclick="onSubmitButtonClicked()">Update Reservation</button>
     <button type="submit" class="btn btn-primary" <?=$disabled?> onclick="onCancelReservationButtonClicked()">Cancel Reservation</button>
   
      <? if(isReoccuringReservation($time, $courtid)){ ?>
         <button type="submit" class="btn btn-primary" <?=$disabled?> onclick="onCancelAllReservationButtonClicked()">Cancel All Occurrences</button>
        <? } ?>

     <button type="submit" class="btn btn-secondary" <?=$disabled?> onclick="onCancelButtonClicked()">Go Back</button>
  

    <input type="hidden" name="cancelall" value="">
  <input type="hidden" name="reservationid" value="<?=$reservationid?>">
  <input type="hidden" name="courtid" value="<?=$courtid?>">
  <input type="hidden" name="time" value="<?=$time?>">
  <input type="hidden" name="guylookingformatch" value="<?=$userid?>">
  <input type="hidden" name="lastupdated" value="<?=$lastupdated?>">

  </div>

</form>

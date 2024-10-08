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



YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {


      document.getElementById('name1').setAttribute("autocomplete", "off");
      document.getElementById('name2').setAttribute("autocomplete", "off");

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

		    var oCancelReservationButton = new YAHOO.widget.Button("cancelReservationbutton", { value: "cancelreservationbuttonvalue" });   
        oCancelReservationButton.on("click", onCancelReservationButtonClicked);

         <? if(isReoccuringReservation($time, $courtid)){ ?>
            var oCancelAllReservationButton = new YAHOO.widget.Button("cancelAllReservationbutton", { value: "cancelreservationbuttonvalue" });   
            oCancelAllReservationButton.on("click", onCancelAllReservationButtonClicked);

        <? } ?>

		//Default names
		document.entryform.name1.value = "<?= addslashes($player1FullName) ?>";
		document.entryform.name2.value = "<?= addslashes($player2FullName) ?>";
		
    });

} ();


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

<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php" autocomplete="off">
  <table cellspacing="0" cellpadding="20" width="410" class="generictable" id="formtable">
    <tr class="borderow">
      <td class=clubid<?=get_clubid()?>th><span class="whiteh1">
        <div align="center">
          <? if($locked=='y'){ ?>
          <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png">
          <?}?>
          <? pv($DOC_TITLE) ?>
        </div>
        </span></td>
    </tr>
    <tr>
      <td>

        <table>
          
          <tr>
            <td><input id="name1" name="name1" type="text" size="35" class="form-autocomplete"   />
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

                </script></td>
            <td><input id="name2" name="name2" type="text" size="35" class="form-autocomplete" value="<?=$player2FullName?>"  />
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
                </script></td>
          </tr>
          <? if(!$isPageBeingLoadedForPastReservation){?>
          <tr>
            <td class="italitcsm" colspan="2">To Remove someone from the reservation, just delete their name<br>
              <br></td>
          </tr>
          <? }?>
          <? if( get_roleid()==2 || get_roleid() ==4){ 
                 
                 	$selected="";
                 	if($locked=='y'){
                 		$selected = "checked=checked"; 
                 	}
                 	
                 ?>

          <tr>
              <td  colspan="2">

                <span class="label" style="margin-right: 20px">Match Type:</span> 
             
                  <select name="matchtype">
                      
                      <option value="2" <?= $matchtype=="2" ? "selected=selected" : "" ?> >Challenge</option>

                      <? if( isSiteBoxLeageEnabled() && isLadderRankingScheme()){ ?>
                      <option value="1" <?= $matchtype=="1" ? "selected=selected" : "" ?> >Box League</option>
                      <? } ?>
                      <option value="0" <?= $matchtype=="0" ? "selected=selected" : "" ?> >Practice</option>
                  </select>
              </td>
          </tr>
          <tr>
              <td colspan="2" style="height: 20px"></td>
          </tr>
          <tr >
            <td colspan="2" >
                <input type="checkbox" name="lock" <?=$selected?> <? if($isPageBeingLoadedForPastReservation){?>disabled <? } ?>/>
              <span class="normal">Lock reservation</span></td>
          </tr>
          <?}?>
          <? 
                //Only display the note if there are email addresses to send to.
                if(count($emailArray)>0){?>
          <tr>
            <td colspan="2" class="normal"><a href="mailto:<?=$emailString?>">Send Note</a></td>
          </tr>
          <? } ?>
        </table></td>
    </tr>
    <tr>
      <td><br>
        <?
	       //if its locked and its just a player disable the submit button
	       $disabled="";
	       if( $isPageBeingLoadedForPastReservation || ($locked=='y' && get_roleid()==1)){
	       	
	       	$disabled = "disabled=disabled";
	       }
	       
	       ?>
        <input type="button" name="submit" value="Update Reservation" id="submitbutton">
			<input type="button" value="Cancel Reservation" <?=$disabled?> id="cancelReservationbutton">
      <? if(isReoccuringReservation($time, $courtid)){ ?>
        <input type="button" value="Cancel All Occurrences" <?=$disabled?> id="cancelAllReservationbutton">
        <? } ?>
		</td>
    </tr>
  </table>

 <input type="hidden" name="cancelall" value="">
 <input type="hidden" name="reservationid" value="<?=$reservationid?>">
  <input type="hidden" name="courtid" value="<?=$courtid?>">
  <input type="hidden" name="time" value="<?=$time?>">
  <input type="hidden" name="guylookingformatch" value="<?=$userid?>">
  <input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
  
</form>

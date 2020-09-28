<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */

/**
* Class and Function List:
* Function list:
* Classes list:
*/
/*
 *
 * $LastChangedRevision: 857 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:08:03 -0500 (Mon, 14 Mar 2011) $
 *
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

//Program administrators have the option of rearranging a doubles reservation
//after it has already occured.  This variable is used for enabling/disabling

//functions to support this feature.

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

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

    	document.getElementById('name1').setAttribute("autocomplete", "off");
    	document.getElementById('name2').setAttribute("autocomplete", "off");
    	document.getElementById('name3').setAttribute("autocomplete", "off");
    	document.getElementById('name4').setAttribute("autocomplete", "off");

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

		var oCancelReservationButton = new YAHOO.widget.Button("cancelReservationbutton", { value: "cancelreservationbuttonvalue" });   
        oCancelReservationButton.on("click", onCancelReservationButtonClicked);
    });

} ();


function onSubmitButtonClicked(){

	var myButton = YAHOO.widget.Button.getButton('submitbutton'); 		
	myButton.set('disabled', true);
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

<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php" onSubmit="SubDisable(this);" autocomplete="off">
	

<table cellspacing="0" cellpadding="20" width="550" class="generictable" id="formtable">
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    
    	
    	<span class="whiteh1">
    		<div align="center">
	    		<? if($locked=='y'){ ?>
	    	 	<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
	    	<?}?>
    		<? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>
		<table>

              
                 <tr>
                 <td>
                 
                    <input id="name1" name="name1" type="text" size="35" class="form-autocomplete" value="<?=$player1FullName?>"  />
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
                </td>
                 <td>
                 
                <input id="name2" name="name2" type="text" size="35" class="form-autocomplete" value="<?=$player2FullName?>" />
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
                </td>
                </tr>


                <tr>
                 <td>
                
                <input id="name3" name="name3" type="text" size="35" class="form-autocomplete" value="<?=$player3FullName?>"  />
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
                
                </td>
                 <td>
                 
                <input id="name4" name="name4" type="text" size="35" class="form-autocomplete" value="<?=$player4FullName?>"  />
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
                 
                </td>

                 <? if( get_roleid()==2 || get_roleid() ==4){ 
                 
                 	$selected="";
                 	if($locked=='y'){
                 		$selected = "checked=checked"; 
                 	}
                 	
                 ?>
                
               
			    <tr>
			    	<td>
			    		<input type="checkbox" name="lock"  <?=$selected?> <? if($isPageBeingLoadedForPastReservation){?>disabled <? } ?>/>
			    		<span class="normal">Lock reservation</span>
			    	
			    	</td>
			    </tr>
			    <?}?>
			    
                </tr>
                
                <? if(!$isPageBeingLoadedForPastReservation){?>
                <tr>
                	<td class="italitcsm" colspan="2">To remove someone from the reservation, just delete their name.<br><br></td>
                </tr>
                <? }?>
                <? 
                //Only display the note if there are email addresses to send to.
                if(count($emailArray)>0){?>
                <tr>
                <td colspan="2" class="normal"><a href="mailto:<?=$emailString?>">Send Note</a></td>
                </tr>
                <? } ?>
                
		</table>
		
		</td>
		</tr>
		<tr>
       <td>
	       <br>
	       
	       <?
	       //if its locked and its just a player disable the submit button
	       $disabled="";
	       if( $isPageBeingLoadedForPastReservation || ($locked=='y' && get_roleid()==1) ){
	       	
	       	$disabled = "disabled=disabled";
	       }
	       
	       ?>
	      
	        
	 		<input type="button" value="Update Reservation" id="submitbutton">
		
			<a href="javascript:onCancelReservationButtonClicked()">Cancel Reservation</a> | 
			<a href="javascript:onCancelButtonClicked()">Go back</a>
       </td>
      </tr> 
	</table>
	
	<input type="hidden" name="cancelall" value="">
	
	<input type="hidden" name="reservationid" value="<?=$reservationid?>">
	<input type="hidden" name="courtid" value="<?=$courtid?>">
	<input type="hidden" name="time" value="<?=$time?>">
	<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
	<input type="hidden" name="creator" value="<?=$creator?>">
	
	
	</form>

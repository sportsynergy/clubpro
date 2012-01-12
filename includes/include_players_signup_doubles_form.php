<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
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
//reservation_doubles_wanted_form.php
$DOC_TITLE = "Doubles Court Reservation";

// Get the first and last name of the player one.  The player one
// and player two variables are set in court_reservation.php when

//determining what form to display.

$needpartnerquery = "SELECT reservationdetails.userid, reservationdetails.usertype
									                     FROM tblReservations reservations, tblkpUserReservations reservationdetails
														 WHERE reservations.reservationid = reservationdetails.reservationid
									                     AND reservations.courtid='$courtid'
									                     AND reservations.time='$time'
														 AND reservations.enddate is NULL
														ORDER BY reservationdetails.usertype, reservationdetails.userid";

// run the query on the database
$needpartnerresult = db_query($needpartnerquery);
$playerOneArray = mysql_fetch_array($needpartnerresult);
$playerTwoArray = mysql_fetch_array($needpartnerresult);
$playerOneQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE tblUsers.userid=$playerOneArray[userid]";
$playerOneResult = db_query($playerOneQuery);
$playerOneNameArray = db_fetch_array($playerOneResult);

// Get the first and last name of the player two
$playerTwoQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE tblUsers.userid=$playerTwoArray[userid]";
$playerTwoResult = db_query($playerTwoQuery);
$playerTwoNameArray = db_fetch_array($playerTwoResult);
?>

<script type="text/javascript">


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbutton1value" });   
        oCancelButton.on("click", onCancelButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}

function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }

</script>

<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">


<table cellspacing="0" cellpadding="20" width="400" class="generictable" >
  <tr>
    <td class=clubid<?=get_clubid()?>th>
    	<span class=whiteh1>
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400">
		<tr>
			<td><input type="radio" name="partner" value="<?=$playerOneArray['userid']?>"  checked="checked" ></td>
           	<td class="normal">Play with <?echo "$playerOneNameArray[firstname] $playerOneNameArray[lastname]"?></td>
       </tr>
        <tr>
        	<td><input type="radio" name="partner" value="<?=$playerTwoArray['userid']?>" ></td>
         	<td class="normal">Play with <?echo "$playerTwoNameArray[firstname] $playerTwoNameArray[lastname]"?></td>
       </tr>
	    <tr> 
		    <td colspan="2" id="formtable">
		           	<input type="button" name="submit" value="Make Reservation" id="submitbutton">
		           	<input type="button" value="Go Back" id="cancelbutton">
		          	<input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
	         		<input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
					<input type="hidden" name="courttype" value="doubles">
	               	<input type="hidden" name="action" value="addpartners">
	               	<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
		    </td>
	    </tr>
	 </table>
 </td>
 </tr>

</table>

</form>

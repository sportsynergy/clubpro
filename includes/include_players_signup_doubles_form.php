<?

  /*
 * $LastChangedRevision: 861 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-16 12:42:52 -0500 (Wed, 16 Mar 2011) $
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



<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">


<table cellspacing="0" cellpadding="20" width="400" class="generictable">
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
		    <td colspan="2">
		           	<input type="submit" name="submit" value="Submit">
		           	<input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'">
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

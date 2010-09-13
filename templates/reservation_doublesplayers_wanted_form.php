<?php
  /*
 * $LastChangedRevision:  $
 * $LastChangedBy:  $
 * $LastChangedDate:  $

*/
?>
<?
         //reservation_doubles_wanted_form.php
         $DOC_TITLE = "Doubles Court Reservation";
?>

<?
 // Get the first and last name of the player one.  The player one
 // and player two variables are set in court_reservation.php when
 //determining what form to display.
 
 $playerOneQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE (((tblUsers.userid)=$playerOneArray[userid]))";

 $playerOneResult = db_query($playerOneQuery);
 $playerOneNameArray = db_fetch_array($playerOneResult);
 
  // Get the first and last name of the player two
 $playerTwoQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE (((tblUsers.userid)=$playerTwoArray[userid]))";

 $playerTwoResult = db_query($playerTwoQuery);
 $playerTwoNameArray = db_fetch_array($playerTwoResult);


?>

<script language="Javascript">
<!--
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


//-->
</script>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">


<table cellspacing="0" cellpadding="20" width="400" >
  <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class=generictable>

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
	    <br>
	           	<input type="submit" name="submit" value="Submit">
	           	<input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'">
	          	<input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
         		<input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>"><td>
         		<input type="hidden" name="opponent" value="fromdoublespwandpwform">
               	<input type="hidden" name="courttype" value="doubles">
               	<input type="hidden" name="opponentplayer1" value="opponentplayer1">
               	<input type="hidden" name="opponentplayer2" value="opponentplayer2">
	           </td>
    </tr>



 </table>


 </td>
 </tr>

</table>
</form>



</td>
</tr>
</table>
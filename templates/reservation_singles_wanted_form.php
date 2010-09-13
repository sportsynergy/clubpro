<?php
/*
 * $LastChangedRevision: 761 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-02-01 17:48:39 -0800 (Mon, 01 Feb 2010) $

*/
?>
<?
         //reservation_singles_wanted_form.php
         $DOC_TITLE = "Singles Court Reservation";

         //Get matchtype
         $matchTypeQuery = "SELECT matchtype from tblReservations WHERE time=$time AND courtid=$courtid AND enddate IS NULL";
         $matchTypeQueryResult =  db_query($matchTypeQuery);
         $matchtype = mysql_result($matchTypeQueryResult,0);

         $lookingForMatchQuery = "SELECT tblkpUserReservations.userid
                                  FROM tblReservations
                                  INNER  JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
                                  WHERE ( ( ( tblReservations.courtid ) = $courtid ) 
								  AND ( ( tblReservations.time ) = $time ) ) 
								  AND (tblkpUserReservations.userid != 0)
								  AND tblReservations.enddate IS NULL";

         $lookingformatchResult = db_query($lookingForMatchQuery);
         $guylookingformatch = mysql_result($lookingformatchResult,0);


?>


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="20" width="400" >
  <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class=generictable>

     <table width="400">
       <tr>
        <td colspan="3">
			<input type="hidden" name="time" value="<?=$time?>">
         	<input type="hidden" name="courtid" value="<?=$courtid?>">
         	<input type="hidden" name="courttype" value="singles">
			<input type="hidden" name="matchtype" value="<?=$matchtype?>">
            <input type="hidden" name="guylookingformatch" value="<?=$guylookingformatch?>">
            <input type="hidden" name="partner" value="frompwform">
            <input type="hidden" name="opponent" value="frompwform">
         </td>
       </tr>

       <tr>
           <td class="normal">Do you want to sign up for  this court?</td>
           <td><input type="submit" name="cancel" value="Yes"></td>
            <td> <input type="button" value="No" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?=gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'"></td>
    </tr>
 </table>

</table>
</form>


</td>
</tr>
</table>
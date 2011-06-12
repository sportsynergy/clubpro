<?php
/*
 * $LastChangedRevision: 847 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-01 10:26:06 -0600 (Tue, 01 Mar 2011) $

*/
?>
<?

  //Get the skill range for players wanted advertising for the club.
  $rankdevquery = "SELECT  tblClubs.rankdev
                    FROM tblClubs
                    WHERE (((tblClubs.clubid)=".get_clubid()."))";

   // run the query on the database
   $rankdevresult = db_query($rankdevquery);
   $rankdev = mysql_result($rankdevresult, 0)

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

     <table cellspacing="10" cellpadding="0" width="450">


        <tr>
            <td><div class=normal>Advertise this reservation to players within my Box:</div></td>
            <td>
                Yes<input type="radio" name="resdetails" value="3" checked></td>
       </tr>     No<td><input type="radio" name="resdetails" value="0"></td>
        <tr>

       <tr>
              <td>
                  <input type="submit" value="Submit">
              </td>
              <td>
                  <input type="hidden" name="resid" value="<? pv($resid) ?>">
                  <input type="hidden" name="time" value="<? pv($time) ?>">
                  <input type="hidden" name="boxid" value="<? pv($boxid) ?>">
              </td>
              <td></td>
        </tr>

 </table>

</td>
</tr>
</table>

</form>



</td>
</tr>
</table>
<?php
/*
 * $LastChangedRevision: 644 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-30 11:06:44 -0800 (Tue, 30 Dec 2008) $

*/
?>
<?
         //reservation_doubles_wanted_form.php
         $DOC_TITLE = "Doubles Court Reservation";


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
<?
 // Get the first and last name of the player who needs a partner
 $getfirstandlastquery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE (((tblUsers.userid)=$userid))";

 $getfirstandlastresult = db_query($getfirstandlastquery);
 $getfirstandlastobj = db_fetch_object($getfirstandlastresult);


?>
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
        <td><? echo "<input type=\"hidden\" name=\"time\" value=\"$time\">" ?><td>
         <td><?echo "<input type=\"hidden\" name=\"courtid\" value=\"$courtid\">" ?><td>
         <td><?echo "<input type=\"hidden\" name=\"opponent\" value=\"fromdoublesplayerwform\">";
               echo "<input type=\"hidden\" name=\"courttype\" value=\"doubles\">";
               echo "<input type=\"hidden\" name=\"userid\" value=\"$userid\">";
               echo "<input type=\"hidden\" name=\"partner\" value=\"$userid\">";
               echo "<input type=\"hidden\" name=\"opponentplayer1\" value=\"opponentplayer1\">";
               echo "<input type=\"hidden\" name=\"opponentplayer2\" value=\"opponentplayer2\">";

               ?></td>
       </tr>

       <tr>
           <td class="normal">Are you sure you want to sign up to play with <?echo "$getfirstandlastobj->firstname $getfirstandlastobj->lastname"?>?</td>
           <td></td>
            <td></td>
       </tr>
       <tr>
           <td><input type="submit" name="cancel" value="Yes">
            <input type="button" value="No" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'">
           <td></td>
           <td></td>
       </tr>



 </table>
   <br>
       <br>


 </td>
 </tr>

</table>
</form>



</td>
</tr>
</table>
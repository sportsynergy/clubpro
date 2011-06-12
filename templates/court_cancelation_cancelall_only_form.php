<?php
/*
 * $LastChangedRevision: 847 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-01 10:26:06 -0600 (Tue, 01 Mar 2011) $

*/
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
    <td class="generictable">

     <table width="400">


       <tr>
        <td><? echo "<input type=\"hidden\" name=\"time\" value=\"$time\">" ?><td>
         <td><?echo "<input type=\"hidden\" name=\"courtid\" value=\"$courtid\">" ?><td>
         <td></td>
       </tr>
       <tr>
           <td><div class=normal>Are you sure you want to cancel this court?</div></td>
           <td><input type="submit" name="cancel" value="Yes"></td>
            <td>
                <input type="button" value="No" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'">
            </td>
    </tr>
    <tr>
     <td>
     <table width="350" cellspacing=2 cellpadding=2>

       <tr>
      <td></td>
       <td><input type="hidden" name="cancelall" value="3"></td>
       </tr>


     </table>

     </td>
    </tr>


 </table>

</table>
</form>


</td>
</tr>
</table>
<?php
/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/
?>

<form name="entryform" method="post" action="<?=$ME?>">

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>

<table cellspacing="0" cellpadding="20" width="400" >
  <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class=generictable>

     <table width="400">


       <tr>
        <td><? echo "<input type=\"hidden\" name=\"userid\" value=\"$userid\">" ?><td>
         <td><td>
         <td><td>
       </tr>
       <tr>
           <td class="normal">Are you sure you want to Delete this user?</td>
           <td><input type="submit" name="cancel" value="Yes"></td>
           <td><input type="button" value="No" onClick="javascript:submitFormWithAction('entryform','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php');">
           <input type="hidden" name="searchname" value="<?=$searchname?>">
           </td>
    </tr>
 </table>

</table>


</td>
</tr>
</table>

</form>

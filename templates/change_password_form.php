<?php
/*
 * $LastChangedRevision: 747 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-10-17 05:26:25 -0700 (Sat, 17 Oct 2009) $

*/
?>
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>



<table width="400" cellpadding="20" cellspacing="0">
     <tr>
         <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
    </tr>

 <tr>
    <td class="generictable">

      <form name="entryform" method="post" action="<?=$ME?>">

      <table cellspacing="5" cellpadding="0" >
      <tr>
        <td class="label">Old Password:</td>
        <td><input type="password" name="oldpassword" size=25>
                <?err($errors->oldpassword)?>
        </td>
      </tr>

      <tr>
        <td class="label">New Password:</td>
        <td><input type="password" name="newpassword" size=25>
                <?err($errors->newpassword)?>
        </td>
      </tr>
      <tr>
        <td class="label">Confirm Password:</td>
        <td><input type="password" name="newpassword2" size=25>
                <?err($errors->newpassword2)?>
        </td>
     </tr>
     <tr>
        <td></td>
        <td><input type="submit" name="submit" value="Change Password">

        </td>
     </table>
     </form>

   </td>
 </tr>
</table>



</td>
</tr>
</table>
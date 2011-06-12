<?php
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/
?>

<form name="entryform" method="post" action="<?=$ME?>">


<table width="400" cellpadding="20" cellspacing="0" class="generictable">
    <tr class="borderow">
         <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
    </tr>

 <tr>
    <td >
      
      <table cellspacing="5" cellpadding="0" class="borderless">
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
     <!-- Spacer -->
     
    
    
     

   </td>
 </tr>
</table>

</form>


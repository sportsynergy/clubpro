<?php
/*
 * $LastChangedRevision: 483 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-03-12 07:41:59 -0700 (Wed, 12 Mar 2008) $
 */
?>

<table cellspacing="1" cellpadding="0" border="0" width="710" align="center">
    <tr>
    <td>
      <table width="500" align="left">
       <tr>
       		
           <td colspan="2"><h2 style='color: #ff0000'>Update Successful</h2></td>
       </tr>
      <tr>
        <td height="10" colspan="2"><!-- Spacer --></td>
     </tr>
     <tr>
      <td class=label>User Name:</td>
       <td><?=$frm['username'] ?> </td>
     </tr>
     <tr>
        <td class=label>First Name:</td>
       <td class="normal"><?=$frm['firstname'] ?> </td>
          </tr>
          <tr>
             <td class=label>Last Name:</td>
            <td class="normal"><?=$frm['lastname'] ?> </td>
          </tr>
           <tr>
             <td class=label>Email:</td>
            <td class="normal"><?=$frm['email'] ?> </td>
          </tr>
          <tr>
             <td class=label>Home Phone:</td>
            <td class="normal"><?=$frm['homephone'] ?> </td>
          </tr>
          <tr>
             <td class=label>Work Phone:</td>
            <td class="normal"><?=$frm['workphone'] ?> </td>
          </tr>
          <? if(!empty($frm['cellphone'])){ ?>
          <tr>
             <td class=label>Cell Phone:</td>
            <td class="normal"><?=$frm['cellphone'] ?> </td>
          </tr>
          <? } ?>
          <? if(!empty($frm['pager'])){ ?>
          <tr>
          <td class=label>Pager:</td>
            <td class="normal"><?=$frm['pager'] ?> </td>
          </tr>
          <? } ?>
          <td class=label>Receive Players Wanted Notifications:</td>
            <td valign="top" class="normal"><?=$frm['recemail']=="y"?"Yes":"No" ?> </td>
          </tr>
          <tr>
             <td class=label valign="top">Address:</td>
            <td class="normal"><?=$frm['useraddress'] ?> </td>
          </tr>
         </table>

       </td>
       </tr>
</table>
<?php
/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
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
      <td class="label">User Name:</td>
       <td class="normal"><?=$frm['username'] ?> </td>
     </tr>
          <tr>
        <td class="label">Password:</td>
       <td>****************** </td>
          </tr>
     <tr>
        <td class="label">First Name:</td>
       <td class="normal"><?=$frm['firstname'] ?> </td>
          </tr>
          
          <tr>
             <td class="label">Last Name:</td>
            <td class="normal"><?=$frm['lastname'] ?> </td>
          </tr>
           <tr>
             <td class="label">Email:</td>
            <td class="normal"><?=$frm['email'] ?> </td>
          </tr>
          <tr>
             <td class="label">Home Phone:</td>
            <td class="normal"><?=$frm['homephone'] ?> </td>
          </tr>
          <tr>
             <td class="label">Work Phone:</td>
            <td class="normal"><?=$frm['workphone'] ?> </td>
          </tr>
          <? if(!empty($frm['cellphone'])){ ?>
          <tr>
             <td class="label">Cell Phone:</td>
            <td class="normal"><?=$frm['cellphone'] ?> </td>
          </tr>
          <? } ?>
          <? if(!empty($frm['pager'])){ ?>
          <tr>
          <td class="label" >Pager:</td>
            <td class="normal"><?=$frm['pager'] ?> </td>
          </tr>
          <? } ?>
          <tr>
          <td class="label" >Receive Players Wanted Notifications:</td>
            <td valign="top" class="normal"><?=$frm['recemail']=="y"?"Yes":"No" ?> </td>
          </tr>
          <tr>
             <td class="label" valign="top">Address:</td>
            <td class="normal"><?=$frm['useraddress'] ?> </td>
          </tr>
          <tr>
             <td class="label" valign="top">Membership ID:</td>
            <td class="normal"><?=$frm['memberid'] ?> </td>
          </tr>
          <tr>
             <td class="label" valign="top">Gender:</td>
            <td class="normal"><? 
            	if($frm['gender']=="1") 
            		print "Male";
            	else print "Female"; 
            ?> </td>
          </tr>
          <tr>
          <td class=label valign="top">Authorized Sites:</td>
           <td class="normal">
           <? if(mysql_num_rows($authSites)>0){ ?>
           <?

                    while($siteArray = mysql_fetch_array($authSites)){

                          print "$siteArray[sitename]<br>";

                    } ?>

           <? } /* endif for mysql_num_rows($authSites)*/?>
           </td>
          </tr>



           <tr>
               <td class=label valign="top">Rankings:</td>
               <td class="normal">
               <table width="220" cellpadding="0" cellspacing="0">
               <? if(mysql_num_rows($registeredSports)>0){ ?>
               <?

                 while($sportArray = mysql_fetch_array($registeredSports)){

                 print "<tr class=normal><td>$sportArray[courttypename]:</td><td>$sportArray[ranking]</td></tr>";

                 }?>
           <? } /* endif for mysql_num_rows($registeredSports)*/?>

                </table>
               </td>
           </tr>
           <tr>
             <td class=label>Enabled:</td>
            <td class="normal"><?=isset($frm['enable'])?"Yes":"No"?> </td>
          </tr>

           <tr>
               <td height="40"><!-- Spacer --></td>
           </tr>
           <tr>
           <td align="right" colspan="2" class="normal">
           <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">
                 <a href="javascript:submitForm('backtolistform');"><< Back to List</a>&nbsp;&nbsp;&nbsp;
                 <input type="hidden" name="searchname" value="<?=$frm['searchname']?>">
            </form>
            </td>
       </tr>
         </table>

       </td>
       </tr>

</table>
<?php
/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/


?>
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<table width="600" cellpadding="0" cellspacing="0">
    <tr>
    <td class="clubid<?=get_clubid()?>th"height="60"><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
 <tr>
  <td>
      <table cellpadding="0" cellspacing="0" class=clubid<?=get_clubid()?>th>
          <tr>
             <td align="left"width="84"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/ProfileOn.gif" border="0"></a></td>
             <td align="left" width="84"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_history.php?userid=<?pv($userid)?>&searchname=<?pv($searchname)?>"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/HistoryOff.gif" border="0"></a></td>
               <td width="100%"></td>
          </tr>
      </table>

  </td>
 </tr>

 <tr>
    <td class="generictable">

       <form name="entryform" method="post" action="<?=$ME?>">
       <table cellspacing="5" cellpadding="1" width="600" >
       <tr>
            <td colspan="2" height="15"></td>
        </tr>
 		<tr>
            <td class="label">User Name:</td>
            <td class="normal"><?=$frm["username"] ?></td>
        </tr>
       <tr>
            <td class="label">First Name:</td>
            <td class="normal"><?=$frm["firstname"] ?></td>
        </tr>
        <tr>
            <td class="label">Last Name:</td>
            <td class="normal"><?=$frm["lastname"] ?></td>
        </tr>

        <tr>
            <td class="label">Email:</td>
            <td class="normal"><?=$frm["email"] ?></td>
        </tr>

        <tr>
            <td class="label">Home Phone:</td>
            <td class="normal"><? if($frm["homephone"]==0)
                       echo "Not Specified";
                  else
                      pv($frm["homephone"]);
                  ?>
            </td>
        </tr>
        <? if(!empty($frm["workphone"])){?>
        <tr>
            <td class="label">Work Phone:</td>
            <td class="normal"><?=$frm["workphone"]?>

            </td>
        </tr>
         <? } ?>
        <? if(!empty($frm["cellphone"])){?>
        <tr>
            <td class="label">Cell Phone:</td>
            <td class="normal"><?=$frm["cellphone"]?>

            </td>
        </tr>
        <? } ?>
        <? if(!empty($frm["pager"])){?>
        <tr>
            <td class="label">Pager:</td>
            <td class="normal"><?=$frm["pager"]?>
            </td>
        </tr>
        <? } ?>
         <? if(!empty($frm["msince"])){?>
         <tr>
            <td class="label">Member Since:</td>
            <td class="normal"><?=$frm["msince"]?></td>
        </tr>
        <? } ?>

        <tr>
            <td class="label" valign="top">Address:</td>
            <td class="normal"><textarea name="useraddress" cols=35 rows=5 disabled><? pv($frm["useraddress"]) ?></textarea>

            </td>
        </tr>
         <tr>
             <td colspan="2" height="20"><!-- Spacer --></td>
         </tr>
         <tr>

          <td class="label" valign="top">Rankings:</td>
          <td>
                    <table width="300">
                    <?  while ($registeredArray = db_fetch_array($registeredSports)){ ?>
                         <tr>
                             <td class="normal"><?=$registeredArray[courttypename]?></td>
                              <td class="normal"><?=$registeredArray[ranking]?></td>
                         </tr>
                     <?  }
                    ?>
                    </table>
          </td>
         </tr>




        </table>
       </form>

    </td>
</tr>
 <td align="right" class="normal">
  <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_lookup.php">
     <a href="javascript:submitForm('backtolistform');"><< Back to List</a></td>&nbsp;&nbsp;&nbsp;
     <input type="hidden" name="searchname" value="<?=$searchname?>">
  </form>
 </td>
</table>

</td>
</tr>
</table>
<?php
/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/
?>
<p>
<table cellpadding=20>
<tr valign=top>
<td width=300 class=normal>
        <p>Enter in your email address to recover your password.  When you submit
        this request, your password will be reset, and a new password will be sent
        to you via email.


</td>

<td>
        <? if (! empty($errormsg)) { ?>
                <div class=warning align=center><? pv($errormsg) ?></div>
        <? } ?>

        <form name="entryform" method="post" action="<?=$ME?>">


        <table cellspacing="0" cellpadding="20" width="400" >


         <tr>
             <td class=loginth><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
          </tr>

          <tr>
          <td class=generictable>

        <table>
        <tr>
                <td class=label>Email Address:</td>
                <td><input type="text" name="email" size=25 value="<? pv($frm["email"]) ?>"></td>
        </tr>
        <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Submit">
                        <input type="button" value="Cancel" onClick="javascript: history.go(-1)">
                        <p class=normal>
                          <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php">Login Screen</a>

                </td>
        </table>

        </td>
        </tr>
       </table>
        </form>
</td>
</tr>
</table>
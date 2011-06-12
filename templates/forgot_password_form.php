<?php
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/
?>
<form name="entryform" method="post" action="<?=$ME?>">


<table cellpadding="20" > 
<tr valign="top">
<td width="300">
        
        <span class="normal">
        Enter in your email address to recover your password.  When you submit
        this request, your password will be reset, and a new password will be sent
        to you via email.
        </span>


</td>

<td>
        <? if (! empty($errormsg)) { ?>
                <div class=warning align=center><? pv($errormsg) ?></div>
        <? } ?>

        


        <table cellspacing="0" cellpadding="20" width="400" class="generictable">


         <tr>
             <td class="loginth">
             	<span class="whiteh1">
             		<div align="center"><? pv($DOC_TITLE) ?></div>
             	</span>
             </td>
          </tr>

          <tr>
          <td>

        <table>
        <tr>
                <td class=label>Email Address:</td>
                <td><input type="text" name="email" size=25 value="<? pv($frm["email"]) ?>"></td>
        </tr>
        <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Submit">
                        <input type="button" value="Cancel" onClick="javascript: history.go(-1)">
                </td>
        </table>

        </td>
        </tr>
       </table>
      
</td>
</tr>
</table>


  </form>
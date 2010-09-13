<?php
/*
 * $LastChangedRevision: 284 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2007-07-22 19:18:16 -0700 (Sun, 22 Jul 2007) $

*/
?>
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="20" width="400" >
  <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class=generictable>

     <table width="400">


        <tr>
            <td class=label>Sport name:</td>
            <td><input type="text" name="sportname" size=25 value="<? pv($frm["sportname"]) ?>">
                <?err($errors->sportname)?>
                </td>
       </tr>


       <tr>
           <td></td>
           <td><input type="submit" name="submit" value="Submit"></td>

    </tr>
 </table>

</table>
</form>


</td>
</tr>
</table>
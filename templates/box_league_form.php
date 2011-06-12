<?php
/*
 * $LastChangedRevision: 2 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2006-05-26 15:46:27 -0500 (Fri, 26 May 2006) $

*/
?>


<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="20" width="400" >
  <tr>
    <td class=clubid".$clubid."th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class=generictable>

     <table width="400">


          <tr>
            <td class=label>Box Description:</td>
            <td><input type="text" name="bname" size=25 value="<? pv($frm["bname"]) ?>">
                <?err($errors->bname)?>
                </td>
       </tr>


       <tr>
           <td></td>
           <td><input type="submit" value="Submit"></td>

    </tr>
 </table>

</table>
</form>

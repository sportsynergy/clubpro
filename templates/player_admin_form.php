<?php
  /*
 * $LastChangedRevision: 284 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2007-07-22 19:18:16 -0700 (Sun, 22 Jul 2007) $

*/
?>


<form name="entryform" method="post" action="<?=$ME?>">

<table cellspacing="0" cellpadding="5" width="710" align="center" border="0">
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
        <td class=label>Member Name:</td>
        <td><input type="text" name="searchname" size=25>
                <?err($errors->searchname)?>
        </td>
</tr>

       <tr>
           <td colspan="2"><p class=normal>
           Search for the first or last name of a member. *Note partial string are supported.
           <i> i.e. Smi for Smith or Pet for Peter</i>
           </p>
           </td>


       </tr>
       <tr>
           <td></td>
           <td><input type="submit" name="submit" value="Search"></td>

       </tr>
 </table>

</table>

</td>
</tr>
</table>

</form>

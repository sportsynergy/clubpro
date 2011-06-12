<?
/*
 * $LastChangedRevision: 854 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-08 20:15:00 -0600 (Tue, 08 Mar 2011) $

*/
?>

<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="20" width="400" class="generictable">
  <tr>
    <td class=clubid<?=get_clubid()?>th>
    	<span class=whiteh1>
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400">
       <tr>
           <td class="normal">Are you sure you want to Delete this user? You know, if there is any chance of this player coming back a better idea is to disable them.</td>
           
    </tr>
    <tr>
    	<td>
           		<input type="submit" name="cancel" value="Yes, I know. Delete this player">
           		<input type="button" value="No, go back" onClick="javascript:submitFormWithAction('entryform','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php');">
           </td>
    </tr>
 </table>

</table>

<input type="hidden" name="searchname" value="<?=$searchname?>">
<input type="hidden" name="userid" value="<?=$userid?>">

</form>

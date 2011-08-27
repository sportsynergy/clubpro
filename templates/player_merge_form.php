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
           <td class="normal">It appears that this same user already has an account at the <span class="bold"><?=$otherClub?></span>.  Would you like to link these two accounts? </td>
           
    </tr>
    <tr>
    	<td>
           		<input type="submit" name="cancel" value="Yes, please link these two accounts">
           		<input type="button" value="No, go back" onClick="javascript:submitForm('changeSettingsForm');">
           </td>
    </tr>
 </table>

</table>


<input type="hidden" name="userid" value="<?=$userid?>" action="<?=$ME?>">
<input type="hidden" name="other_userid" value="<?=$otherClubUser?>">
<input type="hidden" name="action" value="mergeaccounts">

</form>

<form name="changeSettingsForm" method="post" action>
	<input type="hidden" name="userid" value="<?=$userid?>">
</form>

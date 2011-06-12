<?
/*
 * $LastChangedRevision: 854 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-08 20:15:00 -0600 (Tue, 08 Mar 2011) $
 */
?>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">

     <tr>
      <td class="normal">
          The player was successfully removed.  Click <a href="javascript:submitForm('backtosearchresultsform')">here</a> to go back to the list.

         </td>
      </tr>
</table>


<form name="backtosearchresultsform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">
	<input type="hidden" name="searchname" value="<?=$searchname?>">
</form>
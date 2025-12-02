
<div class="alert alert-success" role="alert">
  The player was successfully removed.  Click <a href="javascript:submitForm('backtosearchresultsform')">here</a> to go back to Account Maintenance
</div>
 
<form name="backtosearchresultsform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">
  <input type="hidden" name="searchname" value="<?=$searchname?>">
</form>

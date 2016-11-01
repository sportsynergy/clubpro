<?php
 
/**
* This is a custom wrapper for the JONAS club to use Single Sign on. To use this include a file in the sites configuration including the siteid and clubid (i.e. /clubs/sitecode/filename.php)
*/

$userid = $_REQUEST["userid"];

if ( !isset($userid)  ){
	die("invalid request");
} 

$query = "SELECT password, sitecode FROM tblClubSites WHERE siteid = $siteid";
$result = db_query($query);
$siteArray = mysqli_fetch_array($result);

?>

<html>

<form name="mainForm" action="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=$siteArray['sitecode'] ?>/" method="post">
  <input type="hidden" name="username" value="<?=$userid ?>">
  <input type="hidden" name="password" value="<?=$siteArray['password'] ?>">
</form>

<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/forms.js" type="text/javascript"></script>
<script>

submitForm('mainForm');
</script>


</html>
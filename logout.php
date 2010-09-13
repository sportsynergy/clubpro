<?
/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/

include("./application.php");


$role = get_roleid();


/* unset the SESSION["user"] variable to log out the user */

unset($_SESSION["user"]);
unset($_SESSION["view"]);
unset($_SESSION["courtWindowStart"]);
$wwwroot = $_SESSION["CFG"]["wwwroot"];

if($role==3){
unset($_SESSION["siteprefs"]);
header("Location: $wwwroot/system/");
}
else{
$sitecode = get_sitecode();
unset($_SESSION["siteprefs"]);
header("Location: $wwwroot/clubs/$sitecode/");
}
?>
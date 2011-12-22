<?


include("./application.php");


$role = get_roleid();


/* unset the SESSION["user"] variable to log out the user */

unset($_SESSION["user"]);
unset($_SESSION["view"]);
unset($_SESSION["courtWindowStart"]);
unset($_SESSION["ladder_courttype"]);
unset($_SESSION["ladders"]);
unset($_SESSION["courtGroup"]);

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
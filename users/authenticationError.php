<?
/*
 * Created on Dec 20, 2007
 *
 */
 
include("../application.php");

$DOC_TITLE = "Error Page";
 
include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/auto_login_error_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");


?>



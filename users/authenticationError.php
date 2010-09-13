<?php
/*
 * Created on Dec 20, 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
include("../application.php");

$DOC_TITLE = "Error Page";
 
include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/auto_login_error_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");


?>



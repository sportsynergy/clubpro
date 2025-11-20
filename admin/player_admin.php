<?php

include ("../application.php");
$DOC_TITLE = "Account Maintenance";

require_loginwq();

/* form has been submitted, try to create the new role */

//Set the http variables
$searchname = $_GET["searchname"];

if (isset($searchname)) {
    $errormsg = validate_form($searchname);
    $backtopage = $_SESSION["CFG"]["wwwroot"] . "/admin/player_admin.php";
    

    if (empty($errormsg)) {
        $playerResults = get_admin_player_search($searchname);
        include ($_SESSION["CFG"]["templatedir"] . "/header.php");
        print_players($searchname, $backtopage, $playerResults, $DOC_TITLE, $ME);
        include ($_SESSION["CFG"]["templatedir"] . "/footer.php");
        die;
    }
}
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_admin_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
/**
 *
 * @param $searchname
 */
function validate_form($searchname) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    
    if (isDebugEnabled(1)) logMessage("player_admin.validate_form: Validating Player admin form: searchname $searchname " . strpos($searchname, "'"));
    $errors = new clubpro_obj;
    $msg = "";
    
    if (strpos($searchname, "'") !== false) {
        $msg.= "No speical characters please. ";
    }
    return $msg;
}
/**
 *
 * @param $searchname
 * @param $playerresult
 * @param $DOC_TITLE
 * @param $ME
 */
function print_players($searchname, $backtopage, $playerresult, $DOC_TITLE, $ME) {
     
    if (mysqli_num_rows($playerresult) < 1) {
        $errormsg = "Sorry, no results found.";
        include ($_SESSION["CFG"]["includedir"] . "/errorpage.php");
    } else {
        include ($_SESSION["CFG"]["templatedir"] . "/player_admin_form.php");
        mysqli_data_seek($playerresult, 0);
        $num_fields = mysqli_num_fields($playerresult);
        $num_rows = mysqli_num_rows($playerresult);
?>

<form name="exportDataForm" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/csvServer.php" method="post">
  <input type="hidden" name="searchname" value="<?=$searchname?>">
</form>

<div style="text-align: left; padding-bottom: 2px;display:inline-block;float: left;"> <?=$num_rows?> results</div>

<div style="text-align: right;  padding-bottom: 2px;display:inline-block;float: right;"> <a href="javascript:submitForm('exportDataForm')">Export this list</a> </div>

<table class="table table-striped">
  <thead>
  <tr>
    <th height="25"><span>First Name</span></th>
    <th height="25"><span>Last Name</span></th>
    <th height="25"><span>Email</span></th>
    <th height="25"><span>Mobile Phone</span></th>
    <th colspan="2"></th>
  </tr>
    </thead>
    <tbody>
  <?php
        $rownum = mysqli_num_rows($playerresult);
        while ($row = mysqli_fetch_array($playerresult)) {
          
?>
  <tr >
    <form name="playerform<?=$rownum?>" method="get">
      <td><div>
          <?=$row['firstname']?>
        </div></td>
      <td><div>
          <?=$row['lastname']?>
        </div></td>
      <td><div><a href="mailto:<?=$row['email']?>">
          <?=$row['email']?>
          </a></div></td>
      <td><div>
          <?=$row['cellphone']?>
        </div></td>
      <td colspan="2"><div> <a href="javascript:submitFormWithAction('playerform<?=$rownum?>','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/change_settings.php')">Edit</a> | <a href="javascript:submitFormWithAction('playerform<?=$rownum?>','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_delete.php')">Delete</a> </div></td>
      <input type="hidden" name="userid" value="<?=$row['userid']?>">
      <input type="hidden" name="searchname" value="<?=$searchname?>">
    </form>
  </tr>
  <?  $rownum = $rownum -1; } ?>
        </tbody>
</table>
<?


		
	}

}
	
?>

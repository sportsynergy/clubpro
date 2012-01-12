<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */
/**
* Class and Function List:
* Function list:
* - validate_form()
* - print_players()
* Classes list:
*/
include ("../application.php");
$DOC_TITLE = "Account Maintenance";

/* form has been submitted, try to create the new role */

//Set the http variables
$searchname = $_GET["searchname"];

if (isset($searchname)) {
    $errormsg = validate_form($searchname);
    $backtopage = $_SESSION["CFG"]["wwwroot"] . "/admin/player_admin.php";
    
    if (empty($errormsg)) {
        $playerResults = get_admin_player_search($searchname);
        include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
        print_players($searchname, $playerResults, $DOC_TITLE, $ME);
        include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
        die;
    }
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_admin_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

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
    $errors = new Object;
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
function print_players($searchname, $playerresult, $DOC_TITLE, $ME) {
    
    if (mysql_num_rows($playerresult) < 1) {
        $errormsg = "Sorry, no results found.";
        include ($_SESSION["CFG"]["includedir"] . "/errorpage.php");
    } else {
        include ($_SESSION["CFG"]["templatedir"] . "/player_admin_form.php");
        mysql_data_seek($playerresult, 0);
        $num_fields = mysql_num_fields($playerresult);
        $num_rows = mysql_num_rows($playerresult);
?>

<form name="exportDataForm" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/csvServer.php" method="post">
  <input type="hidden" name="searchname" value="<?=$searchname?>">
</form>
<div style="text-align: right; width:100%; padding-bottom: 2px"> <a href="javascript:submitForm('exportDataForm')">Export this list</a> </div>
<table cellpadding="20" width="100%" class="bordertable">
  <tr class="loginth">
    <th height="25"><span class="whitenorm">First Name</span></th>
    <th height="25"><span class="whitenorm">Last Name</span></th>
    <th height="25"><span class="whitenorm">Email</span></th>
    <th height="25"><span class="whitenorm">Work Phone</span></th>
    <th height="25"><span class="whitenorm">Home Phone</span></th>
    <th colspan="2"></th>
  </tr>
  <?php
        $rownum = mysql_num_rows($playerresult);
        while ($row = mysql_fetch_array($playerresult)) {
            $rc = (($rownum / 2 - intval($rownum / 2)) > .1) ? "lightrow" : "darkrow";
?>
  <tr class="<?=$rc?>">
    <form name="playerform<?=$rownum?>" method="get">
      <td><div align="center">
          <?=$row[firstname]?>
        </div></td>
      <td><div align="center">
          <?=$row[lastname]?>
        </div></td>
      <td><div align="center"><a href="mailto:<?=$row[email]?>">
          <?=$row[email]?>
          </a></div></td>
      <td><div align="center">
          <?=$row[workphone]?>
        </div></td>
      <td><div align="center">
          <?=$row[homephone]?>
        </div></td>
      <td colspan="2"><div align="center"> <a href="javascript:submitFormWithAction('playerform<?=$rownum?>','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/change_settings.php')">Edit</a> | <a href="javascript:submitFormWithAction('playerform<?=$rownum?>','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_delete.php')">Delete</a> </div></td>
      <input type="hidden" name="userid" value="<?=$row['userid']?>">
      <input type="hidden" name="searchname" value="<?=$searchname?>">
      <input type="hidden" name="DOC_TITLE" value="Player Administration">
    </form>
  </tr>
  <?
			$rownum = $rownum -1;
		}
?>
</table>
<?


		
	}

}
	//return $result;
?>

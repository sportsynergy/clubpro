<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
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
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
$DOC_TITLE = "Member Directory";
require_loginwq();

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

// Set the http variables
// Updated to GET method

$searchname = $_GET["searchname"];

//Check to see if the view is empty

if (isset($searchname)) {
    $errormsg = validate_form($searchname);
    $backtopage = $_SESSION["CFG"]["wwwroot"] . "/users/player_lookup.php";
    
    if (empty($errormsg)) {
        $playerResults = get_player_search($searchname);
        include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
        print_players($searchname, $playerResults, $DOC_TITLE, $ME);
        include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
        die;
    }
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_lookup_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($searchname) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    if (strpos($searchname, "'") !== false) {

        //$errors->searchname = true;
        $msg.= "No speical characters please. ";
    }
    
  
    return $msg;
}
/**
 *
 * @param unknown_type $searchname
 * @param unknown_type $playerResults
 * @param unknown_type $DOC_TITLE
 * @param unknown_type $ME
 */
function print_players($searchname, $playerResults, $DOC_TITLE, $ME) {
    
    if (mysqli_num_rows($playerResults) < 1) {
        $errormsg = "Nobody by that name here.";
        include ($_SESSION["CFG"]["includedir"] . "/errorpage.php");
    } else {
        include ($_SESSION["CFG"]["templatedir"] . "/player_lookup_form.php");
        mysqli_data_seek($playerResults, 0);
        $num_fields = mysqli_num_fields($playerResults);
        $num_rows = mysqli_num_rows($playerResults);
?>
<table cellpadding="20" width="100%" class="bordertable">
  <tr class="loginth">
    <th height="25"><span class="whitenorm">First Name</span></th>
    <th height="25"><span class="whitenorm">Last Name</span></th>
    <th height="25"><span class="whitenorm">Email</span></th>
    <th height="25"><span class="whitenorm">Mobile Phone</span></th>
    <th></th>
  </tr>
  <?php
        $rownum = mysqli_num_rows($playerResults);
        while ($playerarray = mysqli_fetch_array($playerResults)) {
            $rc = (($rownum / 2 - intval($rownum / 2)) > .1) ? "darkrow" : "lightrow";
?>
  <tr class="<?=$rc?>" >
    <form name="playerform<?=$rownum?>" method="get" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php" >
      <td><div align="center">
          <?=$playerarray[1]?>
        </div></td>
      <td><div align="center">
          <?=$playerarray[2]?>
        </div></td>
      <td><div align="center"><a href="mailto:<?=$playerarray[3]?>">
          <?=$playerarray[3]?>
          </a></div></td>
      <td><div align="center">
          <?=$playerarray[6]?>
        </div></td>
      <input type="hidden" name="userid" value="<?=$playerarray[0]?>">
      <input type="hidden" name="searchname" value="<?=$searchname?>">
      <input type="hidden" name="origin" value="lookup">
      <td><div align="center">
        <a href="javascript:submitForm('playerform<?=$rownum?>')">Info</a></td>
    </form>
  </tr>
  				<?php
            $rownum = $rownum - 1;
        }
?>
</table>
<?php
    }
}
?>

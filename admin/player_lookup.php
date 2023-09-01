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
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/
include ("../application.php");
$DOC_TITLE = "Account Maintenance";
require_loginwq();

/* form has been submitted, try to create the new role */
$searchname = $_REQUEST['searchname'];

//Check to see if the view is empty

if (isset($searchname)) {
    $errormsg = validate_form($searchname);
    $backtopage = $_SESSION["CFG"]["wwwroot"] . "/admin/player_lookup.php";
    
    if (empty($errormsg)) {
        $playerResults = get_all_player_search($searchname);
        
        if (isDebugEnabled(1)) logMessage("player_lookup: Found " . mysqli_num_rows($playerResults) . " results");
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
    
    if (empty($searchname)) {

        //$errors->searchname = true;
        $msg.= "You did not specify a name to search";
    } elseif (strpos($searchname, "'") !== false) {

        //$errors->searchname = true;
        $msg.= "No speical characters please. ";
    }
    return $msg;
}

/******************************************************************************
 * Print_players
 *****************************************************************************/
function print_players($searchname, $playerResults, $DOC_TITLE, $ME) {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    
    if (isDebugEnabled(1)) logMessage("player_lookup.print_players: searchname: $searchname");
    
    if (mysqli_num_rows($playerResults) < 1) {
        $errormsg = "Sorry, no results found.";
        include ($_SESSION["CFG"]["includedir"] . "/errorpage.php");
    } else {
        include ($_SESSION["CFG"]["templatedir"] . "/player_lookup_form.php");
        mysqli_data_seek($playerResults, 0);
        $num_fields = mysqli_num_fields($playerResults);
        $num_rows = mysqli_num_rows($playerResults);
?>

				<table cellpadding="20" width="650" class="bordertable">
                       <tr class="loginth">
                           <td height="25"><span class="whitenorm"><div align="center">First Name</div></span></td>
                           <td height="25"><span class="whitenorm"><div align="center">Last Name</div></span></td>
                           <td height="25"><span class="whitenorm"><div align="center">Club</div></span></td>

                           <td></td>
                       </tr>

                <?


		$rownum = mysqli_num_rows($playerResults);
		while ($playerarray = mysqli_fetch_array($playerResults)) {

			 $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
			
			?>
			
			<tr class="<?=$rc?>" >
			<form name="playerform<?=$rownum?>" method="get" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_info.php">
			<td><div align="center"><?=$playerarray['firstname']?></div> </td>
			<td><div align="center"><?=$playerarray['lastname']?></div> </td>
				<input type="hidden" name="userid" value="<?=$playerarray[0]?>">
				<input type="hidden" name="searchname" value="<?=$searchname?>">
			<td><div align="center"><?=$playerarray['clubname']?></div> </td>
			<td><div align="center"><a href="javascript:submitForm('playerform<?=$rownum?>')">Info</a></td>
			</form>
			</tr>
			
			<?
			$rownum = $rownum -1;
		}
?>
            
                  </table>
                  
                 <div style="height: 2em;"></div>
                 <div>
                 	<span style="text-align: right;"> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_lookup.php"><< New Search</a>  </span>
                 </div> 
                 
                <?
	}
}

	/******************************************************************************
	 * Get_player_search
	 *****************************************************************************/

	function get_all_player_search($searchname) {

		$playerquery = "SELECT users.userid, users.firstname, users.lastname,  clubs.clubname
		                        FROM tblUsers users, tblClubs clubs, tblClubUser clubuser
		                        WHERE clubuser.clubid = clubs.clubid
								AND users.userid = clubuser.userid
		                        AND (users.firstname Like '$searchname%'
		                        OR users.lastname Like '$searchname%')
		                        AND clubuser.roleid != 4
								AND clubuser.enddate IS NULL
		                        ORDER BY users.lastname";
		if(isDebugEnabled(1) ) logMessage($playerquery);
		
		return db_query($playerquery);

	}
?>
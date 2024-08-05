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
* - password_valid()
* - update_password()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
require_login();

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST)) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
   
    
    if (empty($errormsg)) {
        
        $noticemsg = "";
    }
}
$DOC_TITLE = "League Schedule";

$league_schedule = load_league_schedule();

include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/league_schedule_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the forgot password form, and return the error messages in a string.
     * if the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = ""; // put whatever validation message here
    
    
    return $msg;
}

function load_league_schedule(){

    $query = "SELECT concat(tU1.firstname, ' ',tU1.lastname) AS player1, concat(tU2.firstname, ' ',tU2.lastname) AS player2, tBL.boxname
                FROM tblBoxLeagueSchedule
                INNER JOIN clubpro_main.tblBoxLeagues tBL on tblBoxLeagueSchedule.boxid = tBL.boxid
                INNER JOIN clubpro_main.tblUsers tU1 on tblBoxLeagueSchedule.userid1 = tU1.userid
                INNER JOIN clubpro_main.tblUsers tU2 on tblBoxLeagueSchedule.userid2 = tU2.userid
                WHERE tBL.siteid = ". get_siteid();

    return db_query($query);
}


?>
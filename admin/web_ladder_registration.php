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
* - insert_box()
* Classes list:
*/
include ("../application.php");
require_login();
require_priv("2");

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    
    if (empty($errormsg) && empty($action)) {
        insert_box($frm);
    }
}
$DOC_TITLE = "Box League Setup";
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/web_ladder_registration_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    if (empty($frm["boxname"])) {
        $errors->boxname = true;
        $msg.= "You did not specify a box name ";
    } elseif (empty($frm["courttypeid"])) {
        $errors->courttypeid = true;
        $msg.= "You did not specify a court type.";
    }

    if( isJumpLadderRankingScheme() ) {
        if ( empty($frm["ladder"]) ){
            
            $errors->ladder = true;
            $msg.= "Please specifiy the ladder."; 
        }
    }

    return $msg;
}
function insert_box(&$frm) {

    /* add the new user into the database */
    $clubquery = "SELECT timezone from tblClubs WHERE clubid=" . get_clubid() . "";
    $clubresult = db_query($clubquery);
    $clubobj = db_fetch_object($clubresult);
    $tzdelta = $clubobj->timezone * 3600;

    //Pad Zeros where necessary
    $daystring = "$frm[enddateday]";
    $monthstring = "$frm[enddatemonth]";
    
    if ($frm['enddateday'] < 10) {
        $daystring = "0$frm[enddateday]";
    }
    
    if ($frm['enddatemonth'] < 10) {
        $monthstring = "0$frm[enddatemonth]";
    }
    $datestring = "$frm[enddateyear]$monthstring$daystring";
    $timestamp = gmmktime(0, 0, 0, $frm['enddatemonth'], $frm['enddateday'], $frm['enddateyear']);

    //Get the number of Boxes at the club
    $useridquery = "SELECT boxrank
                          FROM tblBoxLeagues
                          WHERE siteid=" . get_siteid() . "";
    $useridresult = db_query($useridquery);
    $numberofrows = mysqli_num_rows($useridresult);
    $lastboxrank = $numberofrows + 1;
    $ladderid = isset($frm[ladder]) ? $frm[ladder] : "NULL";
    $boxname = addslashes($frm['boxname']);
    $autoschedule = 'yes' ? 'TRUE':'FALSE';
    $query = "INSERT INTO tblBoxLeagues (
                boxname, siteid, courttypeid, boxrank, enddate, enddatestamp,ladderid,autoschedule
                ) VALUES (
                          '$boxname'
                          ,'" . get_siteid() . "'
                          ,$frm[courttypeid]
                          ,$lastboxrank
                          ,$datestring
                          ,$timestamp
                          ,$ladderid
                          ,$autoschedule)";

    // run the query on the database
    $result = db_query($query);
}
?>



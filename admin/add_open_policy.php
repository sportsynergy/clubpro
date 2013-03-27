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
* - insert_hours_policy()
* Classes list:
*/
include ("../application.php");
require_login();
require_priv("3");
$DOC_TITLE = "Club Hours Policy";

/* form has been submitted, try to create the new role */

if (match_referer() && isset($_POST['submit'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    $backtopage = "$wwwroot/admin/add_open_policy.php";
    
    if (empty($errormsg)) {
        insert_hours_policy($frm);
        header("Location: " . $_SESSION["CFG"]["wwwroot"] . "/admin/manage_club_policies.php");
        die;
    }
} elseif (isset($_POST['back'])) {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    header("Location: $wwwroot/admin/manage_club_policies.php");
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/add_open_policy_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new Object;
    $msg = "";
    $openTimeArray = explode(":", $frm["opentime"]);
    $closeTimeArray = explode(":", $frm["closetime"]);
    
    if (empty($frm["siteid"])) {
        $msg.= "You did not specify an site.";
    } elseif (empty($frm["opentime"])) {
        $msg.= "You did not specify an open time.";
    } elseif (empty($frm["closetime"])) {
        $msg.= "You did not specify an close time.";
    } elseif ($openTimeArray[0] > $closeTimeArray[0]) {
        $msg.= "The club has to open before it closes.";
    }
    return $msg;
}
function insert_hours_policy(&$frm) {

    /* add the new user into the database */
    $query = "INSERT INTO tblHoursPolicy (
                siteid, day, month, year, opentime, closetime, enable
                ) VALUES (
                           '$frm[siteid]'
                          ,'$frm[day]'
                          ,'$frm[month]'
                          ,'$frm[year]'
                          ,'$frm[opentime]'
                          ,'$frm[closetime]'
                          ,'1'
                          )";

    // run the query on the database
    $result = db_query($query);
}
?>
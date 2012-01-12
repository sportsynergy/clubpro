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
* - insert_boxuser()
* Classes list:
*/
include ("../application.php");
require_login();
require_priv("2");

//Set the http variables
$action = $_REQUEST["action"];
$boxid = $_REQUEST["boxid"];

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm);
    
    if (empty($errormsg) && empty($action)) {
        insert_boxuser($frm);
    }
}
$boxnamequery = "SELECT boxleague.boxname, boxleague.courttypeid
                           FROM tblBoxLeagues boxleague
                           WHERE boxid=$boxid";
logMessage("web_ladder_manage: $boxnamequery");

// run the query on the database
$result = db_query($boxnamequery);
$boxarray = mysql_fetch_array($result);

//Set some variables for the form
$DOC_TITLE = "Box League - $boxarray[0] ";
$courttype = $boxarray[1];
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/web_ladder_manage_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($frm) {

    // Make sure that the box user isn't already in a box
    
    if (isDebugEnabled(1)) logMessage("web_ladder_manage.validate_form");
    $errors = new Object;
    $msg = "";
    
    if (empty($frm["boxuser"])) {
        
        if (isDebugEnabled(1)) logMessage("\t-> boxuser is empty");
        $errors->boxuser = true;
        $msg.= "Please select a valid user.";
    } elseif (!empty($frm['boxuser'])) {
        $boxUserQuery = "SELECT userid from tblkpBoxLeagues WHERE userid = $frm[boxuser]";
        $boxUserResult = db_query($boxUserQuery);
        
        if (mysql_num_rows($boxUserResult) > 0) {
            
            if (isDebugEnabled(1)) logMessage("\t-> boxuser is not in a box");
            $errors->boxuser = true;
            $msg.= "A player cannot be in more that one box at a time.";
        }
    }
    return $msg;
}
function insert_boxuser(&$frm) {

    // First thing we need to do is find out how many players are
    // in the league.  This will determine what place the user will start with.

    // Build Query

    $boxcountquery = "SELECT boxid
                        FROM tblkpBoxLeagues where boxid=$frm[boxid]";

    // run the query on the database
    $boxcountresult = db_query($boxcountquery);
    $boxcountval = mysql_num_rows($boxcountresult);
    $boxcountval = $boxcountval + 1;

    /* add the new user into the database */
    $query = "INSERT INTO tblkpBoxLeagues (
                boxid, userid, boxplace
                ) VALUES (
                           '$frm[boxid]'
                          ,'$frm[boxuser]'
                          , $boxcountval)";

    // run the query on the database
    $result = db_query($query);
}
?>





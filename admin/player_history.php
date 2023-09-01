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
* - update_settings()
* - load_user_history()
* Classes list:
*/
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/
include ("../application.php");
require_login();

//Set the http variables
$userid = $_REQUEST["userid"];
$searchname = $_REQUEST["searchname"];

if (isset($userid)) {
    $userHistoryResult = load_user_history($userid);
    $frm = load_user_profile($userid);
} else {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    header("Location:  $wwwroot/admin/player_lookup.php");
}
$DOC_TITLE = "Player History";
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_history_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 *  Validate_form
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the forgot password form, and return the error messages in a string.
     * if the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    if (empty($frm["email"])) {
        $errors->email = true;
        $msg.= "<li>You did not specify your email address";
    } elseif (empty($frm["firstname"])) {
        $errors->firstname = true;
        $msg.= "<li>You did not specify your first name";
    } elseif (empty($frm["lastname"])) {
        $errors->lastname = true;
        $msg.= "<li>You did not specify your last name";

        //Not a required Feild
        //} elseif (empty($frm["homephone"])) {

        //        $errors->homephone = true;

        //        $msg .= "<li>You did not specify your home phone number";

        //Not a required Feild

        //} elseif (empty($frm["workphone"])) {

        //        $errors->workphone = true;

        //        $msg .= "<li>You did not specify your work phone number";

        
    } elseif (empty($frm["useraddress"])) {
        $errors->useraddress = true;
        $msg.= "<li>You did not specify your address";
    }
    return $msg;
}

/******************************************************************************
 * Update_settings
 *****************************************************************************/
function update_settings(&$frm) {

    /* set the user's password to the new one */
    
    if (!$frm[userid]) {
        $userid = get_userid();
    }

    //Update User
    $qid = db_query("
        UPDATE tblUsers SET
                 email = '$frm[email]'
                ,firstname = '$frm[firstname]'
                ,lastname = '$frm[lastname]'
                ,homephone = '$frm[homephone]'
                ,workphone = '$frm[workphone]'
                ,useraddress = '$frm[useraddress]'
        WHERE userid = '$userid'
        ");

    //Update Club Specific Settings
    $qid = db_query("
        UPDATE tblClubUser SET 
                ,recemail = '$frm[recemail]'
                ,msince = '$frm[msince]'
        WHERE userid = '$userid' AND clubid = " . get_clubid() . "
        ");
    unset($userid);
}

/******************************************************************************
 * Load User History
 Most of this was pulled from the my reservation page.
 *****************************************************************************/
function load_user_history($userid) {
    $curresidquery = "SELECT reservations.reservationid
                  FROM tblUsers users, tblReservations reservations, tblkpUserReservations reservationdetails
                  WHERE users.userid = reservationdetails.userid
				  AND reservationdetails.userid=$userid
                  AND reservations.reservationid = reservationdetails.reservationid
                  AND reservations.usertype=0
				  AND users.enddate IS NULL
                  ORDER BY reservations.time DESC
                  LIMIT 25";

    // run the query on the database
    return db_query($curresidquery);
}
?>
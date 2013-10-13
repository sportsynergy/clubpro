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

include ("../application.php");

//Set the http variables
$userid = $_REQUEST["userid"];
$boxid = $_REQUEST["boxid"];
$page = $_REQUEST["page"];

//put in validation that current user has access to
require_login();
require_priv_user($userid);
require_priv_box($boxid);


/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm);
    
    if (empty($errormsg) && empty($action)) {
		$noticemsg = "Box League Updated.  Good Job!<br/><br/>";
    }
}

// Get the box history for that user
$query = "SELECT reservations.reservationid 
            FROM tblBoxHistory history
			INNER JOIN tblReservations reservations ON reservations.reservationid = history.reservationid
			INNER JOIN tblkpUserReservations details ON details.reservationid = reservations.reservationid
			WHERE boxid = $boxid AND details.userid = $userid
             AND (SELECT sum(outcome) 
                    FROM tblkpUserReservations 
                    WHERE reservationid = reservations.reservationid GROUP BY reservationid) > 0 
            GROUP BY reservationid
            ORDER BY reservationid";



// run the query on the database
$result = db_query($query);


$boxuserquery = "SELECT concat(users.firstname,' ',users.lastname) AS `name`
                 	FROM tblUsers users
                    WHERE userid = $userid";

// run the query on the database
$box_name_result = db_query($boxuserquery);



//Set some variables for the form
$DOC_TITLE = "Box League History for ".mysql_result($box_name_result,0);

include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/web_ladder_history_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/




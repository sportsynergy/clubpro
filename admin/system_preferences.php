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
* - update_clubprefs()
* Classes list:
*/
include ("../application.php");
$DOC_TITLE = "System Preferences";
require_login();

/* form has been submitted, try to create the new role */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm['message']);
    
    if (empty($errormsg)) {
        update_clubprefs($frm['message']);
        $noticemsg = "System Preferences Saved. <br/><br/>";
    }
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/system_preferences_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($message) {

    /* Just make sure that if they turn this little bugger on that they type in a message */
    $errors = new Object;
    $msg = "";
}
function update_clubprefs($message) {
    
    if (isDebugEnabled(1)) logMessage("system_preferneces.update_clubprefs: Updating system preferences.");

    //First, end date the current one
    $query = "Update tblFooterMessage SET enddate = now()
                WHERE enddate IS NULL";
    $result = db_query($query);

    //Then add the new one.
    //Insert the Club User (for the new club)

    $query = "INSERT INTO tblFooterMessage (
                 text, enddate
                ) VALUES (
                          '$message'
						  ,NULL
                         )";
    $result = db_query($query);

    //Update the session
    unset($_SESSION["footermessage"]);

    // Strip Slashes
    
    if (get_magic_quotes_gpc()) {
        $message = stripslashes($message);
    }
    $_SESSION["footermessage"] = $message;
}
?>
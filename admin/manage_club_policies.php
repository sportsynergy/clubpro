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
* - get_clubs_policies()
* - removeHoursPolicy()
* Classes list:
*/
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/
include ("../application.php");
require_login();
$DOC_TITLE = "Club Policy";
require_priv("3");

/* form has been submitted, try to create the new role */

if (match_referer() && isset($_POST['policyid'])) {
    removeHoursPolicy($_POST['policyid']);
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/manage_club_policies_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    if (empty($frm["sportname"])) {
        $errors->sportname = true;
        $msg.= "You did not specify a sport name";
    }
    return $msg;
}

/************************************************************************************************************************/

/*
     This function returns a list of players for player searches
*/
function get_clubs_policies() {
    $playerquery = "SELECT tblHoursPolicy.*, tblClubSites.sitename, tblClubs.clubname
                   FROM (tblHoursPolicy
                   INNER JOIN tblClubSites
                   ON tblHoursPolicy.siteid = tblClubSites.siteid)
                   INNER JOIN tblClubs ON tblClubSites.clubid = tblClubs.clubid";
    return db_query($playerquery);
}
function removeHoursPolicy($pid) {
    $qid1 = db_query("DELETE FROM tblHoursPolicy
                     WHERE policyid = $pid");
}
?>
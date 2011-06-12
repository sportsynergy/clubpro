<?php

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */

include("../application.php");
require_login();
$DOC_TITLE = "Club Policy";
require_priv("3");


/* form has been submitted, try to create the new role */


if (match_referer() && isset($_POST['policyid'])) {


   removeHoursPolicy($_POST['policyid']);


}

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/manage_club_policies_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

        if (empty($frm["sportname"])) {
                $errors->sportname = true;
                $msg .= "You did not specify a sport name";
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


function removeHoursPolicy($pid){

   $qid1 = db_query("DELETE FROM tblHoursPolicy
                     WHERE policyid = $pid");
}
?>
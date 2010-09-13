<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */

include("../application.php");
require_login();
$DOC_TITLE = "Club Policy";

/* form has been submitted, try to create the new role */


if (match_referer() && isset($_POST['policyid'])) {


   removeHoursPolicy($_POST['policyid']);


}

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/manage_club_policies_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

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
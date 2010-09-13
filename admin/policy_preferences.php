<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */

include("../application.php");
$DOC_TITLE = "Club Preferences";
require_login();
/* form has been submitted, try to create the new role */
if (match_referer() && isset($_POST['skillpolicyid'])) {

   removeSkillRangePolicy($_POST['skillpolicyid']);
}
elseif (match_referer() && isset($_POST['schedulepolicyid'])) {

   removeSchedulePolicy($_POST['schedulepolicyid']);
}

elseif (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $backtopage = $_SESSION["CFG"]["wwwroot"]."/admin/policy_preferences.php";

        if ($errormsg){
             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
        }

         else {
                update_clubprefs($frm);
                include($_SESSION["CFG"]["templatedir"]."/header.php");
                include($_SESSION["CFG"]["includedir"]."/include_success.php");
                include($_SESSION["CFG"]["templatedir"]."/footer.php");


                die;
        }
}



$reservationPolicies = load_reservation_policies(get_siteid());
$skillRangePolicies = load_skill_range_policies(get_siteid());

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/policy_preferences_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* Just make sure that if they turn this little bugger on that they type in a message */

        $errors = new Object;
        $msg = "";

        //Really nothing to validiate here


        return $msg;
}



/*****************************************************************/
/*
     UPdates the database
*/
function update_clubprefs(&$frm) {

     if($frm["enableprimetime"]=="on"){
             $enableprimetime = 1;
         }
         else{
             $enableprimetime = 0;
         }

        $query = "Update tblClubSites
                  SET enableprimetime = $enableprimetime,
                      weeklyprimetimelimit = '$frm[weeklyprimetimelimit]'
                  WHERE siteid = ".get_siteid()."";


       $result = db_query($query);

}




/*****************************************************************/
/*
     Will load the windows
*/
function load_skill_range_policies($siteid){

         $query = "SELECT policy.policyname, 
         				  policy.policyid,
         				  policy.description
                   FROM tblSkillRangePolicy policy
                   WHERE policy.siteid = $siteid";

         return  db_query($query);


}

/*****************************************************************/
/*
     Will remove a scheduling policy
*/
function removeSchedulePolicy($pid){

   $qid1 = db_query("DELETE FROM tblSchedulingPolicy
                     WHERE policyid = $pid");
}

/*****************************************************************/
/*
     Will remove a skill range policy
*/
function removeSkillRangePolicy($pid){

   $qid1 = db_query("DELETE FROM tblSkillRangePolicy
                     WHERE policyid = $pid");
}

?>
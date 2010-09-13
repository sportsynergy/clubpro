<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */

include("../application.php");
require_login();
$DOC_TITLE = "Club Hours Policy";

/* form has been submitted, try to create the new role */
if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $wwwroot = $_SESSION["CFG"]["wwwroot"];
        $backtopage = "$wwwroot/admin/add_open_policy.php";

        if ($errormsg){
             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
        }

         else {
                insert_hours_policy($frm);
                 header ("Location: ".$_SESSION["CFG"]["wwwroot"]."/admin/manage_club_policies.php");
                die;
        }
}

elseif(isset($_POST['back']))  {
	  $wwwroot = $_SESSION["CFG"]["wwwroot"];
      header ("Location: $wwwroot/admin/manage_club_policies.php");
}

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/add_open_policy_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";
        $openTimeArray = explode (":", $frm["opentime"]);
        $closeTimeArray = explode (":", $frm["closetime"]);

        if (empty($frm["siteid"])) {

                $msg .= "You did not specify an site.";
         }
         elseif (empty($frm["opentime"])) {

                $msg .= "You did not specify an open time.";
         }
         elseif (empty($frm["closetime"])) {

                $msg .= "You did not specify an close time.";
         }
         elseif ($openTimeArray[0] > $closeTimeArray[0] ){

                $msg .= "The club has to open before it closes.";
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
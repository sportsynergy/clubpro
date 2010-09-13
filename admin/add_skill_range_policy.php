<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */

include("../application.php");
require_login();
$DOC_TITLE = "Skill Range Policy Setup";

//This puppy will be set when editing a policy
$policyid = $_REQUEST["policyid"];
if( !empty($policyid) ) {
	$skillRangePolicy = load_skill_range_policy($policyid);
}


/* form has been submitted, try to create the new role */
if (match_referer() && isset($_POST['submit'])) {
        
		$frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $wwwroot = $_SESSION["CFG"]["wwwroot"];
        
        if( !empty($policyid) ){
        	$backtopage = "$wwwroot/admin/add_skill_range_policy.php?policyid=".$frm['policyid'];
        }else{
        	$backtopage = "$wwwroot/admin/add_skill_range_policy.php";
        }
        

        if ($errormsg){
             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
        }

         else {
                insert_skill_range_policy($frm);
                $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 header ("Location: $wwwroot/admin/policy_preferences.php");


                die;
        }
}

elseif(isset($_POST['back']))  {
	  $wwwroot = $_SESSION["CFG"]["wwwroot"];
      header ("Location: $wwwroot/admin/policy_preferences.php");
}
elseif( isset($_POST['skillpolicyid']) ){
       $policy = load_skill_range_policy($_POST['skillpolicyid']);
}

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/add_skill_range_policy_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {


        $errors = new Object;
        $msg = "";

        if( isset($frm["starttime"]) ){
             $startTimeArray = explode (":", $frm["starttime"]);
        }
        if( isset($frm["endtime"]) ){
            $endTimeArray = explode (":", $frm["endtime"]);
        }


         //Make sure that they selected everything
         if (empty($frm["name"])) {

                $msg .= "You did not specify a policy name.";
         }
         elseif (empty($frm["description"])) {

                $msg .= "You did not specify a description.";
         }
         elseif (empty($frm["skillrange"])) {

                $msg .= "You did not specify a skill range.";
         }
         elseif (empty($frm["courtid"])) {

                $msg .= "You did not specify a court.";
         }
         elseif ($frm["dow"]=="") {

                $msg .= "You did not specify a day of the week.";
         }

         elseif ( !empty($frm["reservationwindow"]) && empty($frm["starttime"]) ){

                $msg .= "You did not specify a start time.";

         }
         elseif ( !empty($frm["reservationwindow"]) && empty($frm["endtime"]) ) {

                $msg .= "You did not specify an end time.";
         }
         //Validate that the start is before end
         elseif (isset($startTimeArray) && isset($endTimeArray) && $startTimeArray[0] > $endTimeArray[0] ){

                $msg .= "The start time needs to occur before the end time.";
         }

        return $msg;


}

function get_sitecourts_dropdown($siteid){

         $query = "SELECT courtid, courtname
                   FROM tblCourts
                   WHERE siteid = $siteid
                   AND enable =1";

       return db_query($query);

}

function get_dow_dropdown(){

         $query = "SELECT dayid, name
                   FROM tblDays";

       return db_query($query);

}


function insert_skill_range_policy(&$frm) {
/* add the new user into the database */

   if($frm['courtid']=="all"){
       $courtid = "NULL";
   }else{
      $courtid = $frm['courtid'];
   }

   if($frm['dow']=="all"){
      $dayid = "NULL";
   }else{
      $dayid = $frm['dow'];

   }

   if(!isset($frm['reservationwindow'])){
       $alltimes = 'y';
       $starttime = "NULL";
       $endtime = "NULL";

   }else{
      $alltimes = 'n';
      $starttime = "'$frm[starttime]'";
      $endtime = "'$frm[endtime]'";
   }


   
   //If this is the case, then we're updating an existing policy
		if( !empty($frm['policyid'] )) {
			
			$query = "UPDATE tblSkillRangePolicy SET
					policyname = '$frm[name]'
	                ,description = '$frm[description]'
	                ,skillrange = '$frm[skillrange]'
	                ,dayid = $dayid
	                ,courtid = $courtid
	                ,siteid = ". get_siteid()."
	                ,starttime = $starttime
	                ,endtime = $endtime
	        		WHERE policyid = '$frm[policyid]'";
		
		}else{
	        $query = "INSERT INTO tblSkillRangePolicy (
	                policyname, description, skillrange, dayid, courtid, siteid, starttime, endtime
	                ) VALUES (
	                           '$frm[name]'
	                          ,'$frm[description]'
	                          ,'$frm[skillrange]'
	                          ,$dayid
	                          ,$courtid
	                          ,".get_siteid()."
	                          ,$starttime
	                          ,$endtime)";

		}
		
        // run the query on the database
        $result = db_query($query);

}

?>
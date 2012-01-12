<?php

include("../application.php");
require_login();
require_priv("2");

// Include jQuery
define("_JQUERY_",true);
// Include PrettyPhoto
define("_PRETTYPHOTO_",true);

$DOC_TITLE = "Skill Range Policy Setup";
$buttonLabel = "Add Skill Range Policy";

//This puppy will be set when editing a policy
$policyid = $_REQUEST["policyid"];
if( !empty($policyid) ) {
	$skillRangePolicy = load_skill_range_policy($policyid);
	$buttonLabel = "Update Skill Range Policy";
}


/* form has been submitted, try to create the new role */
if (match_referer() && isset($_POST['submitme'])) {
        
		$frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $wwwroot = $_SESSION["CFG"]["wwwroot"];

        if ( empty($errormsg) ) {
             insert_skill_range_policy($frm);
             $wwwroot = $_SESSION["CFG"]["wwwroot"];
             header ("Location: $wwwroot/admin/policy_preferences.php#skill");
        	
        }
        
}

elseif(isset($_POST['back']))  {
	  $wwwroot = $_SESSION["CFG"]["wwwroot"];
      header ("Location: $wwwroot/admin/policy_preferences.php");
}

elseif( isset($_POST['skillpolicyid']) ){
       $policy = load_skill_range_policy($_POST['skillpolicyid']);
}

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/add_skill_range_policy_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

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
                 $errors->name = true;
         }
         elseif (empty($frm["description"])) {

                $msg .= "You did not specify a description.";
                $errors->description = true;
         }
         elseif (empty($frm["skillrange"])) {

                $msg .= "You did not specify a skill range.";
                 $errors->skillrange = true;
         }
         elseif (empty($frm["courtid"])) {

                $msg .= "You did not specify a court.";
                $errors->courtid = true;
         }
         elseif ($frm["dow"]=="") {

                $msg .= "You did not specify a day of the week.";
                $errors->dow = true;
         }

         elseif ( !empty($frm["reservationwindow"]) && empty($frm["starttime"]) ){

                $msg .= "You did not specify a start time.";
                  $errors->starttime = true;

         }
         elseif ( !empty($frm["reservationwindow"]) && empty($frm["endtime"]) ) {

                $msg .= "You did not specify an end time.";
                  $errors->endtime = true;
         }
         //Validate that the start is before end
         elseif (isset($startTimeArray) && isset($endTimeArray) && $startTimeArray[0] > $endTimeArray[0] ){

                $msg .= "The start time needs to occur before the end time.";
                  $errors->window = true;
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

	
	// Strip Slashes
	if(get_magic_quotes_gpc()){
		$description=stripslashes($frm['description']);
	
	}else{
		$description=addslashes($frm['description']);
	}
	
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
	                ,description = '$description'
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
	                          ,'$description'
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
<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */

include("../application.php");
require_login();
$DOC_TITLE = "Scheduling Policy Setup";

$policyid = $_REQUEST["policyid"];

//If a policy id was passed in, then load it up.
if( !empty($policyid) ) {
	$schedulePolicy = load_reservation_policy($policyid);
}



/* form has been submitted, try to create the new role */
if (match_referer() && isset($_POST['submit'])) {
        
	$frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $wwwroot = $_SESSION["CFG"]["wwwroot"];
        
        if( !empty($policyid) ){
        	$backtopage = "$wwwroot/admin/add_scheduling_policy.php?policyid=".$frm['policyid'];
        }else{
        	$backtopage = "$wwwroot/admin/add_scheduling_policy.php";
        }
        

        if ($errormsg){
             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
        }

         else {
                insert_hours_policy($frm);
                 header ("Location: $wwwroot/admin/policy_preferences.php");
                die;
        }
}

elseif(isset($_POST['back']))  {
	  $wwwroot = $_SESSION["CFG"]["wwwroot"];
      header ("Location: $wwwroot/admin/policy_preferences.php");
}

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/add_scheduling_policy_form.php");
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

function get_scheduling_policy_types(){

         $query = "SELECT id, policytypename
                   FROM tblSchedulingPolicyType ";

       return db_query($query);

}


function insert_hours_policy(&$frm) {
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

if($frm['allowlooking']=="yes"){
	$allowlooking = 'y';
}
else{
	$allowlooking = 'n';
}

// Back to Back
if($frm['back2back']=="yes"){
	$allowback2back = 'y';
}
else{
	$allowback2back = 'n';
}

if(!isset($frm['reservationwindow'])){
    
    $starttime = "NULL";
    $endtime = "NULL";

}else{
  
    $starttime = "'$frm[starttime]'";
    $endtime = "'$frm[endtime]'";
}

		//If this is the case, then we're updating an existing policy
		if( !empty($frm['policyid'] )) {
			
		if( isDebugEnabled(1) ) logMessage("add_scheduling_policy.insert_hours_policy: Updating scheduling policy ".$frm['policyid'] );	
			
		$query = "UPDATE tblSchedulingPolicy SET
				policyname = '$frm[name]'
                ,description = '$frm[description]'
                ,schedulelimit = '$frm[limit]'
                ,dayid = $dayid
                ,courtid = $courtid
                ,siteid = ". get_siteid()."
                ,allowlooking = '$allowlooking'
                 ,allowback2back = '$allowback2back'
                ,starttime = $starttime
                ,endtime = $endtime
        WHERE policyid = '$frm[policyid]'";
		
		}else{

			if( isDebugEnabled(1) ) logMessage("add_scheduling_policy.insert_hours_policy: Adding new scheduling policy ");	
			
	        $query = "INSERT INTO tblSchedulingPolicy (
	                policyname, description,schedulelimit,dayid,courtid,siteid,allowlooking,allowback2back,starttime,endtime
	                ) VALUES (
	                           '$frm[name]'
	                          ,'$frm[description]'
	                          ,$frm[limit]
	                          ,$dayid
	                          ,$courtid
	                          ,".get_siteid()."
	                          ,'$allowlooking'
	                          ,'$allowback2back'
	                          ,$starttime
	                          ,$endtime)";
		}
		

        // run the query on the database
        $result = db_query($query);

}

?>
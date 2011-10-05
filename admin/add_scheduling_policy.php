<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */

include("../application.php");
require_login();
require_priv("2");

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


        if ( empty($errormsg)) {
             insert_hours_policy($frm);
              header ("Location: $wwwroot/admin/policy_preferences.php#schedule");
             die;
        }

        
}

elseif(isset($_POST['back']))  {
	  $wwwroot = $_SESSION["CFG"]["wwwroot"];
      header ("Location: $wwwroot/admin/policy_preferences.php");
}

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/add_scheduling_policy_form.php");
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
                $errors->startime = true;
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
                ,description = '$description'
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
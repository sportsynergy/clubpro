<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */

include("../application.php");
$DOC_TITLE = "Club Preferences";
require_login();

	if (isset($_POST['skillpolicyid'])) {
	
	   removeSkillRangePolicy($_POST['skillpolicyid']);
	}
	elseif (isset($_POST['schedulepolicyid'])) {
	
	   removeSchedulePolicy($_POST['schedulepolicyid']);
	}

	if (match_referer() && isset($_POST['submit']) ){
		
		//Save Messages
		if(match_referer() && $_POST['preferenceType'] == "message"){
				$frm = $_POST;
		        $errormsg = validate_messages_form($frm, $errors);
		       
		        if ( empty($errormsg))  {
		             update_message_clubprefs($frm);
		             $noticemsg = "Preferences Saved.  Good Job!<br/><br/>";
		        }
		}
		//Save General Preferences
		elseif(match_referer() && $_POST['preferenceType'] == "general"){
				$frm = $_POST;
	
		        if ( empty($errormsg))  {
		             update_general_clubprefs($frm);
		             $noticemsg = "Preferences Saved.  Good Job!<br/><br/>";
		        }
		}
		
	}



$reservationPolicies = load_reservation_policies(get_siteid());
$skillRangePolicies = load_skill_range_policies(get_siteid());
$siteMessages = load_site_messages(get_siteid());

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/policy_preferences_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

/**
 * 
 * @param unknown_type $frm
 * @param unknown_type $errors
 */
function validate_form(&$frm, &$errors) {
/* Just make sure that if they turn this little bugger on that they type in a message */

        $errors = new Object;
        $msg = "";

        //Really nothing to validiate here


        return $msg;
}


/**
 * 
 * @param $frm
 * @param $errors
 */
function validate_messages_form(&$frm, &$errors) {
/* Just make sure that if they turn this little bugger on that they type in a message */

        $errors = new Object;
        $msg = "";

        if ($frm["messagedisplay"]=="on" && empty($frm["Messagetextarea"])) {
                $errors->Messagetextarea = true;
                $msg .= "You are attempting to turn the message display on with no message.  Turn off the display message or type in a message.";
         }
        elseif(empty($frm["Messagetextarea"])){
                $errors->Messagetextarea = true;
                $msg .= "You didn't specifiy a message.";
        }
		elseif( eregi("'",$frm["Messagetextarea"])){
			 $errors->Messagetextarea = true;
                $msg .= "Please don't use apostrophes, I beg of you.";
		}
		elseif( eregi("\n",$frm["Messagetextarea"])){
			 $errors->Messagetextarea = true;
                $msg .= "This time don't put a return in the message";
		}


        return $msg;
}

/**
 * 
 * @param $frm
 * @param $errors
 */
function validate_skillrange_form(&$frm, &$errors) {


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

/**
 * 
 * @param $siteid
 */
function get_sitecourts_dropdown($siteid){

         $query = "SELECT courtid, courtname
                   FROM tblCourts
                   WHERE siteid = $siteid
                   AND enable =1";

       return db_query($query);

}

/**
 * 
 */
function get_dow_dropdown(){

         $query = "SELECT dayid, name
                   FROM tblDays";

       return db_query($query);

}


/**
 * 
 * @param $siteid
 */
function load_site_messages($siteid) {
/* load up the user's details */

        //if userid exists then the club administrator is updating a users account


        $qid = db_query("SELECT messages.message, messages.enable
                         FROM tblMessages messages
                         WHERE messages.siteid = $siteid");




        return db_fetch_array($qid);

   }
   
/**
 * 
 * @param unknown_type $frm
 */
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

/**
 * 
 * @param $frm
 * @param $errors
 */
function validate_scheduling_policy_form(&$frm, &$errors) {


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

/**
 * 
 */
function get_scheduling_policy_types(){

         $query = "SELECT id, policytypename
                   FROM tblSchedulingPolicyType ";

       return db_query($query);

}

/**
 * 
 * @param unknown_type $frm
 */
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

/**
 * 
 */
function load_club_messages() {
/* load up the user's details */

        //if userid exists then the club administrator is updating a users account


        $qid = db_query("SELECT messages.message, messages.enable
                         FROM tblMessages messages
                         WHERE messages.siteid = ".get_siteid()."");




        return db_fetch_array($qid);

   }

/**
* 
* @param $frm
*/
function update_message_clubprefs(&$frm) {
/* add the new user into the database */
         if($frm["messagedisplay"]=="on"){
             $displaymessage = 1;
         }
         else{
             $displaymessage = 0;
         }
         //Check to see if club has a message
         $qid = db_query("SELECT message, enable FROM tblMessages WHERE siteid = ".get_siteid()."");
         $numrows = mysql_num_rows($qid);

         if($numrows==0){
         $query = "INSERT INTO tblMessages (
                   siteid, message, enable
                   ) VALUES (
                   '".get_siteid()."'
                   ,'$frm[Messagetextarea]'
                   ,'$displaymessage')";
         }
         elseif($numrows==1){
        $query = "Update tblMessages SET
                message = '$frm[Messagetextarea]'
                ,enable = '$displaymessage'
                WHERE siteid = '".get_siteid()."'";
         }


        // run the query on the database
        $result = db_query($query);



}

/**
 * 
 * @param $frm
 */
function update_general_clubprefs(&$frm) {


		if(isDebugEnabled(1) ) logMessage("general_preferneces.update_clubprefs: Updating club preferences.");
		/* Update the club preferences */
		
        $query = "Update tblClubSites SET rankingadjustment = '$frm[inactivity]'
                ,allowselfcancel = '$frm[allowselfcancel]'
				,daysahead = '$frm[daysahead]'
				,allowselfscore = '$frm[allowselfscore]'
                WHERE siteid = '".get_siteid()."'";
        
        // run the query on the database
        $result = db_query($query);

		// Here is a little quirk.  When an administrator sets the player inactivity adjustment, they probably
		//expect that this be executed starting today, meaning that one month from now people will have their 
		//rankings adjusted and 3 weeks from now players may get a warning email sent.  Do to this we have to 
		// update the rankings lastupdate time when this value changes since this is how whe know when their ranking 
		// was last changed

		if( getRankingAdjustment() != $frm['inactivity']){
			
			$siteusersquery = "SELECT rankings.userid  FROM tblUserRankings rankings, tblkupSiteAuth siteauth, tblUsers users
								WHERE siteauth.siteid = ".get_siteid()."
								AND siteauth.userid = rankings.userid
								AND siteauth.userid = users.userid
								AND users.enddate IS NULL
								AND rankings.usertype = 0";
			
			$result = db_query($siteusersquery);
			
			// Go through and update.
			while($array = db_fetch_array($result)){
				
				if(isDebugEnabled(1) ) logMessage("general_preferneces.update_clubprefs: Updating the lastmodified date for user $array[0]");
				$updatequery = "UPDATE tblUserRankings SET lastmodified = NOW() WHERE usertype = 0 and userid = $array[0] ";
				$updateresult = db_query($updatequery);
			}
			
		}else{
			if(isDebugEnabled(1) ) logMessage("general_preferneces.update_clubprefs: The ranking adjustment hasnt' changed not doing anything.");
		}
	


}
?>
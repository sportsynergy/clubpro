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


if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $wwwroot = $_SESSION["CFG"]["wwwroot"];
        $backtopage = "$wwwroot/admin/general_preferences.php";

        if ($errormsg){
             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
        }

       
        update_clubprefs($frm);
        $noticemsg = "Preferences Saved.  Good Job!<br/><br/>";
               
        
}


$frm = getSitePreferences(get_siteid());

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/form_header.php");
include($_SESSION["CFG"]["templatedir"]."/general_preferences_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* Just make sure that if they turn this little bugger on that they type in a message */

        $errors = new Object;
        $msg = "";


        return $msg;
}


function update_clubprefs(&$frm) {


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
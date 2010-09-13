<?php

/*
 * $LastChangedRevision: 624 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-03 15:57:36 -0600 (Wed, 03 Dec 2008) $
 */

include("../application.php");
$DOC_TITLE = "System Preferences";
require_login();
/* form has been submitted, try to create the new role */


if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm['message']);

        if ( empty($errormsg)){
            update_clubprefs($frm['message']);
            $noticemsg = "System Preferences Saved. <br/><br/>";
        }
 
}


include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/form_header.php");
include($_SESSION["CFG"]["templatedir"]."/system_preferences_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($message) {
/* Just make sure that if they turn this little bugger on that they type in a message */

        $errors = new Object;
        $msg = "";
        
 
}


function update_clubprefs($message) {

	
		
		if(isDebugEnabled(1) ) logMessage("system_preferneces.update_clubprefs: Updating system preferences.");
		
		//First, end date the current one
		 $query = "Update tblFooterMessage SET enddate = now()
                WHERE enddate IS NULL";
		 $result = db_query($query);
		
		//Then add the new one.
		
		//Insert the Club User (for the new club)
		 $query = "INSERT INTO tblFooterMessage (
                 text, enddate
                ) VALUES (
                          '$message'
						  ,NULL
                         )";
		$result = db_query($query);
		
		//Update the session
		unset($_SESSION["footermessage"]);
		
		// Strip Slashes
		if(get_magic_quotes_gpc()){
			$message=stripslashes($message);
		}
		
		$_SESSION["footermessage"] = $message;
		

}

?>
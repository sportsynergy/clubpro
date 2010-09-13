<?

/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/

include("../application.php");
require_login();

//Load in Date
$userid = $_REQUEST["userid"];
if(!isset($userid)){
  $userid = get_userid();
}

$DOC_TITLE = "Edit Account";

unset($authSites);
unset($registeredSports);

$registeredSports = load_registered_sports($userid);
$authSites = load_auth_sites($userid);
$availbleSports = load_avail_sports();
$availableSites = load_avail_sites();


/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_REQUEST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);

          if(isset($userid)){
             $useridstring = "?userid=$userid";
          }
          $backtopage = $_SESSION["CFG"]["wwwroot"]."/users/change_settings.php$useridstring";

		 if (empty($errormsg)){
	       update_settings($frm);
	       $noticemsg = "Your profile was saved.  Good Job!<br/><br/>";
		 }


} else {


        $frm = load_user_profile($userid);

}



include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/form_header.php");
include($_SESSION["CFG"]["templatedir"]."/change_settings_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/



function validate_form(&$frm, &$errors) {
/* validate the forgot password form, and return the error messages in a string.
 * if the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

        if (empty($frm["email"])) {

                $errors->email = true;
                $msg .= "You did not specify your email address";

        } elseif (!is_email_unique($frm["email"],$frm["userid"])) {
                $errors->email = true;
                $msg .= "The email address <b>" . ov($frm["email"]) ."</b> already exists";
		} elseif (!is_email_valid($frm["email"])) {
                $errors->email = true;
                $msg .= "Please enter a valid email address";
		
        } elseif (empty($frm["firstname"])) {
                $errors->firstname = true;
                $msg .= "You did not specify your first name";

        } elseif (empty($frm["lastname"])) {
                $errors->lastname = true;
                $msg .= "You did not specify your last name";
        
        } elseif (empty($frm["homephone"])) {
                $errors->homephone = true;
                $msg .= "You did not specify a home phone number";

        } elseif (empty($frm["workphone"])) {
                $errors->workphone = true;
                $msg .= "You did not specify a work phone number";

        }

        return $msg;
}

function update_settings(&$frm) {
/* set the user's password to the new one */

		 if( isDebugEnabled(1) ) logMessage("Saving profile for ".$frm['firstname']." ".$frm['lastname']);
		 
		 
         //If userid is not set this is being run by a player who is updating
         //their own accoutn information.  If this is the case we will get the userid
         //out of the session.
         if(! isset($frm['userid']) ){
			if( isDebugEnabled(1) ) logMessage("change_settings.update_settings: getting userid from session");
         	$userid = get_userid();
         }else{
         	$userid = $frm['userid'];
         }
         

		//Update User
        $updateQuery = "UPDATE tblUsers SET
                 email = '$frm[email]'
                ,firstname = '$frm[firstname]'
                ,lastname = '$frm[lastname]'
                ,homephone = '$frm[homephone]'
                ,workphone = '$frm[workphone]'
                ,cellphone = '$frm[cellphone]'
                ,pager = '$frm[pager]'
                ,useraddress = '$frm[useraddress]'
        WHERE userid = '$userid'";
        

        db_query($updateQuery);
        
        //Update Club user
        $qid = db_query("
        UPDATE tblClubUser SET
			recemail = '$frm[recemail]'
        WHERE userid = '$userid'");
        

   unset($userid);
   }

?>
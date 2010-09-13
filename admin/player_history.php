<?

/*
 * $LastChangedRevision: 643 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-30 08:51:17 -0800 (Tue, 30 Dec 2008) $
 */

include("../application.php");
require_login();

//Set the http variables
$userid = $_REQUEST["userid"];
$searchname = $_REQUEST["searchname"];

if(isset($userid)){
   $userHistoryResult = load_user_history($userid);
   $frm = load_user_profile($userid);

}
else{
     $wwwroot = $_SESSION["CFG"]["wwwroot"];
     header("Location:  $wwwroot/admin/player_lookup.php");
}

$DOC_TITLE = "Player History";
include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/player_history_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 *  Validate_form
 *****************************************************************************/



function validate_form(&$frm, &$errors) {
/* validate the forgot password form, and return the error messages in a string.
 * if the string is empty, then there are no errors */


        $errors = new Object;
        $msg = "";

        if (empty($frm["email"])) {
                $errors->email = true;
                $msg .= "<li>You did not specify your email address";

        } elseif (empty($frm["firstname"])) {
                $errors->firstname = true;
                $msg .= "<li>You did not specify your first name";

        } elseif (empty($frm["lastname"])) {
                $errors->lastname = true;
                $msg .= "<li>You did not specify your last name";

        //Not a required Feild
        //} elseif (empty($frm["homephone"])) {
        //        $errors->homephone = true;
        //        $msg .= "<li>You did not specify your home phone number";

        //Not a required Feild
        //} elseif (empty($frm["workphone"])) {
        //        $errors->workphone = true;
        //        $msg .= "<li>You did not specify your work phone number";


        } elseif (empty($frm["useraddress"])) {
                $errors->useraddress = true;
                $msg .= "<li>You did not specify your address";



        }

        return $msg;
}

/******************************************************************************
 * Update_settings
 *****************************************************************************/

function update_settings(&$frm) {
/* set the user's password to the new one */


         if(!$frm[userid]){

         $userid = get_userid();
         }

		//Update User
        $qid = db_query("
        UPDATE tblUsers SET
                 email = '$frm[email]'
                ,firstname = '$frm[firstname]'
                ,lastname = '$frm[lastname]'
                ,homephone = '$frm[homephone]'
                ,workphone = '$frm[workphone]'
                ,useraddress = '$frm[useraddress]'
        WHERE userid = '$userid'
        ");
        
        //Update Club Specific Settings
        $qid = db_query("
        UPDATE tblClubUser SET 
                ,recemail = '$frm[recemail]'
                ,msince = '$frm[msince]'
        WHERE userid = '$userid' AND clubid = ".get_clubid()."
        ");
        

   unset($userid);
   }

/******************************************************************************
 * Load User History
 Most of this was pulled from the my reservation page.
 *****************************************************************************/
function load_user_history($userid) {

$curresidquery = "SELECT reservations.reservationid
                  FROM tblUsers users, tblReservations reservations, tblkpUserReservations reservationdetails
                  WHERE users.userid = reservationdetails.userid
				  AND reservationdetails.userid=$userid
                  AND reservations.reservationid = reservationdetails.reservationid
                  AND reservations.usertype=0
				  AND users.enddate IS NULL
                  ORDER BY reservations.time DESC
                  LIMIT 25";

// run the query on the database
return db_query($curresidquery);


}

?>
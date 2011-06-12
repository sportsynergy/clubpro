<?

/*
 * $LastChangedRevision: 857 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:08:03 -0500 (Mon, 14 Mar 2011) $

*/

include("../application.php");
require_login();

//Set the http variables
$userid = $_REQUEST["userid"];
$searchname = $_REQUEST["searchname"];
$extraParametersResult = load_site_parameters();


if(isset($userid)){
   $frm = load_user_profile($userid);
   $registeredSports = load_registered_sports($userid);
}
else{
  	$wwwroot = $_SESSION["CFG"]["wwwroot"];
     header("Location:  $wwwroot/users/player_lookup.php");
}

$DOC_TITLE = "Player Info";

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/player_info_form.php");
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



?>
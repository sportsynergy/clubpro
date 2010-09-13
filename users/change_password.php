<?
/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $

*/

include("../application.php");
require_login();

/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $backtopage = $_SESSION["CFG"]["wwwroot"]."/users/change_password.php";


        if (empty($errormsg)){
              update_password($frm["newpassword"]);
              $noticemsg = "Your password was updated.<br/><br/>";
        }

        
}

$DOC_TITLE = "Change Password";
include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/form_header.php");
include($_SESSION["CFG"]["templatedir"]."/change_password_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the forgot password form, and return the error messages in a string.
 * if the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

        if (empty($frm["oldpassword"])) {
                $errors->oldpassword = true;
                $msg .= "You did not specify your old password";

        } elseif (! password_valid($frm["oldpassword"])) {
                $errors->oldpassword = true;
                $msg .= "Your old password is invalid";

        } elseif (empty($frm["newpassword"])) {
                $errors->newpassword = true;
                $msg .= "You did not specify your new password";

        } elseif (empty($frm["newpassword2"])) {
                $errors->newpassword2 = true;
                $msg .= "You did not confirm your new password";

        } elseif ($frm["newpassword"] != $frm["newpassword2"]) {
                $errors->newpassword = true;
                $errors->newpassword2 = true;
                $msg .= "Your new passwords do not match";
        }

        return $msg;
}

function password_valid($password) {
/* return true if the user's password is valid */


        $username = $_SESSION["user"]["username"];
        $password = md5($password);

        $qid = db_query("SELECT 1 FROM tblUsers WHERE username = '$username' AND password = '$password' AND tblUsers.enddate IS NULL");
        return db_num_rows($qid);
}

function update_password($newpassword) {
/* set the user's password to the new one */


        $username = $_SESSION["user"]["username"];
        $newpassword = md5($newpassword);

        $qid = db_query("UPDATE tblUsers SET password = '$newpassword' WHERE username = '$username'");
}

?>
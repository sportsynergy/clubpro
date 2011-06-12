<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/

include("../application.php");
$DOC_TITLE = "Password Recovery";


/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $backtopage = $_SESSION["CFG"]["wwwroot"]."/users/forgot_password.php";

        if ( empty( $errormsg)) {
             
                $username = get_username($_POST["email"]);
                reset_user_password($username);
                include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
                include($_SESSION["CFG"]["includedir"]."/include_fogpwsuc.php");
                include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
                die;
        }
}
include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/forgot_password_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

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

        } elseif (! email_exists($frm["email"])) {
                $errors->email = true;
                $msg .= "The specified email address is not on file";
        }

        return $msg;
}

function get_username($email) {
/* get the username based on an email address */

        $qid = db_query("SELECT username FROM tblUsers WHERE email = '$email'");
        $user = db_fetch_object($qid);

        return $user->username;
}

?>
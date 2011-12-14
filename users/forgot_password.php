<?

include("../application.php");
require($_SESSION["CFG"]["libdir"]."/postageapplib.php");

$DOC_TITLE = "Password Recovery";


/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submitme'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $backtopage = $_SESSION["CFG"]["wwwroot"]."/users/forgot_password.php";

        if ( empty( $errormsg)) {
             
                $userid = getUserIdFromEmail($_POST["email"]);
                reset_user_password($userid);
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

function getUserIdFromEmail($email) {
/* get the username based on an email address */

        $qid = db_query("SELECT users.userid 
        					FROM tblUsers users, tblClubUser clubuser
        					WHERE users.email = '$email' 
        					AND users.userid = clubuser.userid
        					AND clubuser.clubid = ".get_clubid());
        
        $user = db_fetch_object($qid);

        return $user->userid;
}

/**
 * Reset the users password
 * 
 * @param $userid
 */
function reset_user_password($userid) {
	/* resets the password for the user with the username $username, and sends it
	 * to him/her via email */

	if(isDebugEnabled(1) ) logMessage("applicationlib: reset_user_password for userid: $userid");
	

	/* load up the user record */
	$qid = db_query("SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$userid'");
	$user = db_fetch_object($qid);

	/* reset the password */
	$newpassword = generate_password();
	
	if(isDebugEnabled(1) ) logMessage("applicationlib: reset_user_password the new password will be: $newpassword");
	
	$qid = db_query("UPDATE tblUsers SET password = '" . md5($newpassword) . "' WHERE userid = '$user->userid'");

	/* email the user with the new account information */
	$var = new Object;
	$var->support = $_SESSION["CFG"]["support"];

	$subject = "Sportsynergy Account Information";
	$to_email = $user->email;
	$to_name = $user->firstname." ".$user->lastname;
	$from_email = "PlayerMailer@sportsynergy.net";
	$content = new Object;
	$content->line1 = "Your password at Sportsynergy has been reset, your username is $user->username and your new password is $newpassword.";
	$content->line2 = "It is highly recommended that you log into Sportsynergy and change your password as soon as possible.  Thank you for using Sportsynergy.  If you have any questions or concerns, please contact us at $var->support.";
	$content->clubname = get_clubname();
	$content->to_firstname = $user->firstname;
	
	$template = get_sitecode();
	
	
	send_email($subject, $to_email, $to_name,$from_email, $content, $template);
	//mail("$var->fullname <$user->email>", "Sportsynergy Account Information", $emailbody, "From: $var->support", "-fPlayerMailer@sportsynergy.com");
}

/* returns a randomly generated password of length $maxlen.  inspired by
	 * http://www.phpbuilder.com/columns/jesus19990502.php3 */
function generate_password() {

	$maxlen = 10;
	
	if(isDebugEnabled(1) ) logMessage("forgot_password: generate_passowrd: ".$_SESSION["CFG"]["wordlist"]);
	
	$fillers = "1234567890!@#$%&*-_=+^";
	$wordlist = file($_SESSION["CFG"]["wordlist"]);

	srand((double) microtime() * 1000000);
	$word1 = trim($wordlist[rand(0, count($wordlist) - 1)]);
	$word2 = trim($wordlist[rand(0, count($wordlist) - 1)]);
	$filler1 = $fillers[rand(0, strlen($fillers) - 1)];

	return substr($word1 . $filler1 . $word2, 0, $maxlen);
}

?>
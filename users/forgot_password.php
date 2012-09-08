<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */
/**
* Class and Function List:
* Function list:
* - validate_form()
* - getUserIdFromEmail()
* - reset_user_password()
* - generate_password()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/sendgrid-php/SendGrid_loader.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

$DOC_TITLE = "Password Recovery";

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    $backtopage = $_SESSION["CFG"]["wwwroot"] . "/users/forgot_password.php";
    
    if (empty($errormsg)) {
        $userid = getUserIdFromEmail($_POST["email"]);
        reset_user_password($userid);
        include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
        include ($_SESSION["CFG"]["includedir"] . "/include_fogpwsuc.php");
        include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
        die;
    }
}
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/forgot_password_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

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
        $msg.= "You did not specify your email address";
    } elseif (!email_exists($frm["email"])) {
        $errors->email = true;
        $msg.= "The specified email address is not on file";
    }
    return $msg;
}
function getUserIdFromEmail($email) {

    /* get the username based on an email address */
    $qid = db_query("SELECT users.userid 
        					FROM tblUsers users, tblClubUser clubuser
        					WHERE users.email = '$email' 
        					AND users.userid = clubuser.userid
        					AND clubuser.clubid = " . get_clubid());
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
    
    if (isDebugEnabled(1)) logMessage("applicationlib: reset_user_password for userid: $userid");

    /* load up the user record */
    $qid = db_query("SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$userid'");
    $user = db_fetch_object($qid);

    /* reset the password */
    $newpassword = generate_password();
    
    if (isDebugEnabled(1)) logMessage("applicationlib: reset_user_password the new password will be: $newpassword");
    $qid = db_query("UPDATE tblUsers SET password = '" . md5($newpassword) . "' WHERE userid = '$user->userid'");

    /* email the user with the new account information */
    $var = new Object;
    $var->username = $user->username;
    $var->support = $_SESSION["CFG"]["support"];
    $var->newpassword = $newpassword;
    $emailbody = read_template($_SESSION["CFG"]["templatedir"] . "/email/reset_password.php", $var);
    $emailbody = nl2br($emailbody);
    $subject = "Sportsynergy Account Information";
    $to_email = array(
        $user->email => array(
            'name' => $user->firstname
        )
    );
    $to_name = $user->firstname . " " . $user->lastname;
    $from_email = "Sportsynergy <player.mailer@sportsynergy.net>";
    $content = new Object;
    $content->line1 = $emailbody;
    $content->clubname = get_clubname();
    $template = get_sitecode();
    sendgrid_email($subject, $to_email, $content, "Forgot Password");
}

/* returns a randomly generated password of length $maxlen.  inspired by
 * http://www.phpbuilder.com/columns/jesus19990502.php3 */
function generate_password() {
    $maxlen = 10;
    
    if (isDebugEnabled(1)) logMessage("forgot_password: generate_passowrd: " . $_SESSION["CFG"]["wordlist"]);
    $fillers = "1234567890!@#$%&*-_=+^";
    $wordlist = file($_SESSION["CFG"]["wordlist"]);
    srand((double)microtime() * 1000000);
    $word1 = trim($wordlist[rand(0, count($wordlist) - 1) ]);
    $word2 = trim($wordlist[rand(0, count($wordlist) - 1) ]);
    $filler1 = $fillers[rand(0, strlen($fillers) - 1) ];
    return substr($word1 . $filler1 . $word2, 0, $maxlen);
}
?>
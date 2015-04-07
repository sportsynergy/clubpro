<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
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
* - send_message()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/sendgrid-php/SendGrid_loader.php");
$DOC_TITLE = "Player Mailer";
require_loginwq();
require_priv("2");

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    
    if (empty($errormsg)) {
        send_message($frm['subject'], $frm['message'], get_siteid() , $frm['email_address'] );
        
            $noticemsg = " Message sent to 1 person";
            unset($frm);
       
    }
}

include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_mailer_test_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new Object;
    $msg = "";
    
    if (empty($frm["subject"])) {
        $errors->subject = true;
        $msg.= "You did not specify a Subject";
    } elseif (empty($frm["message"])) {
        $errors->message = true;
        $msg.= "You did not specify a message";
    }
    return $msg;
}
/**
 * Sends the email to everyone
 */
function send_message($subject, $message, $siteid, $recipient) {

    // Strip Slashes
    if (get_magic_quotes_gpc()) {
        $message = stripslashes($message);
        $subject = stripslashes($subject);
    }
    $message = nl2br($message);
    
    if (isDebugEnabled(1)) logMessage("playerMailer(test).send_message(): \n subject: $subject\n message: $message\n siteid: $siteid \n");


    // run the query on the database

    $clubadminquery = "SELECT tblUsers.email
                           FROM tblUsers
                           WHERE tblUsers.userid=" . get_userid() . "";

    // run the query on the database
    $clubadminresult = db_query($clubadminquery);
    $clubadminval = mysql_result($clubadminresult, 0);
    
    $to_emails = array();
    $to_emails[$recipient] = array('name' => 'Mr. Test Email');

    $from_email = $clubadminval;
    $content = new Object;
    $content->line1 = $message;
    $content->clubname = get_clubname();
    $template = get_sitecode() . "-blank";

    //Send the email
    sendgrid_clubmail($subject, $to_emails, $content, "Club Email");
    return ;
}
?>
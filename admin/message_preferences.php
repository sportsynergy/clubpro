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
        $backtopage = "$wwwroot/admin/message_preferences.php";

		if ($errormsg){
             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
        }
        
        update_clubprefs($frm);
        $noticemsg = "Preferences Saved.  Good Job!<br/><br/>";
            
}


$frm = load_club_profile();
include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/form_header.php");
include($_SESSION["CFG"]["templatedir"]."/message_preferences_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* Just make sure that if they turn this little bugger on that they type in a message */

        $errors = new Object;
        $msg = "";

        if ($frm["messagedisplay"]=="on" && empty($frm["Messagetextarea"])) {
                $errors->Messagetextarea = true;
                $msg .= "You are attempting to turn the message display on with no message.  Turn off the display message or type in a message.";
         }
        elseif(empty($frm["Messagetextarea"])){
                $errors->Messagetextarea = true;
                $msg .= "You didn't specifiy a message.";
        }
		elseif( eregi("'",$frm["Messagetextarea"])){
			 $errors->Messagetextarea = true;
                $msg .= "Please don't use apostrophes, I beg of you.";
		}
		elseif( eregi("\n",$frm["Messagetextarea"])){
			 $errors->Messagetextarea = true;
                $msg .= "This time don't put a return in the message";
		}


        return $msg;
}

/************************************************************************************************************************/
/*
     This is used by the club preferences pages to load club preference information
*/

function load_club_profile() {
/* load up the user's details */

        //if userid exists then the club administrator is updating a users account


        $qid = db_query("SELECT messages.message, messages.enable
                         FROM tblMessages messages
                         WHERE messages.siteid = ".get_siteid()."");




        return db_fetch_array($qid);

   }

function update_clubprefs(&$frm) {
/* add the new user into the database */
         if($frm["messagedisplay"]=="on"){
             $displaymessage = 1;
         }
         else{
             $displaymessage = 0;
         }
         //Check to see if club has a message
         $qid = db_query("SELECT message, enable FROM tblMessages WHERE siteid = ".get_siteid()."");
         $numrows = mysql_num_rows($qid);

         if($numrows==0){
         $query = "INSERT INTO tblMessages (
                   siteid, message, enable
                   ) VALUES (
                   '".get_siteid()."'
                   ,'$frm[Messagetextarea]'
                   ,'$displaymessage')";
         }
         elseif($numrows==1){
        $query = "Update tblMessages SET
                message = '$frm[Messagetextarea]'
                ,enable = '$displaymessage'
                WHERE siteid = '".get_siteid()."'";
         }


        // run the query on the database
        $result = db_query($query);



}

?>
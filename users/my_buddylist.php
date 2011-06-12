<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/

include("../application.php");
require($_SESSION["CFG"]["libdir"]."/reservationlib.php");
require_login();

/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
   
        if ( empty($errormsg) ) {
                
               	if( isDebugEnabled(1) ) logMessage("my_buddylist: inserting buddy");
           		 insert_buddy($frm);
                
        }else{
        	if( isDebugEnabled(1) ) logMessage("my_buddylist: there was a problem adding the buddy becuase of $errormsg");
        }

        

}
if( isDebugEnabled(1) ) logMessage("my_buddylist");

$DOC_TITLE = "My Buddy List";
include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/my_buddylist_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {
/* validate the forgot password form, and return the error messages in a string.
 * if the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

        if (empty($frm["buddy"])) {
                $errors->buddy = true;
                $msg .= "You did not specify a buddy.";

        }
        else if(isABuddyOfMine($frm["buddy"])){
             $errors->buddy = true;
             $msg .= "I am sorry but this person is already a buddy.";
        }

        return $msg;
}


 function insert_buddy(&$frm) {
/* add the new user into the database */

		if( isDebugEnabled(1) ) logMessage("my_buddylist: insert_buddy".$frm['buddy']." for user ".get_userid());

        $query = "INSERT INTO tblBuddies (
                userid, buddyid
                ) VALUES (
                          '" .get_userid()."'
                          ,'$frm[buddy]')";



        // run the query on the database
        $result = db_query($query);

 }
?>
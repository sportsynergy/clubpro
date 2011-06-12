<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */
 

include("../application.php");
require_login();
require_priv("2");


/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        

        if ( empty($errormsg) && empty($action) ){
             insert_box($frm);
             
        }


}

$DOC_TITLE = "Box League Setup";

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/web_ladder_registration_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

        if (empty($frm["boxname"])) {
                $errors->boxname = true;
                $msg .= "You did not specify a box name ";

        } elseif (empty($frm["courttypeid"])) {
                $errors->courttypeid = true;
                $msg .= "You did not specify a court type.";

        }

         return $msg;

 }

 function insert_box(&$frm) {
/* add the new user into the database */

       $clubquery = "SELECT timezone from tblClubs WHERE clubid=".get_clubid()."";
       $clubresult = db_query($clubquery);
       $clubobj = db_fetch_object($clubresult);
       $tzdelta = $clubobj->timezone*3600;

       //Pad Zeros where necessary
       $daystring = "$frm[enddateday]";
       $monthstring =  "$frm[enddatemonth]";

       if($frm['enddateday']<10){
           $daystring = "0$frm[enddateday]";
       }

       if($frm['enddatemonth']<10){
          $monthstring =  "0$frm[enddatemonth]";
       }


       $datestring = "$frm[enddateyear]$monthstring$daystring";
       $timestamp = gmmktime(0,0,0,$frm['enddatemonth'],$frm['enddateday'],$frm['enddateyear']);

        //Get the number of Boxes at the club
          $useridquery = "SELECT boxrank
                          FROM tblBoxLeagues
                          WHERE siteid=".get_siteid()."";

        $useridresult = db_query($useridquery);
        $numberofrows = mysql_num_rows($useridresult);

        $lastboxrank =   $numberofrows + 1;

        $query = "INSERT INTO tblBoxLeagues (
                boxname, siteid, courttypeid, boxrank, enddate, enddatestamp
                ) VALUES (
                          '$frm[boxname]'
                          ,'" .get_siteid()."'
                          ,$frm[courttypeid]
                          ,$lastboxrank
                          ,$datestring
                          ,$timestamp)";


        // run the query on the database
        $result = db_query($query);
 }
?>



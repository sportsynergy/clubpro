<?

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */
 

include("../application.php");
require_login();

//Set the http variables
$action = $_REQUEST["action"];
$boxid = $_REQUEST["boxid"];


/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm);
        $backtopage = $_SESSION["CFG"]["wwwroot"]."/admin/web_ladder_manage.php?boxid=$boxid";

        if ($errormsg){

             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
        }

        if(empty($action))
        {
            insert_boxuser($frm);
        }

}

//Get the name of the box
if($boxid)
{
          $boxnamequery = "SELECT boxleague.boxname, boxleague.courttypeid
                           FROM tblBoxLeagues boxleague
                           WHERE boxid=$boxid";

          // run the query on the database
          $result = db_query($boxnamequery);
		  $boxarray = mysql_fetch_array($result);
          
          //Set some variables for the form
          $DOC_TITLE = "Box League - $boxarray[0] ";
		  $courttype = $boxarray[1];

		  include($_SESSION["CFG"]["templatedir"]."/header.php");
		  include($_SESSION["CFG"]["templatedir"]."/web_ladder_manage_form.php");
		  include($_SESSION["CFG"]["templatedir"]."/footer.php");

}

else {
    include($_SESSION["CFG"]["templatedir"]."/header.php");
    include($_SESSION["CFG"]["templatedir"]."/footer.php");
}
/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

 function validate_form($frm){

         // Make sure that the box user isn't already in a box

		if( isDebugEnabled(1) ) logMessage("web_ladder_manage.validate_form");
		
         $errors = new Object;
         $msg = "";
         if ( empty($frm["boxuser"]) ) {
                
                if( isDebugEnabled(1) ) logMessage("\t-> boxuser is empty");
                
                $errors->boxuser = true;
                $msg .= "Please select a valid user.";

         }
         elseif(!empty($frm['boxuser'])){
              $boxUserQuery = "SELECT userid from tblkpBoxLeagues WHERE userid = $frm[boxuser]";
              $boxUserResult = db_query($boxUserQuery);

              if (mysql_num_rows($boxUserResult)>0) {
                    
                     if( isDebugEnabled(1) ) logMessage("\t-> boxuser is not in a box");
                     $errors->boxuser = true;
                     $msg .= "A player cannot be in more that one box at a time.";

              }
          }

        return $msg;


 }

 function insert_boxuser(&$frm) {
// First thing we need to do is find out how many players are
// in the league.  This will determine what place the user will start with.

// Build Query
      $boxcountquery = "SELECT boxid
                        FROM tblkpBoxLeagues where boxid=$frm[boxid]";

// run the query on the database
        $boxcountresult = db_query($boxcountquery);
        $boxcountval = mysql_num_rows($boxcountresult);

        $boxcountval = $boxcountval + 1;

/* add the new user into the database */

        $query = "INSERT INTO tblkpBoxLeagues (
                boxid, userid, boxplace
                ) VALUES (
                           '$frm[boxid]'
                          ,'$frm[boxuser]'
                          , $boxcountval)";


        // run the query on the database
        $result = db_query($query);
 }
?>





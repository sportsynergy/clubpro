<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */

include("../application.php");
$DOC_TITLE = "User Registration";
require_loginwq();
require_priv("2");



$availbleSports = load_avail_sports();
$availableSites = load_avail_sites();

/* form has been submitted, try to create the new user account */
if (match_referer() && isset($_POST['submit'])) {

        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);


         if (empty($errormsg)){
             insert_user($frm, $availbleSports,$availableSites);
             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/include_userregsuc.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
        }


}

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/form_header.php");
include($_SESSION["CFG"]["templatedir"]."/user_registration_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");


/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

		if(isSiteAutoLogin() && empty($frm["memberid"])){
			$errors->memberid = true;
			$msg .= "You did not specify a member id";
		}
		 elseif (username_exists($frm["username"]) && !isSiteAutoLogin() ) {
                $errors->username = true;
                $msg .= "The username <b>" . ov($frm["username"]) ."</b> already exists";
        } elseif( empty($frm["username"]) && !isSiteAutoLogin()){
        	 $errors->username = true;
        	$msg .= "You did not specify a username";
        	
        } elseif (empty($frm["password"]) && !isSiteAutoLogin()) {
                $errors->password = true;
                $msg .= "You did not specify a password";

        } elseif (empty($frm["firstname"])) {
                $errors->firstname = true;
                $msg .= "You did not specify a first name";

        } elseif (empty($frm["lastname"])) {
                $errors->lastname = true;
                $msg .= "You did not specify a last name";

        } elseif ( !empty($frm["email"]) && !is_email_valid($frm["email"])) {
                $errors->email = true;
                $msg .= "Please enter a valid email address";
        }
        

        return $msg;
}

function insert_user(&$frm, $availbleSports,$availableSites) {


/* add the new user into the database */


		if(isSiteAutoLogin()){
			
			$sitePasswordQuery = "SELECT sites.password FROM tblClubSites sites WHERE sites.siteid = ".get_siteid()."";
			$sitePasswordResult = db_query($sitePasswordQuery);
			$password = mysql_result($sitePasswordResult,0);
			$username = $frm['memberid'];
		}
		else{
			$password = md5($frm["password"]);
			$username = $frm['username'];
		}


		//If email exists already, just add the club authorization
		if( !empty($frm["email"]) && email_exists($frm["email"]) ){
					
			$emailIdQuery = "SELECT users.userid from tblUsers users WHERE users.email = '$frm[email]' ";
			$emailIdResult = db_query($emailIdQuery);
			$userid = mysql_result($emailIdResult, 0);
			
			//Insert the Club User (for the new club)
			 $clubUserQuery = "INSERT INTO tblClubUser (
	                userid, clubid, msince, roleid, memberid
	                ) VALUES (
	                          $userid
							  ,".get_clubid()."
	                          ,'$frm[msince]'
	                          ,'$frm[usertype]'
	                          ,'$frm[memberid]'
	                          )";
			
			$clubUserResult = db_query($clubUserQuery);
			
		}
		else{
	        $query = "INSERT INTO tblUsers (
	                username, password, firstname, lastname, email, homephone, workphone, cellphone, pager, useraddress, gender
	                ) VALUES (
	                          '$username'
	                          ,'$password'
	                          ,'$frm[firstname]'
	                          ,'$frm[lastname]'
	                          ,'$frm[email]'
	                          ,'$frm[homephone]'
	                          ,'$frm[workphone]'
	                          ,'$frm[cellphone]'
	                          ,'$frm[pager]'
	                          ,'$frm[useraddress]'
	                          ,'$frm[gender]'
	                          )";
	
			    // run the query on the database.  Get the user that was just added.  Make sure to get the right one.  Usersnames only have
			    // to be unique within a club, but they can be there can be duplicates from club to club.  To mitigate the risk of adding 
			    // a club authoriation for the wrong id, match on the password, 
		        $result = db_query($query);
		        
		        $contactidquery = "SELECT userid FROM tblUsers WHERE username = '$username' 
										AND password = '$password'
										AND homephone = '$frm[homephone]'";
										
		        $contactidresult =  db_query($contactidquery);
		        $userid = mysql_result($contactidresult,0);
		        
		        //Insert the Club User (for the new club)
				 $clubUserQuery = "INSERT INTO tblClubUser (
		                userid, clubid, msince, roleid, memberid
		                ) VALUES (
		                          $userid
								  ,".get_clubid()."
		                          ,'$frm[msince]'
		                          ,'$frm[usertype]'
		                          ,'$frm[memberid]'
		                          )";
				
				$clubUserResult = db_query($clubUserQuery);

		
		}
		
        //Now set the sites
           for ($i=0; $i<mysql_num_rows($availbleSports); ++$i){

                 $courtTypeArray = mysql_fetch_array($availbleSports);
                 if($frm["courttype$courtTypeArray[courttypeid]"]){
                         $query = "INSERT INTO `tblUserRankings`
                                   (`userid` , `courttypeid` , `ranking` , `hot` , `usertype`  )
                                   VALUES ('$userid', '$courtTypeArray[courttypeid]', '".$frm["courttype$courtTypeArray[courttypeid]"]."', '0', '0')";
                         db_query($query);
                     }

           }


        //Now set the sites
       for ($i=0; $i<mysql_num_rows($availableSites); ++$i){
             $siteArray = mysql_fetch_array($availableSites);
             if($frm["clubsite$siteArray[siteid]"]){
                 mysql_query("INSERT INTO `tblkupSiteAuth` ( `userid` , `siteid` ) VALUES ($userid, ".$siteArray['siteid'].")");
             }

       }




}

?>
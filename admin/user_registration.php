<?

/*
 * $LastChangedRevision: 857 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:08:03 -0500 (Mon, 14 Mar 2011) $
 */

include("../application.php");
$DOC_TITLE = "User Registration";
require_loginwq();
require_priv("2");



$availbleSportsResult = load_avail_sports();
$availableSitesResult = load_avail_sites();
$extraParametersResult = load_site_parameters();

/* form has been submitted, try to create the new user account */
if (  isset($_POST['submit']) || isset($_POST['action']) ) {

        $frm = $_POST;
      
		$errormsg = validate_form($frm, $errors);
          	
         if( isDebugEnabled(1) ) logMessage("user_registration: adding account ");

         if (empty($errormsg)){

         	
         	 	
         	 	 insert_user($frm, $availbleSportsResult,$availableSitesResult,$extraParametersResult);
	             include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
	             include($_SESSION["CFG"]["includedir"]."/include_userregsuc.php");
	             include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
				die;
				
         
         	 }
		         	
		         	
		        
		        
 }




include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/user_registration_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");


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
        } elseif(!empty($frm["email"]) ){
        	
        	
        
	        if ( ! isEmailUniqueAtClub($frm["email"], get_clubid() ) ) {
	                $errors->email = true;
	                $msg .= "The email address <b>" . ov($frm["email"]) ."</b> already exists";
			}
        }
        
		
        
        
        

        return $msg;
}

function insert_user(&$frm, $availbleSports,$availableSites,$extraParametersResult ) {


/* add the new user into the database */
	if( isDebugEnabled(1) ) logMessage("user_registration.insert_user ");
	

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

		
		
		
        //Now set the rankings
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
                db_query("INSERT INTO `tblkupSiteAuth` ( `userid` , `siteid` ) VALUES ($userid, ".$siteArray['siteid'].")");
             }

       }

       // Finally add in the extra parameters
	   while ($parameterArray = mysql_fetch_array($extraParametersResult) ){
             
	   		 $parameterId = $parameterArray['parameterid'];

             if($frm["parameter-$parameterId"]){
             	if( isDebugEnabled(1) ) logMessage("user_registration.insert_user: adding custom parameter:  ".$frm["parameter-$parameterId"]);
             	$parameterValue = $frm["parameter-$parameterId"];
                $query = "INSERT INTO `tblParameterValue` ( `userid`, `parameterid`, `parametervalue` ) VALUES ('$userid', '$parameterId','$parameterValue')";
       			db_query($query);
             }

       }
       
       
}

/**
 * Make sure that nobody else has this same email address...
 * @param $frm
 * @param $errors
 */
function validate_email(&$frm, &$errors){
	
		if( isDebugEnabled(1) ) logMessage("change_settings.update_settings: validate_email is not found in any other clubs ". $frm["email"]);

		if (!empty($frm["email"]) ) {
			 return verifyEmailUniqueOutsideClub($frm["email"],$frm["userid"], get_clubid() );
		} 
		
		return;
}



?>
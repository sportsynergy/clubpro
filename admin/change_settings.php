<?

/*
 * $LastChangedRevision: 858 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:29:16 -0500 (Mon, 14 Mar 2011) $
 */

include("../application.php");
require_login();
require_priv("2");

//Set the http variables
$userid = $_REQUEST["userid"];
$searchname = $_REQUEST["searchname"];


if(!isset($userid)){
  $userid = get_userid();
}


$DOC_TITLE = "Player Administration";



/* form has been submitted, check if it the user login information is correct */
if ( isset($_POST['submit']) || isset($_POST['action'])) {
        
		 $frm = $_POST;
		 
         // Do a special check for duplicate email addresses.
          if(isset($userid)){
             $useridstring = "?userid=$userid";
          }
          $wwwroot = $_SESSION["CFG"]["wwwroot"];
          $backtopage = "$wwwroot/admin/change_settings.php$useridstring";

          // Check to see if the user had tried to save a duplicate email address and chosen to 
          // merge the accounts
          if( $frm["action"] == "mergeaccounts" ){

			
			//Set the new userid
			$query = "SELECT userid from tblClubUser WHERE id = ".$frm['other_userid'];
			$result = db_query($query);
			$otherUserId = mysql_result($result,0);
			
			//Merge these two accounts (create a club user for the current club, for that user)
			mergeAccounts($userid, $otherUserId);
			
			
			$userid = $otherUserId;
                                   
          	//Display the message
          	$noticemsg = "Profile was merged.  Good Job!<br/><br/>";
          	
          }
          // Save the user
          else{
          	
          		$errormsg = validate_form($frm, $errors);
        
				if (empty($errormsg)){
	                update_settings($frm,$availableSites, $availbleSports, $extraParametersResult);
					
	                $registeredSports = load_registered_sports($userid);
	                $authSites = load_auth_sites($userid);
	                $availbleSports = load_avail_sports();
	                $availableSites = load_avail_sites();
	                
	                if( mysql_num_rows($extraParametersResult) > 0 ){
	                	mysql_data_seek($extraParametersResult, 0);
	                }
	                
				}
				
				if( isDebugEnabled(1) ) logMessage("change_settings.submit: checking to see if email exists outside of club ". $frm["email"]);
				$otherClubUser = validate_email($frm["email"], $frm["userid"]);
				
				if(  isset($otherClubUser ) ){
					
					$clubNameQuery = "SELECT clubs.clubname FROM tblClubs clubs, tblClubUser clubuser WHERE clubs.clubid = clubuser.clubid AND clubuser.id = $otherClubUser";
					$otherClubResult = db_query($clubNameQuery);
					$otherClub = mysql_result($otherClubResult,0);
					
					include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
					include($_SESSION["CFG"]["templatedir"]."/player_merge_form.php");
					include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
					die;
				}
				else{
					
					//Save email address
					$query = "UPDATE tblUsers set email = '".$frm['email']."' where userid = ".$frm['userid'];
					db_query($query);
					
					// Display this, their email validates
					$noticemsg = "Your profile was saved.  Good Job!<br/><br/>";
				}
			
          }
			

} 


// Load up data for view
$registeredSports = load_registered_sports($userid);
$authSites = load_auth_sites($userid);
$availbleSports = load_avail_sports();
$availableSites = load_avail_sites();
$extraParametersResult = load_site_parameters();

$frm = load_user_profile($userid);

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/change_settings_admin_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/



function validate_form(&$frm, &$errors) {
/* validate the forgot password form, and return the error messages in a string.
 * if the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";
		
		// Make sure that the login id is set
		if(isSiteAutoLogin() && empty($frm["memberid"])){
			$errors->memberid = true;
			$msg .= "You did not specify a member id";
		} 
		elseif( !isSiteAutoLogin() && empty($frm["username"])  ){
        	 $errors->username = true;
        	$msg .= "You did not specify a username";
		} 
		elseif (!isSiteAutoLogin() && username_already_exists($frm["username"], $frm["userid"]) ) {
                $errors->username = true;
                $msg .= "The username <b>" . ov($frm["username"]) ."</b> already exists";
        }  
        elseif (!empty($frm["email"]) && !is_email_valid($frm["email"])) {
                $errors->email = true;
                $msg .= "Please enter a valid email address";
        }
        
        elseif (empty($frm["firstname"])) {
                $errors->firstname = true;
                $msg .= "You did not specify a first name";

        } elseif (empty($frm["lastname"])) {
                $errors->lastname = true;
                $msg .= "You did not specify a last name";
                
        } elseif ( !empty($frm["email"]) ) {
                
        	$otherUser = verifyEmailUniqueAtClub($frm["email"], $frm["userid"], get_clubid() );
        	
        	if( isset($otherUser) ){
        		$errors->email = true;
                $msg .= "The email address <b>" . ov($frm["email"]) ."</b> already exists";
        	}
        	
        } 
        

        return $msg;
}

/**
 * 
 * @param $email
 * @param $userid
 */
function validate_email($email, $userid){
	
		if( isDebugEnabled(1) ) logMessage("change_settings.update_settings: validate_email ". $email);

		if (!empty($email) ) {
			 return verifyEmailUniqueOutsideClub($email,$userid, get_clubid());
		} 
		
		return;
}

/**
 * 
 * @param $frm
 * @param $availableSites
 * @param $availbleSports
 * @param $extraParametersResult
 */
function update_settings(&$frm, $availableSites, $availbleSports, $extraParametersResult) {


         //If userid is not set this is being run by a player who is updating
         //their own accoutn information.  If this is the case we will get the userid
         //out of the session.
         if(!$frm['userid']){

         $userid = get_userid();
         }
        else{
		
		$userid = $frm['userid'];
        $mycourtTypes = explode (",", $frm['mycourttypes']);
        $mySites = explode (",", $frm['mysites']);


                  //if the courttype post var is set we need to either update or insert depending
                 //on if it was set before.

                 //Now set the sites
                for ($i=0; $i<mysql_num_rows($availbleSports); ++$i){
                      $courtTypeArray = mysql_fetch_array($availbleSports);
                      if($frm["courttype$courtTypeArray[courttypeid]"]){

                          if(in_array($courtTypeArray['courttypeid'],$mycourtTypes)){
                             mysql_query("UPDATE `tblUserRankings` SET ranking = '".$frm["courttype$courtTypeArray[courttypeid]"]."' WHERE courttypeid='$courtTypeArray[courttypeid]' AND userid ='$userid' AND usertype ='0'");

                          }
                          else{
                               $query = "INSERT INTO `tblUserRankings`
                                        (`userid` , `courttypeid` , `ranking` , `hot` , `usertype`  )
                                        VALUES ('$userid', '$courtTypeArray[courttypeid]', '".$frm["courttype$courtTypeArray[courttypeid]"]."', '0', '0')";

                                   db_query($query);
                          }


                      }
                      //if courttype post var is not set and it was set before we will delete it.
                      else{
                           mysql_query("DELETE from `tblUserRankings`WHERE userid=$userid AND courttypeid='$courtTypeArray[courttypeid]' AND usertype=0");
                      }

                }


                 //Now set the sites
                for ($i=0; $i<mysql_num_rows($availableSites); ++$i){
                      $siteArray = mysql_fetch_array($availableSites);
                      if($frm["clubsite$siteArray[siteid]"]){



                          //If the site isn't in the list insert it
                          if(!in_array($siteArray['siteid'],$mySites)){

                          $addSiteQuery = "INSERT INTO `tblkupSiteAuth` ( `userid` , `siteid` ) VALUES ($userid, ".$siteArray['siteid'].")";
                           db_query($addSiteQuery);
                          }


                      }
                      //if siteid post var is not set and it was set before we will delete it.
                      else{
                        mysql_query("DELETE from `tblkupSiteAuth `WHERE userid=$userid AND siteid=".$siteArray['siteid']."");
                      }

                }
        }

		//Set the enable variable
		if( isset($frm['enable'])){
			$enable= 'y';
		}
		else{
			$enable= 'n';
		}
	
		
		if(isSiteAutoLogin()){
			$username = $frm['memberid'];
		}
		else{
			$username = $frm['username'];
		}
	

		$updateUserQuery = "
        UPDATE tblUsers SET
				username = '$username'
                ,email = '$frm[email]'
                ,firstname = '$frm[firstname]'
                ,lastname = '$frm[lastname]'
                ,homephone = '$frm[homephone]'
                ,workphone = '$frm[workphone]'
                ,cellphone = '$frm[cellphone]'
                ,pager = '$frm[pager]'
                ,useraddress = '$frm[useraddress]'
		        ,gender = '$frm[gender]'
        WHERE userid = '$userid'";
    
        $qid = db_query($updateUserQuery);
        
        
        $updateClubUserQuery = "
        UPDATE tblClubUser SET
                recemail = '$frm[recemail]'
                ,enable = '$enable'
                ,memberid = '$frm[memberid]'
				,roleid 	  =  '$frm[roleid]'
        WHERE userid = '$userid'";
        
        $qid = db_query($updateClubUserQuery);


	//Set the password
	if(!empty($frm["password"])){
		
		$updateUserQuery = "UPDATE tblUsers SET password = '" . md5($frm["password"]) ."' WHERE userid = '$userid'";
		$qid = db_query($updateUserQuery);
	}
	
 		// Update the Custom Parameters
 		while ($parameterArray = mysql_fetch_array($extraParametersResult) ){
             
	   		 $parameterId = $parameterArray['parameterid'];
	   		 
             if($frm["parameter-$parameterId"]){
             	if( isDebugEnabled(1) ) logMessage("(admin)change_settings.update_settings: adding custom parameter:  ".$frm["parameter-$parameterId"]);
             	$parameterValue = $frm["parameter-$parameterId"];
                $query = "INSERT INTO tblParameterValue (userid,parameterid,parametervalue) 
                			VALUES ('$userid', '$parameterId','$parameterValue') 
                			ON DUPLICATE KEY UPDATE parametervalue = '$parameterValue'";
       			db_query($query);
       			
             }

       }
       

   unset($userid);
   }

   
   /**
    * 
    * This function does a replacement of one user with another user from another club
    * 
    * Steps:
    * 	- End date the first user
    * 	- Create a clubuser record for the second user
    *  	- Copy the first users buddyes to the second user
    *  	- Copy the first users authorizations to the second user
    *  
    *  
    * @param $olderUserId
    * @param $newUserId
    */
   function mergeAccounts($oldUserId, $newUserId){
   	
   			// end date the current club user account for this club
          	if( isDebugEnabled(1) ) logMessage("change_settings.mergeaccounts:enddating: $oldUserId");
          	
          	$mergeClubUserQuery = "SELECT msince, roleid, memberid, userid FROM tblClubUser where userid = '$oldUserId' AND clubid = ".get_clubid()." and enddate is null";
          	$mergeClubUserResult = db_query($mergeClubUserQuery);
          	$mergeClubUserArray = mysql_fetch_array($mergeClubUserResult);
          	
          	$oldClubUserAcountQuery = "UPDATE tblClubUser set enddate = NOW() WHERE clubid = ".get_clubid()."
          								AND userid = $oldUserId";
          	db_query($oldClubUserAcountQuery);
          	
          	
          	
          	// create a club user entry for the other_user with the current club
          	//Insert the Club User (for the new club)
			 $clubUserQuery = "INSERT INTO tblClubUser (
	                userid, clubid, msince, roleid, memberid
	                ) VALUES (
	                         $newUserId
							  ,".get_clubid()."
	                          ,'$mergeClubUserArray[msince]'
	                          ,'$mergeClubUserArray[roleid]'
	                          ,'$mergeClubUserArray[memberid]'
	                          )";
			
			$clubUserResult = db_query($clubUserQuery);
			
			// Copy buddies
			$buddyQuery = "SELECT buddyid FROM tblBuddies WHERE userid = $oldUserId";
			$buddyResult = db_query($buddyQuery);
			
			// Go through all of the buddies and copy them over
			while( $buddyArray = mysql_fetch_array($buddyResult) ){
				
				if( isDebugEnabled(1) ) logMessage("change_settings.merging buddy: ".$buddyArray[buddyid]);
				
			        $query = "INSERT INTO tblBuddies (
			                userid, buddyid
			                ) VALUES (
			                          '$newUserId'
			                          ,'$buddyArray[buddyid]')";
			
			        // run the query on the database
			        $result = db_query($query);
				
			}
			
			//Add the new player as a buddy from others
			if( isDebugEnabled(1) ) logMessage("change_settings.Add the new player as a buddy from others");
			$buddyquery = "SELECT userid from tblBuddies WHERE buddyid = $oldUserId";
			$buddyresult = db_query($buddyquery);
			
			while( $buddyUserArray = mysql_fetch_array($buddyresult) ){
				if( isDebugEnabled(1) ) logMessage("change_settings.updating people who had $oldUserId as their buddy");
				
				  $query1 = "INSERT INTO tblBuddies (
			                userid, buddyid
			                ) VALUES (
			                          '$buddyUserArray[userid]'
			                          ,'$newUserId')";
			
			        // run the query on the database
			        $result1 = db_query($query1);
				
			}
			
			
			// Copy authorizations
			$siteAuthorizationQuery = "SELECT siteid FROM tblkupSiteAuth WHERE userid = $oldUserId";
			$siteAuthorizationResult = db_query($siteAuthorizationQuery);
			
			while($siteAuthArray = mysql_fetch_array($siteAuthorizationResult) ){
				if( isDebugEnabled(1) ) logMessage("change_settings.merging siteauths: ".$siteAuthArray[siteid]);
				 $query = "INSERT INTO tblkupSiteAuth (
			                userid, siteid
			                ) VALUES (
			                          '$newUserId'
			                          ,'$siteAuthArray[siteid]')";
			
			        // run the query on the database
			        $result = db_query($query);
			}
			
			
   }
?>
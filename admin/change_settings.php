<?

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $
 */

include("../application.php");
require_login();


//Set the http variables
$userid = $_REQUEST["userid"];
$searchname = $_REQUEST["searchname"];

if(!isset($userid)){
  $userid = get_userid();
}


$DOC_TITLE = "Player Administration";



$registeredSports = load_registered_sports($userid);
$authSites = load_auth_sites($userid);
$availbleSports = load_avail_sports();
$availableSites = load_avail_sites();


/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submit'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);

          if(isset($userid)){
             $useridstring = "?userid=$userid";
          }
          $wwwroot = $_SESSION["CFG"]["wwwroot"];
          $backtopage = "$wwwroot/admin/change_settings.php$useridstring";


			if (empty($errormsg)){
                update_settings($frm,$availableSites, $availbleSports);
				$noticemsg = "Your profile was saved.  Good Job!<br/><br/>";
                $registeredSports = load_registered_sports($userid);
                $authSites = load_auth_sites($userid);
                $availbleSports = load_avail_sports();
                $availableSites = load_avail_sites();
			}

} else {


        $frm = load_user_profile($userid);

}

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/form_header.php");
include($_SESSION["CFG"]["templatedir"]."/change_settings_admin_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

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
        elseif (!empty($frm["email"]) && !is_email_unique($frm["email"],$frm["userid"])) {
                $errors->email = true;
                $msg .= "The email address <b>" . ov($frm["email"]) ."</b> already exists";
		} 
        elseif (empty($frm["firstname"])) {
                $errors->firstname = true;
                $msg .= "You did not specify a first name";

        } elseif (empty($frm["lastname"])) {
                $errors->lastname = true;
                $msg .= "You did not specify a last name";
        } 
        

        return $msg;
}

function update_settings(&$frm, $availableSites, $availbleSports) {
/* set the user's password to the new one */

        

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

   unset($userid);
   }

?>
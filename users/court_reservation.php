<?
/*
 * $LastChangedRevision: 862 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-04-07 22:24:19 -0500 (Thu, 07 Apr 2011) $



/*
 A user can come into this page from a link in an email where the reservation has since changed
 sice the email was sent out.  This email is sent when players are looking for a match.  Here
 is an example of this kind of link
 http://localhost/clubpro/users/court_reservation.php?time=1285426800&courtid=5&user=2
  
 Now, the first time this link is clicked on the system will ask if they user wants to sign up for the 
 court.  The problem happens when that first user adds hisself to the reservation and a second players comes in
 later and clicks on the same link. So as to prevent the second persom from being able to signup for this court, 
 a little check needs to be made that makes sure that:
 
 1.) if this page is being loaded from the link in a players wanted email
 2.) The reservation has alredy has the maximum number or people in it
 
 then
 
 an error message will result notifing the user that the reservation is full.
 
*/




/*****************************************************************************
*
* Do some administrative things
* 
/*****************************************************************************/



include("../application.php");
require($_SESSION["CFG"]["libdir"]."/reservationlib.php");
require($_SESSION["CFG"]["libdir"]."/courtlib.php");

$DOC_TITLE = "Court Reservation";

//Set the http variables
$courtid = $_REQUEST["courtid"];
$time = $_REQUEST["time"];
$userid = $_REQUEST["userid"];
$courttype = $_REQUEST["courttype"];
$courtid = $_REQUEST["courtid"];

// In case the user is loading this page from a link on an email, 
// we have to load in the site preferences (normally this is done in
// the scheduler content.

if( !isset($_SESSION["siteprefs"]["clubid"]) ) {

	if( isDebugEnabled(1) ) logMessage("court_reservation: setting site preference for court $courtid");
	$siteprefs = getSitePreferencesForCourt($courtid);
	$_SESSION["siteprefs"] = $siteprefs;

}

require_loginwq();


/*****************************************************************************
 * 
 * Process Form Post
 * 
 ******************************************************************************/

if (match_referer() && isset($_POST['courttype'])) {

		// Set some variables 
        $frm = $_POST;
        $wwwroot = $_SESSION["CFG"]["wwwroot"];
        
        
        // Validate
        $errormsg = validate_form($frm, $errors);
		
		
		if ( empty($errormsg) ){
			  	
				if( isDebugEnabled(1) ) logMessage("Inserting the reservation");
			  	
				//Actually Make the Reservation
				if( $frm['action'] == "create" ){
					$resid = insert_reservation($frm);
				}else{
					$resid = update_reservation($frm);
				}
				
		

	            /**
	             * ReDirect the user to the reservation_details screen so they can advertise the match
	             */
	            if (   $frm['action']=="create" && $frm['playertwoid']=="" && $frm['matchtype']==1){
					   
	            	if( isDebugEnabled(1) ) logMessage("court_reservation: prompting for more details as matchtype is 1 and opponent is not set");
	                   
	            	$boxid = getBoxIdForUser(get_userid());
	                 header ("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time&boxid=$boxid");
	
	            }
	            elseif( $frm['action']=="create" && $frm['courttype']=="singles"
	            			&& ($frm['playeroneid']=="" || $frm['playertwoid']=="")
	            			&& !isGuestPlayer($frm['playeroneid'], $frm['playeronename']) 
	            			&& !isGuestPlayer($frm['playertwoid'], $frm['playertwoname'])  
	            			&& ($frm['matchtype']==0 || $frm['matchtype']==1 || $frm['matchtype']==2)
	            			
	            ){
	            	if( isDebugEnabled(1) ) logMessage("court_reservation: courttype is singles and opponent is empty");
	            	header ("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time");
	            }
	       
	            // If any spots are open for doubles
	            elseif( 
	            		// If any of the spots are empty
	            		$frm['courttype']=="doubles"
	            		&& $frm['action']=="create"
	            		&& ( !isGuestPlayer($frm['playeroneid'], $frm['playeronename']) && empty($frm['playeroneid'])
	            			|| !isGuestPlayer($frm['playertwoid'], $frm['playertwoname']) && empty($frm['playertwoid'])
	            			|| !isGuestPlayer($frm['playerthreeid'], $frm['playerthreename']) && empty($frm['playerthreeid'])
	            			|| !isGuestPlayer($frm['playerfourid'], $frm['playerfourname']) && empty($frm['playerfourid']) )
	            		 
	            		){
	            	 		if( isDebugEnabled(1) ) logMessage("Prompting for more details as this is a front desk doubles reservation and a name is empty");
	            			header ("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time");
	            }
	            else{
	                 header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ."");
	            }

	            
	            die;
	    }else{
	    	
	    	// This did not pass the form validation, set these variables for the same form
	    	$userid = $frm['userid'];

	    }

	    

}


 /******************************************************************************
 * 
 * Get the type of reservation.  This is used a lot.
 * 
 ******************************************************************************/
$newReservation = FALSE;

$userTypeQuery = "SELECT usertype, matchtype, guesttype, lastmodified, reservationid
					FROM tblReservations reservations
					WHERE reservations.time = $time 
					AND reservations.courtid = $courtid
					AND reservations.enddate is NULL";

$userTypeResult = db_query($userTypeQuery);

if( mysql_num_rows($userTypeResult) > 0) {
	
	$reservationArray = mysql_fetch_array($userTypeResult);
	$usertype = $reservationArray['usertype'];
	$matchtype = $reservationArray['matchtype'];
	$guesttype = $reservationArray['guesttype'];
	$lastupdated = $reservationArray['lastmodified'];
	$reservationid = $reservationArray['reservationid'];
	
	if( isDebugEnabled(1) ) logMessage("court_reservation: setting usertype: $usertype, matchtype: $matchtype, guesttype: $guesttype, and lastupdated:$lastupdated ");
	
}else{
	
	if( isDebugEnabled(1) ) logMessage("court_reservation: this is a new reservation ");
	
	$newReservation = TRUE;
}

	
 /******************************************************************************
 *  
 *  Run Form Load Validation, stuff to do before loading first form
 * 
 ******************************************************************************/
	

if(get_roleid()==1){

	  $courttypeid = getCourtTypeIdForCourtId($courtid);
		
      if( !amiValidForSite( get_siteid() ) || !isValidForCourtType( $courttypeid , get_userid() )){

      	   $errormsg = "Sorry, you are not authorized to reserve this court.";
           include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
           include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
           die;
      }

}

if(get_roleid()==5){
	
			$errormsg = "Sorry, you are not authorized to reserve this court.  Talk to the pro about getting set up to do this.";
           include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
           include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
           die;
           
}





/******************************************************************************
 * 
 * Load Forms
 * 
 ******************************************************************************/

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");

/**
 * Determine what form to display
 */

if($newReservation){
	include($_SESSION["CFG"]["templatedir"]."/reservation_form.php");
}
elseif( $usertype == 0  && isSinglesReservationNeedPlayers($time, $courtid) ) {
	
	if( $userid == get_userid() || get_roleid()==2 || get_roleid() ==4 ){
			include($_SESSION["CFG"]["includedir"]."/include_update_singles_form.php");
		}
	else{
		include($_SESSION["CFG"]["includedir"]."/include_signup_singles_form.php");
	}	
			

}elseif( $usertype == 1  && isDoublesReservationNeedPlayers($time, $courtid) ) {
	
	   $teamQuery = "SELECT reservationdetails.userid, reservationdetails.usertype
                        FROM tblReservations reservations, tblkpUserReservations reservationdetails
                        WHERE reservations.reservationid = reservationdetails.reservationid
                        AND reservations.time=$time
						AND reservations.courtid=$courtid
						AND reservations.enddate IS NULL
						ORDER BY reservationdetails.usertype DESC, reservationdetails.userid DESC";
        
        $teamResult = db_query($teamQuery);
		$teamRow = mysql_fetch_array($teamResult);
		$player1userId = $teamRow['userid'];
		$player1userType = $teamRow['usertype'];
		$teamRow = mysql_fetch_array($teamResult);
		$player2userId = $teamRow['userid'];
		$player2userType = $teamRow['usertype'];
		
	// if curent user is an admin or they are they user where a single person is needed
	// or where they are on the team where a doubles team is needed
	if(  (get_roleid()==2 || get_roleid() ==4) 
			|| ($player1userType == 0 && $player1userId == get_userid() ) 
			|| ($player2userType == 0 && $player2userId == get_userid() )
			|| ($player1userType == 1 && isCurrentUserOnTeam($player1userId) )
			|| ($player2userType == 1 && isCurrentUserOnTeam($player2userId) )
			){
		include($_SESSION["CFG"]["includedir"]."/include_update_doubles_form.php");
	}
	elseif( $player1userType == 1
			&& $player2userType == 0
			&& $player2userId == 0){
		include($_SESSION["CFG"]["includedir"]."/include_team_signup_doubles_form.php");
	}
	elseif($player1userType == 0
			&& $player1userId != 0
			&& $player2userType == 0
			&& $player2userId != 0){
		include($_SESSION["CFG"]["includedir"]."/include_players_signup_doubles_form.php");
		
	}
	elseif($player1userType == 0
			&& $player1userId != 0
			&& $player2userType == 0
			&& $player2userId == 0){
		include($_SESSION["CFG"]["includedir"]."/include_player_team_signup_doubles_form.php");
	}
	elseif($player1userType == 1
			&& $player1userId != 0
			&& $player2userType == 0
			&& $player2userId != 0){
		include($_SESSION["CFG"]["includedir"]."/include_doublesplayer_wanted_form.php");
	}
	else{
		print "error";
	}

}else{
	print "error Invalid form data Error 612";
}



include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");



/******************************************************************************
 * Validate the FORMS
 * 
 * These are the the forms for creating a singles reservation:
 * 
 * \includes\include_reservation_singles.php
 * 
 * These are the forms for updating a singles reservation:
 * 
 * \includes\include_update_singles.php (for admins or players in the reservation) action:create
 * \includes\include_signup_singles.php (for people signing up to play) action: addpartner
 * 
 * or
 * 
 * These are the forms for creating a doubles reservation:
 * 
 * \includes\include_reservation_doubles.php (for creating a new reservation) action:create
 * 
 * These are the forms for updating a doubles reservation
 * 
 * \includes\include_team_signup_doubles.php (for signing up another team)  action: addteam
 * \includes\include_doublesplayer_wanted_form.php (for signing up the last person) action: addpartner
 * \includes\include_players_signup_doubles_form.php (for signing one of the two teams) action: addpartners
 * \includes\include_player_team_doubles_form.php (for signing one of the two teams) action: addplayerorteam
 * 
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

		$msg = "";
        $errors = new Object;

        
        if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form() ");
        
          //First we have to get the reservationid
	      $residquery = "SELECT tblReservations.lastmodified
	                             FROM tblReservations
	                             WHERE tblReservations.courtid=".$frm['courtid']."
	                             AND tblReservations.time=".$frm['time']."
								 AND tblReservations.enddate IS NULL";
	
	      
	      //this is just a way to know if this is a new reservation.
	      $residresult =  db_query($residquery);
	      $resArray = mysql_fetch_array($residresult);
	      $reservationTimeStamp =  $resArray['lastmodified'];

        
		 /******************************************
         * 
         * Validate Event Reservation
         * 
         *******************************************/
        if($frm['courttype'] == "event"){

         	if (empty($frm["eventid"])) {
                      $errors->event = true;
                      $msg .= "You did not specify an event ";
               }
            elseif (empty($frm["repeat"])) {
                      $errors->repeat = true;
                      $msg .= "You did not specify the repeat interval";
               }
            elseif ($frm["repeat"] != "norepeat" && empty($frm["duration"])) {
                      $errors->duration = true;
                      $msg .= "You did not specify the duration interval ";
               }
        }
        
          /******************************************
         * 
         * Validate Singles Reservation
         * 
         *******************************************/
        elseif($frm['courttype'] == "singles"){
        	
        	if( isDebugEnabled(1) ) logMessage("court_reservation.validate_form(): Validating a Singles Reservation ". $frm["playertwoid"].".");
        	
        	 if( $frm['action']=="addpartner" && $frm["lastupdated"] != $reservationTimeStamp){
				 $msg = "Sorry, somebody out there just reserved this court. Please refresh the page and try again.";
			 } 
			 
		     elseif( $frm['action'] =="create" && get_roleid() == 1
		     		&& !empty($frm["playeroneid"]) && !empty($frm["playertwoid"])
		     		&& ! validateSkillPolicies($frm["playeroneid"], $frm["playertwoid"], $frm['courtid'], $frm['courttype'], $frm['time']) ){
                 $msg = "A skill range policy is preventing you from reserving this court with this opponent.";
            }
            
            //If a solo rervation by an admin make sure that playername is specified
            elseif($frm['action'] == "create" && $frm['matchtype']==5 && empty( $frm['playeroneid'] ) ){                    		 
                   $msg .= "Please specify a player for the solo reservation.";
             }
            
            // Validate Box Leagues
            elseif ($frm['action'] == "create" && $frm['matchtype']==1 &&  !empty($frm['playeroneid']) && !empty($frm['playertwoid']) )  {
            	
            	if( isDebugEnabled(1) ) logMessage("court_reservation.validate_form(): Validating a new singles box league reservation");
            	
					$boxid = getBoxIdForUser($frm["playeroneid"]);
				
					if($boxid>0){
						if(isBoxExpired($frm['time'],$boxid)){
							$msg .= "We are sorry but by the time these guys end up playing this match the box league will be done.";
						}
					}
	
					//Check that if player one is specified that he is in box
					if( !empty($frm['playeroneid']) && !is_inabox($frm['courtid'], $frm['playeroneid'])){
						$msg .= "$frm[playeronename] is not currently setup to play in a box league." ;
					}
					//Check that if player two is specified that he is in box
					elseif( !empty($frm["playertwoid"]) && !is_inabox($frm["courtid"], $frm["playertwoid"])){
						$msg .= "$frm[playertwoname] is not currently setup to play in a box league." ;
					}
					//if there are both players are specified that at least they are in a box togehter.
					elseif(!empty($frm["playeroneid"]) && !empty($frm["playertwoid"]) && !are_boxplayers($frm["playeroneid"],$frm["playertwoid"])){
						$msg .= "$frm[playeronename] and $frm[playertwoname] are not in a box league together.";
					}
					elseif(hasPlayedBoxWith($frm["playeroneid"],$frm["playertwoid"], $boxid)){
						$msg .= "Hold on, we just checked and  $frm[playeronename] and $frm[playertwoname] are already scheduled to play or have already played in this box. ";
					}
            }
        	//Validate form when SIGNING UP for a box league reservation
			elseif ($frm['action'] == "addpartner" && $frm['matchtype']==1 )  {
				
					if( isDebugEnabled(1) ) logMessage("court_reservation.validate_form(): Validating Singles Box League Reservation");
						
					if( !are_boxplayers($frm["guylookingformatch"], get_userid() )){
						$msg .= "You don't seem to be in a box league with this person";
					}
					
			}
            
         	//If this is is made as a buddy reservation make sure that this person is in fact....a buddy
            elseif ( get_roleid()==1 && $frm['action'] =="addpartner" && $frm['matchtype']==3 ){
						
                   	if( isDebugEnabled(1) ) logMessage("court_reservation.validate_form(): Validating Singles Buddy Reservation");
                            
                   	if(!amIaBuddyOf($frm['guylookingformatch'])) {

                               $fullnameResult = db_query("SELECT firstname, lastname from tblUsers WHERE userid=".$frm['guylookingformatch']);
                               $buddyArray = mysql_fetch_array($fullnameResult);
                               $msg .= "I am sorry but $buddyArray[firstname] $buddyArray[lastname] is looking for a match with a buddy";

                     }
             }
			// People can't play themselves, except for with special accounts (Club Member, Club Guest)
			elseif ( $frm['action'] =="create" &&  !isClubMemberName($frm["playeronename"])
						&& !isClubGuestName($frm["playeronename"]) //Club Members and Club Guests can play each other
						&& $frm["playeroneid"] == $frm["playertwoid"] //Names aren't the same
					) {
			                                      
						if( isDebugEnabled(1) ) logMessage("court_reservation.validate_form(): Validating that singles players can't play each other");
						$msg .= "Please specify different players";
			}
			//Club Members or Club Guests cannot look for matches
			elseif( $frm['action'] =="create" && isClubMemberName($frm["playeronename"]) && empty( $frm["playertwoname"] )) {                                
				
				$msg .= "Please register the club member to advertise for this match";
			}
			//Nor can club guests 
			elseif( $frm['action'] =="create" && isClubGuestName($frm["playeronename"]) && empty( $frm["playertwoname"] )) {                                     

				$msg .= "Please register the club guest to advertise for this match";
			}
			// Guest Reservations are only allowed on sites that are setup for  it.
			elseif( $frm['action'] =="create" && !isSiteGuestReservationEnabled()  && get_roleid() == 1
				  &&  ( isGuestPlayer($frm["playeroneid"],$frm["playeronename"])
						|| isGuestPlayer($frm["playertwoid"],$frm["playertwoname"]) )
			){
				$msg .= "It appears that you did not select your opponent correctly from the dropdown menu";
				
			}
			else if( get_roleid() == 1 || get_roleid() == 5){
				$msg = validateSchedulePolicies($frm['courtid'], $frm['time'], $frm['playeroneid']);
			}	 
		}

        /******************************************
         * 
         * Validate Doubles Reservation
         * 
         *******************************************/
        elseif($frm['courttype'] == "doubles"){
        	
        	if( isDebugEnabled(1) ) logMessage("court_reservation.validate_form(): Validating Doubles Reservation");
        	
        	if( ($frm['action']=="addteam" || $frm['action']=="addpartner" || $frm['action']=="addpartners" || $frm['action']=="addplayerorteam" )
        			&& $frm["lastupdated"] != $reservationTimeStamp){
				 $msg = "Sorry, somebody out there just reserved this court. Please refresh the page and try again.";
			 } 
			 
         	
        	//for validating reservation_doublesplayer_and_team_wanted_form
			elseif( $frm['action'] =="addplayerorteam" && $frm['userid'] == $frm['partner']  ){
                     $msg .= "The person you want to be partners with is already in the reservation.";
			}
                  
        	// For buddy reservations, regular players have to be a buddy of at least one of the people already playing
			elseif ( get_roleid() == 1 && $frm['matchtype']==3 &&  $frm['action']=="addteam"  ) {

				   if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form(): Validating Buddy Reservation.");
                            
					$iHaveABuddy = FALSE;
					$fullNameSearchQuery = "SELECT teamdetails.userid, users.firstname, users.lastname
					FROM tblUsers users, tblkpTeams teamdetails 
					WHERE users.userid = teamdetails.userid
					AND teamdetails.teamid=".$frm['teamid'];
					
					$fullNameSearchResult = db_query($fullNameSearchQuery);
					$playerArray = mysql_fetch_array($fullNameSearchResult);
					
					if(amIaBuddyOf($playerArray[0])) {
						$iHaveABuddy = TRUE;
					}
					
					$firstName0 = $playerArray[1];
					$lastName0 = $playerArray[2];
					
					$playerArray = mysql_fetch_array($fullNameSearchResult);
					                                 
					if(amIaBuddyOf($playerArray[0])) {
						$iHaveABuddy = TRUE;
					}
					
					$firstName1 = $playerArray[1];
					$lastName1 = $playerArray[2];
					
					
					if(!$iHaveABuddy){
						$msg .= "We're sorry but $firstName0 $lastName0 and $firstName1 $lastName1 are looking for a match with a buddy.";
					}
			}		
			// Make sure at least two people when solo reservation is enabled
			elseif( $frm['action'] =="create"){
				
				if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form(): Validating a player making a doubles reservation");
				
				if(  (isGuestPlayer($frm['playeroneid'], $frm['playeronename'])
						|| isGuestPlayer($frm['playertwoid'], $frm['playertwoname'])
						|| isGuestPlayer($frm['playerthreeid'], $frm['playerthreename'])
						|| isGuestPlayer($frm['playerfourid'], $frm['playerfourname']) )
				 ){
				 	$guestReservation = true;
				 }else{
				 	$guestReservation = false;
				 }
				
				if( isSiteGuestReservationEnabled() ){
					
					//Set the playerone name
					if( get_roleid() == 1){
						$playerOneName = get_userfullname();
					}
					else{
						$playerOneName = $frm['playeronename'];
					}
					
					// If any of the players are guests, all players must be specified
					if(  $guestReservation  
						&& 
						( empty($playerOneName)
							|| empty($frm['playertwoname'])
							|| empty($frm['playerthreename'])
							|| empty($frm['playerfourname']) )
							
						){
						return  "Please type in all names from the drop down list";
					}	
				}
				else{
					
					if($guestReservation){
						return "Please select all names from the drop down list";
					}
				}
				
				if(isSoloReservationEnabled() ){
					
					if( !$guestReservation 
						&& !isAtLeastOnePlayerSpecifiedForDoubles( $frm['playeroneid'], $frm['playertwoid'], $frm['playerthreeid'], $frm['playerfourid']) ){
						$msg .= "For doubles, please specify at least one person";
					}
				}
				else{
					
					if( !$guestReservation 
						&& !isAtLeastTwoPlayerSpecifiedForDoubles( $frm['playeroneid'], $frm['playertwoid'], $frm['playerthreeid'], $frm['playerfourid'])  ) {
						$msg .= "For doubles, please specify at least two people";
					}
				}
			}
			// Check for duplicates on create form
			elseif( $frm['action'] =="create" 
					&& !isAtLeastOnePlayerDuplicatedForDoubles(  $frm['playeroneid'], $frm['playertwoid'], $frm['playerthreeid'], $frm['playerfourid'] ) ){
				
				$msg .= "Please specify different people in the opposing team.";
			}
			// Check for duplicates on addteam form
			elseif ($frm['action'] == "addteam" ){
                     
                     if(isDebugEnabled(1) ) logMessage("court_reservation.validate_form: Validating the addteam form");
                     
                     $teamThatsAlreadyPlayingResult = getUserIdsForTeamId($frm["userid"]);
                     $playerRow = mysql_fetch_array($teamThatsAlreadyPlayingResult);
			         $teamPlayerOne =  $playerRow['userid'];
			         $playerRow = mysql_fetch_array($teamThatsAlreadyPlayingResult);
			         $teamPlayerTwo = $playerRow['userid'];
			         
					//make sure they aren't signing up with someone already in the reservation
                     if( !empty($frm['partnerid']) 
                     	&& ($teamThatsAlreadyPlaying[0] == $frm['partnerid'] || $teamThatsAlreadyPlaying[1] == $frm['partnerid'] ) ){
                    	 $msg .= "It looks like ". getFullNameForUserId($partnerId) ." is already playing.  Try picking someone else";
                     }
                     
                     if( isGuestPlayer($frm['partnerid'], $frm['partnername']) ){
                     	$msg = "Please select ".$frm['partnername']." from the dropdown list";
                     }
                     

			} 
			// Check for duplicates on addplayerorteam form
			elseif($frm['action'] =="addplayerorteam"){
				
				if(isDebugEnabled(1) ) logMessage("court_reservation.validate_form: Validating the addpartners form");
				
				// Make sure that partner is not userid
				if(  isGuestPlayer($frm["partner"],$frm["partnername"]) )  {
					$msg .= "Please select your partner from the drop down menu";
				}
				
				// Make sure that partner is not userid
				if( !empty($frm["partner"]) && $frm["partner"] ==  $frm["userid"])  {
					$msg .= "Please select a different partner";
				}
				
			}
			//Make sure that if player three and four are left empty that ids one or two are set (that team one is no guest)
			elseif( $frm['action'] =="create" 
					&& empty($frm["playerthreename"]) 
					&& empty($frm["playerfourname"]) 
					&&  empty($frm["playeroneid"]) 
					&& empty($frm["playertwoid"]) ){
				$msg .= "You have to put at least one name in";
			}
			// Check guest reservations
			elseif( $frm['action'] =="create" && !isSiteGuestReservationEnabled()  && get_roleid() == 1
				&& (
					isGuestPlayer($frm["playeroneid"],$frm["playeronename"])
					|| isGuestPlayer($frm["playertwoid"],$frm["playertwoname"])
					|| isGuestPlayer($frm["playerthreeid"],$frm["playerthreename"])
					|| isGuestPlayer($frm["playerfourid"],$frm["playerfourname"])
					)
			){
				 $msg .= "It appears that you did not select all of the players from the dropdown menu.";
			}
			// If one name is typed in, they all have to be
			elseif( $frm['action'] =="create" && 
					isFrontDeskGuestDoublesReservation($frm["playeroneid"],$frm["playeronename"],
						$frm["playertwoid"],$frm["playertwoname"],
						$frm["playerthreeid"],$frm["playerthreename"],
						$frm["playerfourid"],$frm["playerfourname"]) 
					&& isDoublesSpotAvailable($frm["playeronename"],$frm["playertwoname"],$frm["playerthreename"],$frm["playerfourname"]) 
			){
				$msg .= "If you type in at least one name, you aren't allowed to leave any spot open.";
			}
            else{
            	$msg = validateSchedulePolicies($courtid, $time);
            }
			
        
        }
 		else{
        	die("no valid forms error 202");
        }



        return $msg;
}


/******************************************
* 
* Update a reservation
* 
*******************************************/
function update_reservation(&$frm) {
	
	/* Update old reservation */
	if( isDebugEnabled(1) ) logMessage("court_reservation.update_reservation:");
	
/* First thing we do is check to see if this was made from a
   players wanted form if so will we update the court appropriately. */

     //First we have to get the reservationid
      $residquery = "SELECT tblReservations.reservationid,tblReservations.matchtype
                             FROM tblReservations
                             WHERE tblReservations.courtid='$frm[courtid]'
                             AND tblReservations.time='$frm[time]'
							 AND tblReservations.enddate IS NULL";

      $residresult =  db_query($residquery);

      //this is just a way to know if this is a new reservation.
      $reservation = mysql_num_rows($residresult);

      $resArray = mysql_fetch_array($residresult);
      $residval =  $resArray['reservationid'];
      $matchtype = $resArray['matchtype'];


   if ($frm['courttype']=="singles" && $frm['action']=="addpartner"){

      // Now we just need to update that reservation
      $qid = db_query("UPDATE tblkpUserReservations SET userid = ".get_userid()."
      					WHERE reservationid = $residval
                       	AND userid = 0");
      
      // Now we just need to update that reservation
      $qid = db_query("UPDATE tblReservations SET lastmodifier = ".get_userid()."
      					WHERE reservationid = $residval");

                               	
		   //Update the boxhistory table
		   if($matchtype==1){
		           $boxid = getBoxIdForUser(get_userid());
		           $query = "INSERT INTO tblBoxHistory (
		                     boxid, reservationid
		                     ) VALUES (
		                            $boxid
		                            ,'$residval')";
		                            
		           $result = db_query($query);
		   }
		
		   //Send out update emails
		   confirm_singles($residval, false);
		   return $residval;

   }
  
 //This is the form that allows a player to sign up either with 
 //an player or pick a partner from a list.
 // reservation_doublesplayer_and_team_wanted_form.php
 elseif($frm['courttype']=="doubles" && $frm['action']=="addplayerorteam"){
 	
 	if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation: A Player is updating reservation where one player is looking for a partner and a team is needed too (action: addplayerorteam )");
	
 	//Get Court Type for making the team
 	$courtTypeId = getCourtTypeIdForCourtId($frm['courtid']);
 	
 	$qid1 = db_query("UPDATE tblReservations
							  SET lastmodifier = ".get_userid()."
                              WHERE reservationid = $residval");
                              
 	//Playwith is variable only used on this page for indicating who the player wants
 	//to play with, either the person who made the reservation or someone else.
 	if($frm['playwith']=="1"){
		
       $currentTeamID = getTeamIDForCurrentUser($courtTypeId,$frm['userid']);
       
       //Replace the individual with the new team
       $qid = db_query("UPDATE tblkpUserReservations
                       SET userid =$currentTeamID,
                       usertype = 1
                       WHERE reservationid = $residval
                       AND userid = $frm[userid]");
     	
 	}
 	//Sign up with a partner of this persons choosing
 	else{

 		 $currentTeamID = getTeamIDForCurrentUser($courtTypeId,$frm['partner']);
 		 
 		 // Now we just need to update that reservation
      	$qid = db_query("UPDATE tblkpUserReservations
                       SET userid =$currentTeamID,
                       usertype = 1
                       WHERE reservationid = $residval
                       AND userid = 0");
 		 
 	}   
      
 	//Send out update emails
    confirm_doubles($residval, false);
    return $residval;
 	
 }
  //This is the form that allows a player to sign up either with 
 // include_doublesplayer_wanted_form.php
 elseif($frm['courttype']=="doubles" && $frm['action']=="addpartners"){
 	
 	if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation: A player is updating reservation where two players were looking for a partner (action: addpartners)");
	
 	//Get Court Type for making the team
 	$courtTypeId = getCourtTypeIdForCourtId($frm['courtid']);
	$currentTeamID = getTeamIDForCurrentUser($courtTypeId,$frm['partner']);
 		 
		 //Update the last modifier
		 $qid1 = db_query("UPDATE tblReservations
							  SET lastmodifier = ".get_userid()."
	                          WHERE reservationid = $residval");
 		 
 		 // Now we just need to update that reservation
      	$qid = db_query("UPDATE tblkpUserReservations
                       SET userid = $currentTeamID,
                       usertype = 1
                       WHERE reservationid = $residval
                       AND userid = $frm[partner]
					   AND usertype = 0");
      
 	return $residval;
 	 
 }
 //Check to see if we are to add a team to a reservation
 elseif($frm['courttype']=="doubles" && $frm['action']=="addteam"){

	if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation: A Player is updating reservation where a team was looking for another team (action: addteam)");
       
		       if( empty($frm['partnerid']) ){
		       	
		       	if( isDebugEnabled(1) ) logMessage("\tThe partner is not set, just adding the user");
			
		       	 // Now we just need to update that reservation
		          $qid = db_query("UPDATE tblkpUserReservations
		                       SET userid = ".get_userid(). ",usertype = 0
		                       WHERE reservationid = $residval
		                       AND userid = 0");
		                       
		       
		       }else{
		       	

		       	if( isDebugEnabled(1) ) logMessage("\tThe partner is set, adding the team.");
		       	
		       	 $courtTypeId = getCourtTypeIdForCourtId($frm['courtid']);
		       	 $currentTeamID = getTeamIDForCurrentUser($courtTypeId,$frm['partnerid']);
		       	 
		       	 $updateQuery = "UPDATE tblkpUserReservations
		                       SET userid =$currentTeamID, usertype=1
		                       WHERE reservationid = $residval
		                       AND userid = 0";
		       	 
		       	  // Now we just need to update that reservation
		          $qid = db_query($updateQuery);
		       	
		       }
      
     //Update the last modifier
	 $qid1 = db_query("UPDATE tblReservations
						  SET lastmodifier = ".get_userid()."
                          WHERE reservationid = $residval");
		
     

    //Send out update emails
    confirm_doubles($residval, false);
    
    return $residval;
    
   }

 //Check to see if we are to add a single player to a team in the reservtion.  To do this we
 // will first add the make the team if it doesn't exist already.  Then we will reset the
 // player with the new or existing teamid.  Finally we will reset the usertype on the tlbkpReservation
 // table
 elseif($frm['courttype']=="doubles" && $frm['action']=="addpartner"){

		if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation: A Player is updating reservation where a player was looking for a partner (addpartner)");
	
		$courtTypeId = getCourtTypeIdForCourtId($frm['courtid']);
        $currentTeamID = getTeamIDForCurrentUser($courtTypeId,$frm['userid']);

	  //Update the last modifier
	  $qid1 = db_query("UPDATE tblReservations
						  SET lastmodifier = ".get_userid()."
                          WHERE reservationid = $residval");

      //UPdate the tblkpUserReservation Table
      $qid = db_query("UPDATE tblkpUserReservations SET userid =$currentTeamID
                       WHERE reservationid = $residval
                       AND userid = $frm[userid]
                       AND usertype = 0");

      //Now set the usertype to reflect a team reservation
      $qid = db_query("UPDATE tblkpUserReservations SET usertype = 1
                       WHERE reservationid = $residval
                       AND userid = $currentTeamID");
                       
      //Send out update emails
	  confirm_doubles($residval, false);
	  
	  return $residval;

 }
	
	
}




/******************************************
* 
* Insert a reservation
* 
*******************************************/
function insert_reservation(&$frm) {

		if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation");

		// Initialize Guest Type
		$guesttype = 0;
		
		
		if ($frm['courttype'] == "event"){
				
				makeEventReservation($frm);
				return;

		 } 
		 
		 
		 
		 
		 
		 if( $frm['courttype']=="singles" && $frm['action']=="create" 
        		&& (
        				isGuestPlayer($frm['playeroneid'], $frm['playeronename']) 
        			|| 	isGuestPlayer($frm['playertwoid'], $frm['playertwoname']) 
        			)
        	){
			
            $guesttype = 1;
        }
        
        /* Or This is a guest reservation if its a a doubles reservation and
         */
        elseif( $frm['courttype']=="doubles" 
        && 
        	// if ther is a player name with no player id on any of the players
        	isFrontDeskGuestDoublesReservation( 
        			$frm['playeroneid'],  
        			$frm['playeronename'], 
        			$frm['playertwoid'], 
        			$frm['playertwoname'], 
        			$frm['playerthreeid'], 
        			$frm['playerthreename'], 
        			$frm['playerfourid'],
        			$frm['playerfourname']) 
        	 
        	 ){
            $guesttype = 1;
        }
        
  
	        // Add the Reservation
	        $resquery = "INSERT INTO tblReservations (
	                courtid, time, matchtype, guesttype, lastmodifier, creator, createdate
	                ) VALUES (
	                          '$frm[courtid]'
	                          ,'$frm[time]'
	                          ,'$frm[matchtype]'
	                          ,$guesttype
							  , ".get_userid()."
							  , ".get_userid()."
							  , now() )";
	
	        db_query($resquery);
	                  
	         //Now we need to get the reservationid.  (This is what we just inserted )
	        $residquery = "SELECT reservationid FROM tblReservations WHERE courtid= '$frm[courtid]'
	                       AND time='$frm[time]' AND enddate IS NULL";
	
	         $residresult =  db_query($residquery);
	         $residvarresult = db_fetch_object($residresult);
	         
	         
	
           if ($frm['courttype']=="doubles"){   
             makeDoublesReservation($frm ,$guesttype, $residvarresult->reservationid);
           }
           elseif( $frm['courttype']=="singles" ) {
           	
           		if( $frm['matchtype']=="5" ){
  					makeSoloReservation($frm, $residvarresult->reservationid);
           		}else{
	       			makeSinglesReservation($frm ,$guesttype, $residvarresult->reservationid);
	       		}

           }
	           

	         
			return $residvarresult->reservationid;
			
			
	
}




/**
 * Makes an event reservations
 */
function makeEventReservation(&$frm){
	
	 if( isDebugEnabled(1) ) logMessage("court_reservation.MakeEventReservation: Making an Event Reservation");
	
	$clubquery = "SELECT timezone from tblClubs WHERE clubid=".get_clubid()."";
	$clubresult = db_query($clubquery);
	$clubobj = db_fetch_object($clubresult);
	$courtid = $frm['courtid'];
	$tzdelta = $clubobj->timezone*3600;
	
	if( isDebugEnabled(1) ) logMessage("court_reservation.makeEventReservation: Repeat interval is ".$frm['repeat']." and duration inverval is ". $frm['duration']);
	
	 // Add the non reoccuring event
	    if ($frm['repeat']=="norepeat"){
	
	      $resquery = "INSERT INTO tblReservations (
	                 courtid, eventid, time, lastmodifier, creator, createdate
	                 ) VALUES (
	                           '$frm[courtid]'
	                           ,'$frm[eventid]'
	                           ,'$frm[time]'
							   , ".get_userid()."
							   ,  ".get_userid()."
							   , now() )";
	
	        $resresult =  db_query($resquery);
	    }
	
	 	// Add the daily event
	     elseif ($frm['repeat']=="daily"){
	
				 $initialHourstart = 0;
				
		         //Set the occurance interval
		         if($frm['duration']=="week")
		         $numdays = 7;
		         if($frm['duration']=="month")
		         $numdays = 30;
		         if($frm['duration']=="year")
		         $numdays = 365;
	
		         for ($i = 0; $i < $numdays; $i++) {
				  
				         $nextday = gmmktime (gmdate("H",$frm['time']),
				         						gmdate("i",$frm['time']),
				         						gmdate("s",$frm['time']),
				         						gmdate("n",$frm['time']),
				         						gmdate("j", $frm['time'])+$i,
				         						gmdate("Y", $frm['time']));
						
						
						 // Set the event interval.  This will be the duration for the court for that day
				        $dayOfWeek = gmdate("w", $nextday);
				        $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtid";
				        $courtHourResult = db_query($courtHourQuery);
				        $courtHourArray = mysql_fetch_array($courtHourResult);
				        
						
						//Save off the first reservation time
						if($i>0){

				         	$hourstart = $initialHourstart - $courtHourArray["hourstart"];
				        	$nextday -= ($hourstart * 60);
				         }
				         else{
				         	$startday = $nextday;
				         	$initialHourstart = $courtHourArray["hourstart"];
				         }
				
						if(!isCourtAlreadyReserved($frm['courtid'], $nextday)){
							//Add as reservation
					         $resquery = "INSERT INTO tblReservations (
					                 courtid, eventid, time, lastmodifier, creator
					                 ) VALUES (
					                           '$frm[courtid]'
					                           ,'$frm[eventid]'
					                           ,$nextday
											   , ".get_userid()."
											   , ".get_userid().")";
					
					        $resresult =  db_query($resquery);
						}
				
		        
		       }
		       
		       
		        //Add as reoccuring event
		        $reoccuringQuery = "INSERT INTO tblReoccuringEvents (
						courtid, eventinterval, starttime, endtime
						) VALUES (
							$frm[courtid],
							86400,
							$startday,
							$nextday)";
							
						
			   db_query($reoccuringQuery);
	       }
	
	       //Add the weekly event
	       elseif ($frm['repeat']=="weekly"){

		         //Set the occurance interval
		         if($frm['duration']=="week")
		         	$numdays = 7;
		         if($frm['duration']=="month")
		         	$numdays = 30;
		         if($frm['duration']=="year")
		         	$numdays = 365;
	
				 $initialHourstart = 0;
				 
		         for ($i = 0; $i < $numdays; $i += 7) {
		
			         $nextday = gmmktime (gmdate("H",$frm['time']),
			         						gmdate("i",$frm['time']), 
			         						gmdate("s",$frm['time']),
			         						gmdate("n",$frm['time']),
			         						gmdate("j", $frm['time'])+$i,
			         						gmdate("Y", $frm['time']));
			
			         // Set the event interval.  This will be the duration for the court for that day
			        $dayOfWeek = gmdate("w", $nextday);
			        $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtid";
			        $courtHourResult = db_query($courtHourQuery);
			        $courtHourArray = mysql_fetch_array($courtHourResult);
			        
			        
			       //Save off the first reservation time
					if($i>0){
	
			         	$hourstart = $initialHourstart - $courtHourArray["hourstart"];
			        	$nextday -= ($hourstart * 60);
			         }
			         else{
			         	$startday = $nextday;
			         	$initialHourstart = $courtHourArray["hourstart"];
			         }
			          
			        if(!isCourtAlreadyReserved($frm['courtid'], $nextday)){
					//Add as reservation
			         $resquery = "INSERT INTO tblReservations (
			                 courtid, eventid, time, lastmodifier, creator
			                 ) VALUES (
			                           '$frm[courtid]'
			                           ,'$frm[eventid]'
			                           ,$nextday
									   , ".get_userid()."
									   , ".get_userid().")";
			
			        $resresult =  db_query($resquery);
				}
		         
		
		       }
		       
		        //Add as reoccuring event
		        $reoccuringQuery = "INSERT INTO tblReoccuringEvents (
						courtid, eventinterval, starttime, endtime
						) VALUES (
							$frm[courtid],
							604800,
							$startday,
							$nextday
						)";
						
			     db_query($reoccuringQuery);
	       }
	
	       //Add the monthly event
	       elseif ($frm['repeat']=="monthly"){
	
	         //Set the occurance interval
	         if($frm['duration']=="week")
	         $numdays = 1;
	         if($frm['duration']=="month")
	         $numdays = 1;
	         if($frm['duration']=="year")
	         $numdays = 12;
	
			 $initialHourstart = 0;	
			 
		         for ($i = 0; $i < $numdays; $i++) {
		
		         	$nextday = gmmktime (gmdate("H",$frm['time']),
		         						gmdate("i",$frm['time']), 
		         						gmdate("s",$frm['time']),
		         						gmdate("n",$frm['time'])+$i,
		         						gmdate("j", $frm['time']),
		         						gmdate("Y", $frm['time']));
		         	 
		         	 // Set the event interval.  This will be the duration for the court for that day
			        $dayOfWeek = gmdate("w", $nextday);
			        $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtid";
			        $courtHourResult = db_query($courtHourQuery);
			        $courtHourArray = mysql_fetch_array($courtHourResult);
			        				
		         						
			       //Save off the first reservation time
					if($i>0){

			         	$hourstart = $initialHourstart - $courtHourArray["hourstart"];
			        	$nextday -= ($hourstart * 60);
			         }
			         else{
			         	$startday = $nextday;
			         	$initialHourstart = $courtHourArray["hourstart"];
			         }
			         
			         if(!isCourtAlreadyReserved($frm['courtid'], $nextday)){
						//Add as reservation
				         $resquery = "INSERT INTO tblReservations (
				                 courtid, eventid, time, lastmodifier, creator
				                 ) VALUES (
				                           '$frm[courtid]'
				                           ,'$frm[eventid]'
				                           ,$nextday
										   , ".get_userid()."
										   , ".get_userid().")";
				
				        $resresult =  db_query($resquery);
			}
		
		       }
			       
			     //Add as reoccuring event
		        $reoccuringQuery = "INSERT INTO tblReoccuringEvents (
						courtid, eventinterval, starttime, endtime
						) VALUES (
							$frm[courtid],
							2419200,
							$startday,
							$nextday
						)";
				
				db_query($reoccuringQuery);
	}
	

}


/**
 * Make a solo reservation
 * 
 */
 function makeSoloReservation($frm, $reservationid){
 	
 	 if( isDebugEnabled(1) ) logMessage("court_reservation.makeSoloReservation");
	

         		//if this is empty that means that the user typed something in, a guest reservation
         		if( empty($frm['playeroneid']) ){
         			
         			$userid = $frm['playeronename'];
         			
         			//If there is name is set, that means that
		               $query = "INSERT INTO tblkpGuestReservations (
		                                reservationid, name
		                                ) VALUES (
		                                          '$reservationid'
		                                          ,'$userid')";
		              // run the query on the database
		              $result = db_query($query);
         			
         		}
         		//Else the playeroneid is set and we can use it for a normal reservation
         		else{
         			
         			$userid = $frm['playeroneid'];	

		         	//Make Player Ones Reservation
	               	$query = "INSERT INTO tblkpUserReservations (
	                                reservationid, userid, usertype
	                                ) VALUES (
	                                          '$reservationid'
	                                          ,$userid
	                                          ,0)";
	              	// run the query on the database
	              	$result = db_query($query);
         			
         			
         		//ONly send out emails to registered users
				confirm_singles($reservationid, true);	
         			
         		}
         		
 }




/**
 * 
 * Make a doubles reservation
 * pass in the frm object, guesttype (which was already figured out), reservation id
 * 
 */
function makeDoublesReservation($frm, $guesttype, $reservationid){
	
	
		if( isDebugEnabled(1) ) logMessage("court_reservation.makeDoublesReservation");
			
          //Set the User type value to 1 to indicate that the userid specify teamids
         // not userids.  A usertype of 0 will be the default where userids are userids
         // in the tblkpUserReservations.

         $qid = db_query("UPDATE tblReservations
                          SET usertype = 1
                          WHERE reservationid = $reservationid");


         //Get the tblCourtType id for this court
          $courttypeid = getCourtTypeIdForCourtId($frm['courtid']);

           // Now update the users (either front guest type or regular)


	                       //check if userids are presetn
	                       if( $guesttype == 0){
	                         
		                           /*
		                            * If playertwoid = nopartner, then that means that the team one is actually a single
		                            * looking for a partner, in that case usertype will be 0 (single)
		                            */
			                        if( empty($frm['playertwoid']) ){
			                        	
			                        	$teamid1 = $frm['playeroneid'];
			                        	$teamone_usertype = 0;
			                        	
			                        }
			                        elseif( empty($frm['playeroneid']) ){
			                        	$teamid1 = $frm['playertwoid'];
			                        	$teamone_usertype = 0;
			                        }
			                        else{
			                        	
			                        	$teamid1 = getTeamIDForPlayers($courttypeid,$frm['playeroneid'],$frm['playertwoid']);
			                        	$teamone_usertype = 1;
			                        }
			                        
			                        
			                        
	                       $query = "INSERT INTO tblkpUserReservations (
	                                reservationid, userid, usertype
	                                ) VALUES (
	                                       '$reservationid'
	                                        ,$teamid1
	                                        ,$teamone_usertype)";
	
	                       		// run the query on the database
	                     		$result = db_query($query);
	
	
		                        /* There is the distinct possibility that a club administrator or front desk user
		                        * left playerthree and playerfour empty.  what this means is that they will be
		                        * making this reservation looking for another team
		                        * 
		                        */
		                        if( empty( $frm['playerthreeid']) && empty( $frm['playerfourid']) ){
		                        	
		                        	$teamid2 = 0;
		                        	$teamtwo_usertype = 0;
		                        	
		                        }
		                        else{
			                       
			                       	    /*
				                        * Something has been entered, now we just check if team two has two players
				                        * looking for a partner, in that case usertype will be 0 (single)
				                        */
				                        if( empty($frm['playerfourid'])){
				                        	
				                        	$teamid2 = $frm['playerthreeid'];
				                        	$teamtwo_usertype = 0;
				                        	
				                        }
				                        elseif( empty($frm['playerthreeid']) ){
				                        	
				                        	$teamid2 = $frm['playerfourid'];
				                        	$teamtwo_usertype = 0;
				                        	
				                        }
				                        else{
				                        	$teamid2 = getTeamIDForPlayers($courttypeid ,$frm['playerthreeid'],$frm['playerfourid']);
				                        	$teamtwo_usertype = 1;
				                        }
			                       
	
		                        }
	
	
	                      $query = "INSERT INTO tblkpUserReservations (
	                            reservationid, userid, usertype
	                            ) VALUES (
	                                   '$reservationid'
	                                    ,$teamid2
	                                    ,$teamtwo_usertype)";
	
	                       // run the query on the database
	                     $result = db_query($query);
	                     
	                     confirm_doubles($reservationid, true);
	                     
	                    

					 
                     }
                     elseif( $guesttype == 1){

						// Set the player one name
						if( get_roleid() ==1 ){
							$playerOneName = get_userfullname();
							
						}else{
							$playerOneName = $frm['playeronename'];
						}
                     	
                     	$query = "INSERT INTO tblkpGuestReservations (
                                           reservationid, name
                                           ) VALUES (
                                           '$reservationid'
                                           ,'$playerOneName - $frm[playertwoname]')";

                             // run the query on the database
                            $result = db_query($query);

                             $query = "INSERT INTO tblkpGuestReservations (
                                                 reservationid, name
                                                 ) VALUES (
                                                 '$reservationid'
                                                 ,'$frm[playerthreename] - $frm[playerfourname]')";

                             // run the query on the database
                             $result = db_query($query);



                     }
     
}


/**
 * Make a plain old singles reservation.  Thats right, a plain old singles reservation
 * This taks a form object, a guesttype, and the reservationid
 */
function makeSinglesReservation($frm, $guesttype, $reservationid){
	

        $playerone = $frm['playeroneid'];
        $playertwo = $frm['playertwoid'];
        
        if( isDebugEnabled(1) ) logMessage("court_reservation.makeSinglesReservation: playerone: $playerone playertwo $playertwo and guesttype is $guesttype");
        
        //Set these puppies
        if(empty($playerone)){
        	$playerone = 0;
        }
         if(empty($playertwo)){
        	$playertwo = 0;
        }
        
         /*Right now the rule is if either one of the players here are club guests then
         both of them will be considered club guests (even if one of them is a member).
         The way we check this is if a playerid is sent.  If we don't have in our
         possession both playeroneid and playertwoid we are going to update the guest table with
         two new names.
         */

         if( $guesttype == 0){
                   //Make Player Ones Reservation
                   $query = "INSERT INTO tblkpUserReservations (
                                    reservationid, userid, usertype
                                    ) VALUES (
                                              '$reservationid'
                                              ,'$playerone'
                                              ,0)";


                   // run the query on the database
                   $result = db_query($query);

                   //Make Player Two Reservation
                   $query = "INSERT INTO tblkpUserReservations (
                                    reservationid, userid, usertype
                                    ) VALUES (
                                              '$reservationid'
                                              ,'$playertwo'
                                              ,0)";

                 // run the query on the database
                   $result = db_query($query);

                   /*
                     If this was made a a box league resevation update tblBoxHistory.  At this point
                     we have already validated that these users are one in a box and two in a box
                     together so we are not going to mess around with that here.
                   */
                    if($frm['matchtype']==1){
                        $boxid = getBoxIdForUser($frm['playeroneid']);
                        $query = "INSERT INTO tblBoxHistory (
                                  boxid, reservationid
                        ) VALUES (
                                  $boxid
                                  ,'$reservationid')";

                        $result = db_query($query);
                }

                   confirm_singles($reservationid, true);
    

              }
            elseif( $guesttype == 1){
            
            	// playeronename wont' be set with regular players, its disabled.
            	if( get_roleid() == 1 ){
            		$playerOneName = get_userfullname();
            	}
            	else{
            		$playerOneName = $frm['playeronename'];
            	}
            	
            	
            	//We don't have both of the playerids so we are going to book a guest reservation.
                   $query = "INSERT INTO tblkpGuestReservations (
                                    reservationid, name
                                    ) VALUES (
                                              '$reservationid'
                                              ,'$playerOneName')";
                     
                    // run the query on the database
                   $result = db_query($query);

							 //Make Player Two Reservation (if not a solo reservation)
							if( $frm['matchtype']!=5 ){
								 
                             	$query = "INSERT INTO tblkpGuestReservations (
                                            reservationid, name
                                            ) VALUES (
                                                      '$reservationid'
                                                      ,'$frm[playertwoname]')";

	                           // run the query on the database
	                           $result = db_query($query);

							}
                  
            }

}






?>
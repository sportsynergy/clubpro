<?php
/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $

*/

include("../application.php");
require($_SESSION["CFG"]["libdir"]."/reservationlib.php");
$DOC_TITLE = "Court Reservation";

//Set the http variables
$courtid = $_REQUEST["courtid"];
$time = $_REQUEST["time"];
$userid = $_REQUEST["userid"];
$ct = $_REQUEST["ct"];

//Do some administrative things
//***************************************************************

require_loginwq();
$useridstring = "";
if(isset($userid)){
  $useridstring = "&userid=$userid";
}
$backtopage = $_SESSION["CFG"]["wwwroot"]."/users/court_reservation.php?time=$time&courtid=$courtid$useridstring";

/************************************************************************************************/



$getAuthDetailsQuery = "SELECT tblCourts.siteid, tblCourtType.courttypeid
                       FROM (tblCourts INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid)
                       WHERE (((tblCourts.courtid)=$courtid))";

$getAuthDetailsResult = db_query($getAuthDetailsQuery);
$authDetailsArray = mysql_fetch_array($getAuthDetailsResult);

$currentCourtCourtTypeID = $authDetailsArray['courttypeid'];
$currentCourtSiteID = $authDetailsArray['siteid'];


//Do sport auth check for members and admins
if(get_roleid()==1){

      if(!amiValidForSite($currentCourtSiteID) || !isValidForCourtType($currentCourtCourtTypeID, get_userid() )){
           $errormsg = "Sorry, you are not authorized to reserve this court.";
           include($_SESSION["CFG"]["templatedir"]."/header.php");
           include($_SESSION["CFG"]["includedir"]."/errorpageNoLink.php");
           include($_SESSION["CFG"]["templatedir"]."/footer.php");
           die;
      }



}


// Limited Players can't reserve courts themselves
if(get_roleid()==5){
	
			$errormsg = "Sorry, you are not authorized to reserve this court.  Talk to the pro about getting set up to do this.";
           include($_SESSION["CFG"]["templatedir"]."/header.php");
           include($_SESSION["CFG"]["includedir"]."/errorpageNoLink.php");
           include($_SESSION["CFG"]["templatedir"]."/footer.php");
           die;
           
}

if (match_referer() && isset($_POST['courttype'])) {

		// Set some variables 
        $frm = $_POST;
        $wwwroot = $_SESSION["CFG"]["wwwroot"];
        
        
        // Validate
        $errormsg = validate_form($frm, $errors);
		
		//Actually Make the Reservation
		if (empty($errormsg)){
			  	
				if( isDebugEnabled(1) ) logMessage("Inserting the reservation");
			  	$resid = insert_reservation($frm);
		
				// Event Reservations dont' set this value
				if ($frm['courttype'] != "event"){
					
					$guesttypequery = "SELECT guesttype FROM tblReservations where reservationid = $resid";
	                $guesttyperesult =  db_query($guesttypequery);
	                $guesttype = mysql_result($guesttyperesult,0);
				}
	            
	            //Becuase we just got done making a reservation for one this has to be a non front desk user.
	            if ($frm['opponent']=="" && $frm['matchtype']==1){
					   if( isDebugEnabled(1) ) logMessage("Prompting for more details as matchtype is 1 and opponent is not set");
	                   $boxid = getBoxIdForUser(get_userid());
	                   header ("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time&boxid=$boxid");
	
	            }
	            //Doubles reservations by a player 
	             elseif ( $frm['courttype']=="doubles" 
	             		&& $frm['whichone']=="lookformatchwithpartner"){ 
	                  if( isDebugEnabled(1) ) logMessage("Prompting for more details as courttype is doubles and whichone is lookformatchwithpartner");
	                  header ("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time");
	            }
	            //Singles non-admin reservations for practice, challenge or box leagues matches, solo matches dont' advertise.
	            elseif(
	            	$frm['courttype']=="singles"
	            	&& $frm['opponent']==""
	            	&& $frm['usertype']=="nonfrontdesk"
	            	&& $guesttype == 0
	            	&& ($frm['matchtype']==0 || $frm['matchtype']==1 || $frm['matchtype']==2)
	            			
	            ){
	            	if( isDebugEnabled(1) ) logMessage("courttype is singles and opponent is empty");
	            	header ("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time");
	            }
	            /** 
	             * When a front desk user makes a singles reservations without specifing both
	             * players automatically send emails to everyone (within the clubs settings) if
	             * if this is an program admin redirect to the details page where they can choose
	             *  how to send out these emails. 
	             **/
	            elseif($frm['courttype']=="singles"
	                  && $frm['playertwoname'] =="" && get_roleid()=="2"){
	            	 if( isDebugEnabled(1) ) logMessage("Prompting for more details as playertwoname is empty and role is 2");
	            	header ("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time");
	            }
	            /**
	             * When a program adminisrator makes a doubles reservation and leaves one spot open
	             */
	            elseif( 
	            		// If any of the spots are empty
	            		($frm['playeroneid']=="" ||
	            		$frm['playertwoid']=="" ||
	            		$frm['playerthreeid']=="" || 
	            		$frm['playerfourid']=="")
	            		
	            		// its a doubles reservation
	            		&& $frm['usertype']=="frontdesk"
	            		&& $frm['courttype']=="doubles"){
	            	 		if( isDebugEnabled(1) ) logMessage("Prompting for more details as this is a front desk doubles reservation and a name is empty");
	            			header ("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time");
	            }
	            
	            else{
	                 header ("Location: $wwwroot/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ."");
	            }
	            die;
	    }
		    
             

        

}

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/form_header.php");


$courtformquery = "SELECT tblCourtType.courttypeid, tblCourts.courtid, tblCourtType.reservationtype
                   FROM tblCourtType
                   INNER JOIN tblCourts ON tblCourtType.courttypeid = tblCourts.courttypeid
                   WHERE (((tblCourts.courtid)=$courtid))";

$courtformresult = db_query($courtformquery);
//$courtformarray = mysql_fetch_array($courtformresult);

//Get the reservation type if it exists.
$rezType = "SELECT tblReservations.usertype
            FROM tblReservations
            WHERE (((tblReservations.courtid)=$courtid)
            AND ((tblReservations.time)=$time))
			AND tblReservations.enddate IS NULL";

$rezResult = db_query($rezType);
if(mysql_num_rows($rezResult)>0){
  $rezType = mysql_result($rezResult,0);
}


//Find out if there are any openings for the court
$needpartnerquery = "SELECT tblkpUserReservations.userid, tblkpUserReservations.usertype
                     FROM tblReservations INNER JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
                     WHERE tblReservations.courtid=$courtid
                     AND tblReservations.time=$time
					 AND tblReservations.enddate IS NULL
					 ORDER BY tblkpUserReservations.usertype, tblkpUserReservations.userid ";

// run the query on the database
$needpartnerresult = db_query($needpartnerquery);


      // Determine what kind of court reservation form to display
          $row = mysql_fetch_object($courtformresult);
          $reservationType = $row->reservationtype;

        if(!isset($ct)){

          if($reservationType==2){
              $submitchoice = "submit";
              $courtchoice="doubles";
          }
          elseif($reservationType==0){
               $submitchoice = "submit";
               $courtchoice="singles";
          }
          elseif($reservationType==1){
               $submitchoice = "submit";
               if($rezType==1){
                  $courtchoice="doubles";
               }
               else{
                  $courtchoice="singles";
               }

          }
       }
       else{


           //Do a quick check to make sure that ct isn't being
          // dicked around with through the url
            if($ct=="doubles" && $reservationType==0){
              $courtchoice="singles";
            }
            elseif($ct=="singles" && $reservationType==2){
             $courtchoice="doubles";
            }
            elseif($ct=="event" && $reservationType==2 && get_roleid()<2){
             $courtchoice="doubles";
            }
            elseif($ct=="event" && $reservationType==1 && get_roleid()<2){
             $courtchoice="singles";
            }
            elseif($ct=="event" && $reservationType==0 && get_roleid()<2){
             $courtchoice="singles";
            }
            else{
                $courtchoice=$ct;
            }
            $submitchoice = "submit";
       }




      //Ask what type of court reservation we want to make either doubles or singles.
             if($courtchoice=="singles"){

                     //Display Singles Court Reservation Form
                      if(isset($userid) && $userid==get_userid() && get_roleid()!=2){
                              redirect($_SESSION["CFG"]["wwwroot"]."/users/court_cancelation.php?time=$time&courtid=$courtid");
                      }
                      else{
                                       while($row = mysql_fetch_row($needpartnerresult)) {
                                              if($row[0]==0){
                                                  $needpartnerval = 1;
                                               }
                                       }
                                        if(has_priv(4) && isset($needpartnerval) ){
                                           redirect($_SESSION["CFG"]["wwwroot"]."/users/court_cancelation.php?time=$time&courtid=$courtid&cmd=cancelall");
                                        }
                                        
                                        if(isset($needpartnerval) && get_roleid()=="1"){
                                               include($_SESSION["CFG"]["templatedir"]."/reservation_singles_wanted_form.php");
                                         }
                                         elseif(isset($needpartnerval)){
                                         		redirect($_SESSION["CFG"]["wwwroot"]."/users/court_cancelation.php?time=$time&courtid=$courtid");
                                         }
                                         else{
                                              //If this is a frontdesk user display a form to take both players.
                                              if(get_roleid()==4 || get_roleid()==2){
                                                 include($_SESSION["CFG"]["templatedir"]."/reservation_singles_frontdesk_form.php");
                                              }else{
                                               include($_SESSION["CFG"]["templatedir"]."/reservation_singles_form.php");
                                               }
                                          }


                     }
                 }
                 //Display Doubles Reservation Form
                 elseif($courtchoice=="doubles"){

                 			if( isDebugEnabled(1) ) logMessage("Court Reservation.  Determining what form to load");		
                            //Look up all teams that the person is in listing singles players first
                            $myteamsquery = "SELECT tblTeams.teamid
                                             FROM tblTeams INNER JOIN tblkpTeams ON tblTeams.teamid = tblkpTeams.teamid
                                             WHERE (((tblkpTeams.userid)=".get_userid() ."))";

                            // run the query on the database
                            $myteamsresult = db_query($myteamsquery);

                            //Compare what is passed as the userid to the teams in the query  for players looking
                            //for another team
                            while($row = mysql_fetch_row($myteamsresult)) {
                              if(isset($userid) && $row[0]==$userid ){ 
                                    $iAmOnTeamPlaying = 1; 
                               }
                            }

							$playerOneArray = mysql_fetch_array($needpartnerresult);
							$playerTwoArray = mysql_fetch_array($needpartnerresult);
							
							/*The player is in need of a partner if one of the two following conditions
								are met.  
							*/
							if( $playerOneArray['usertype']=="0" && $playerOneArray['userid'] == get_userid()
							|| $playerTwoArray['usertype']=="0" && $playerTwoArray['userid'] == get_userid() ){
								if( isDebugEnabled(1) ) logMessage("\tThe current guy needs a partner");		
								$iNeedAPartner = 1;
							}
							
	                         if( $playerOneArray['userid']=="0" && $playerOneArray['usertype']=="0" && $playerTwoArray['usertype']=="0"){
	                         	if( isDebugEnabled(1) ) logMessage("\tNeed three players, only one guy signed up");		
	                         	$needThreePlayers = 1;
	                         }
	                          // Check to see if reservation is made looking for another team
	                          if($playerOneArray['userid']=="0" && $playerOneArray['usertype']=="0" ){
	                          	if( isDebugEnabled(1) ) logMessage("\tNeed two players for a team");		
	                          	$needTeamval = 1;
	                          }
	                          //Check to see if reservation is made looking for one more person.
	                          if($playerOneArray['usertype']=="0" && $playerOneArray['userid']!="0" && $playerTwoArray['usertype']=="1"){
	                          	if( isDebugEnabled(1) ) logMessage("\tOnly need one player");		
	                          	$needonemore = 1;
	                          }
	                          if($playerOneArray['usertype']=="0" 
	                          		&& $playerOneArray['userid']!=0 
	                          		&& $playerTwoArray['usertype']=="1"
	                          		&& isCurrentUserOnTeam($playerTwoArray['userid'])){
	                             if( isDebugEnabled(1) ) logMessage("\tI have a partner already and only one guy is on opposing team");	
	                          	$iAmOnTeamPlayingOne = 1;
	                          }
	                          if($playerOneArray['usertype']=="0" && $playerOneArray['userid']!=0 && $playerTwoArray['usertype']=="0" && $playerTwoArray['userid']!=0){
	                          	if( isDebugEnabled(1) ) logMessage("\tNeed two players, each on seperate teams");					
	                          	$needTwoPlayers = 1;
	                          	
	                          }
                             
                               /**
                                * The following routine checks the variables as set above.  Order is
                                * important as more specific critiria is checked first, for example
                                * needteam is checked after needthreeplayers as needthreeplayers
                                * is more specific.
                                */
                               
                               //If this is a front desk user and one or more players are needed cancel the court
                               if( db_num_rows($needpartnerresult) > 0 && (get_roleid()=="2" || get_roleid()=="4") ){
                                   
                                    include($_SESSION["CFG"]["templatedir"]."/court_cancelation_form.php");
                               }
                              //Check to see if user is on a team agains tsomeone whose partner
                              //has pulled out.  only give the option of canceling the entire resrvation.
                               elseif(isset($iAmOnTeamPlayingOne)){
                                     include($_SESSION["CFG"]["templatedir"]."/court_cancelation_form.php");
                               }
                               elseif(isset($iAmOnTeamPlaying)){
                               		include($_SESSION["CFG"]["templatedir"]."/court_cancelation_form.php");
                               }
                               	// If three people are needed and the user is the one who is looking
                               	elseif(isset($needThreePlayers) && $iNeedAPartner){
                               		include($_SESSION["CFG"]["templatedir"]."/court_cancelation_form.php");
                               	}
                               //If three people are needed
                               	elseif( isset($needThreePlayers) ){
                               		include($_SESSION["CFG"]["templatedir"]."/reservation_doublesplayer_and_team_wanted_form.php");
                               	}
                               //Reservation is in need of another team
                               elseif( isset($needTeamval) ){
                                  include($_SESSION["CFG"]["templatedir"]."/reservation_doubles_wanted_form.php");
                                }
                                //This person is looking for a partner
                                elseif ( isset($iNeedAPartner) ){
                                	 include($_SESSION["CFG"]["templatedir"]."/court_cancelation_form.php");
                                }
                                //If its just one other person needed
                                 elseif( isset($needonemore) ){
	                                  include($_SESSION["CFG"]["templatedir"]."/reservation_doublesplayer_wanted_form.php");
	                             }
                          
                             	//If two people are needed (on different teams)
                           		elseif( isset($needTwoPlayers)  ){	
                           			include($_SESSION["CFG"]["templatedir"]."/reservation_doublesplayers_wanted_form.php");
                           		}
                               else{
                                    //Just display a plain old reservation page
                                   include($_SESSION["CFG"]["templatedir"]."/reservation_doubles_frontdesk_form.php");
                                    
                               }



                 }

                 //Display Event Reservation Form
                 elseif($courtchoice=="event"){
                         include($_SESSION["CFG"]["templatedir"]."/reservation_event_form.php");
                 }

include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */


        $errors = new Object;
        $msg = "";
        $time = $frm['time'];
        $partnerId = chop($frm["partnerid"]);
        
        if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form(): ");

        //Check to see if this is an event, if it is we don't need partner or opponent
        if ($frm['courttype'] != "event"){
		

        	// Making sure that this reservation is unique.  There is a possiability that a person is allowed to make a reservation
		  	 // by using a old reservation scheudle (ie. one that is not refreshed).  A reservation id should not exist on new reservations
		  		
  		     //First we have to get the reservationid
		      $residquery = "SELECT tblReservations.reservationid
		                             FROM tblReservations
		                             WHERE tblReservations.courtid='$frm[courtid]'
		                             AND tblReservations.time='$frm[time]'
									 AND tblReservations.enddate IS NULL";
		
		      $residresult =  db_query($residquery);
		
		      //this is just a way to know if this is a new reservation.
		      $reservation = mysql_num_rows($residresult);
		
		      $resArray = mysql_fetch_array($residresult);
		      $residval =  $resArray['reservationid'];
	
		        	// Make sure that no one reserved the court in the meantime
		           if( !empty($residval) && $frm['opponent']!="fromdoublesplayerwform"
		                          && $frm['opponent']!="fromdoublespwandtwform"
		                          && $frm['opponent']!="fromdoublespwandpwform"
		                          && $frm['opponent']!="frompwform"
		                          && $frm['opponent']!="fromdoublespwform"){
					  	 $msg = "Sorry, somebody out there just reserved this court.  Tough luck, buddy. ";
					  	 $backtopage = $_SESSION["CFG"]["wwwroot"]."/clubs/".get_sitecode()."/index.php?daysahead=". gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ;
					  	 
					  } 
		        	
					  // Validate Scheduling Policies(Only validate scheduing policies for plaing users (not for administrators))
		        	if(get_roleid()==1 ){
		  			  	$msg = validateSchedulePolicies($frm['courtid'], $frm['time'],  $frm['opponent']);
		  			  	
		  			  	if( !empty($msg) ) {
		  			  		return $msg;
		  			  	}
		        		
		  			  }
					 
					// Validate Skill Range Policies (Only validate scheduing policies for plaing users (not for administrators))
					// Only validate upon signing up with someone for the first time
		            if(get_roleid()==1
		                  		  &&  $frm['partner']=="frompwform"){
		                          	
		                          // When http var 'guylookingformatch' is set then we are coming in from
		                          // the singles pwform.  If this is the case validate this user, instead of opponent
								  if( isset($frm['guylookingformatch'])){
								  	$otherguy = $frm['guylookingformatch'];
								  }else{
		                          	$otherguy = $frm['opponent'];
								  }
								  
								  
								 if( isDebugEnabled(1) ) logMessage("court_reservation.validate: Court Type:  ". $frm['courttype']);
	 
		                         if( ! validateSkillPolicies($otherguy, get_userid(), $frm['courtid'], $frm['courttype'], $frm['time']  ) ){
		                         	 return "A skill range policy is preventing you from reserving this court with this opponent.";
		                         }
		                        
		             }
                   
				   //for validating reservation_doublesplayer_and_team_wanted_form
				   if( isset($frm['opponent']) 
				   		&& $frm['opponent']=="fromdoublespwandtwform" 
				   		&& empty($frm['partner']) 
				   		&& !isset($frm["playwith"])){
				   		$errors->partner = true;
                       $msg .= "You did not specify your partner.";
				   	
				   }
				  //for validating reservation_doublesplayer_and_team_wanted_form
				   elseif( isset($frm['opponent']) && $frm['opponent']=="fromdoublespwandtwform" && $frm['userid']==$frm['partner']  ){
				   		$errors->partner = true;
                       $msg .= "The person you want to be partners with is already in the reservation.";
				   	
				   }
				   //Make sure that when signing up with a partner with just one other person in the reservation, that they
				   //select the name from the list (no guest reservations)
				   
				   elseif($frm['opponent'] == "fromdoublespwandtwform"
				   		&& isGuestPlayer($frm['partner'],$frm['partnername'])){
				   	 $msg .= "It appears that you did not select your partner from the dropdown menu.  Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
				   }
				   
                  //If this is is made as a buddy reservation make sure that this person is in fact....a buddy
                   elseif ( isset($frm['opponent']) && isset($frm['matchtype']) && $frm['matchtype']==3 && $frm['opponent']=="frompwform"){
						
                   	if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form(): Validating Singles Buddy Reservation.");
                            
                   	if(!amIaBuddyOf($frm["guylookingformatch"])) {

                               $fullnameResult = db_query("SELECT firstname, lastname from tblUsers WHERE userid='$frm[guylookingformatch]'");
                               $buddyArray = mysql_fetch_array($fullnameResult);
                               $msg .= "I am sorry but $buddyArray[firstname] $buddyArray[lastname] is looking for a match with a buddy.";

                               }
                    }
                    //If you say that you are going to specify a partner you have to specify a partner.
                     elseif ($frm["usertype"]== "nonfrontdesk" 
                     		&& $frm["courttype"]== "doubles"
                     		&& empty($frm["name"]) 
                     		&& !isset($frm["playwith"])
                     		&& ( $frm["whichone"]== "lookformatchwithpartner"||
                     			 $frm["whichone"]== "versesPlayer")) {
                       $errors->partner = true;
                       $msg .= "You did not specify your partner.";
                     }
                     
                     // For regular players signing up with a partner, 
                     elseif ($frm["opponent"]== "fromdoublespwform" ){
                     	
                     	if(isDebugEnabled(1) ) logMessage("court_reservation.validate_form: Validating the fromdoublespwform form");

                     	// make sure they select the partner name from the list
                     	if($frm["partnername"] != "" && empty( $partnerId ) && get_roleid()==1){
                     			$msg .= "It appears that you did not select your partner from the dropdown menu.  Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
                     	} 

                     	
                     	
                     	$teamThatsAlreadyPlaying = getUserIdsForTeamId($frm["userid"]);
                     	
                     	if(isDebugEnabled(1) ) logMessage("court_reservation.validate_form: Team is $teamThatsAlreadyPlaying[0],$teamThatsAlreadyPlaying[1],$partnerId.");
                     	
                     	//make sure they aren't signing up with someone already in the reservation
                     	if( $teamThatsAlreadyPlaying[0] == $partnerId || $teamThatsAlreadyPlaying[1] == $partnerId  ){

                     		$msg .= "It looks like ". getFullNameForUserId($partnerId) ." is already playing.  Try picking someone else.";
                     	}
                     	
                     	
                     }
                     
                     /*
                     Ensure if the parter is not specified that at least both players in the opposing team are.
                     */
                     elseif ($frm['partner']=="" 
                     && $frm['opponentplayer2']==""
                     && ( $frm["whichone"]== "lookformatchwithpartner"||
                     			 $frm["whichone"]== "versesPlayer")){
                            $errors->partner = true;
                            $msg .= "Please specify a partner if opposing team is in need of another player.";
                     }
                    elseif (isset($frm['matchtype']) && $frm['matchtype']==3 && $frm['opponent']=="fromdoublespwform"){

							if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form(): Validating Buddy Reservation.");
                            
                             $iHaveABuddy = FALSE;
                             $fullNameSearchQuery = "SELECT tblkpTeams.userid, tblUsers.firstname, tblUsers.lastname
                                                     FROM tblUsers INNER JOIN tblkpTeams ON tblUsers.userid = tblkpTeams.userid
                                                     WHERE (((tblkpTeams.teamid)=$userid))";


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
                   //If this is is made as a box league make sure that both are in a box

                   elseif (isset($frm['matchtype']) && isset($frm['opponent']) && $frm['matchtype']==1 && $frm["opponent"]!="")  {


						if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form(): Validating Box League");
                         //Do box league validation for FRONTDESK USERS
                         if(get_roleid()==4 || get_roleid()==2){


                                          $boxid = getBoxIdForUser($frm["playeroneid"]);
                                          if($boxid>0){
                                             if(isBoxExpired($frm["time"],$boxid)){
                                                $msg .= "We are sorry but by the time these guys end up playing this match the box league will be done.";
                                             }
                                         }
                                          //check that at least one of the two players is a box player. (ie one can be looking for a match)
                                           if(empty($frm["playeroneid"]) && empty($frm["playertwoid"])){
                                               $msg .= "$frm[playeronename] and $frm[playertwoname] are not in a box league together.";
                                           }
                                           //Check that if player one is specified that he is in box
                                           if( !empty($frm["playeroneid"]) && !is_inabox($frm["courtid"], $frm["playeroneid"])){
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

                         else{

                         //Do box league validation for PLAYERS (non front desk)

                                          $boxid = getBoxIdForUser(get_userid());
                                          if($boxid>0 && isBoxExpired($frm["time"],$boxid)){
                                                     $msg .= "We are sorry but by the time you end up playing this match your box league will be done.";
                                           }
                                          //Check when signing up for an existing reservation
                                           elseif(isset($frm["guylookingformatch"]) && !are_boxplayers($frm["guylookingformatch"], get_userid())){
                                               $msg .= "You don't seem to be in a box league with this person. ";
                                           }
                                        //Check when making a full reservation

                                           elseif($frm["opponent"]!="frompwform" && !are_boxplayers($frm["opponent"], get_userid())){
                                               $msg .= "You don't seem to be in a Box League with this person";
                                           }
                                           elseif($frm["opponent"]!="frompwform" && hasPlayedBoxWith(get_userid(),$frm["opponent"], $boxid)){
                                           $msg .= "Nice try, but you either are scheduled to play or have already played this person in this box. ";
                                           }
                        }
                    }

                   //whichone is set to lookformatchwithpartner in the doubles court reservation
                   elseif($frm['courttype']=="doubles" && isset($frm['whichone']) && isset($frm['usertype']) && $frm['usertype']=="nonfrontdesk"){

						if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form(): Validating a player making a doubles reservation");
							
							if ( !isSoloReservationEnabled() &&
                           			$frm["partner"]=="" &&
                           			$frm["whichone"]== "lookformatchwithpartner" ) {
                                $errors->opponentplayer1 = true;
                               $msg .= "For doubles, please specify at least two people.";

                           }
                           elseif ($frm["whichone"]== "versesPlayer" 
                           		&& $frm["opponentplayer1"]!= "" 
                           		&& !isClubGuest($frm["opponentplayer1"]) 
                           		&& !isClubMember($frm["opponentplayer1"]) 
                           		&& ($frm["opponentplayer1"] == $frm["opponentplayer2"])) {
                                $errors->opponentplayer1 = true;
                               $msg .= "Please specify different people in the opposing team.";

                           }
                            elseif ($frm["partner"]!="" 
                            	&& $frm["opponentplayer1"]!= "opponentplayer1" 
                            	&& !isClubGuest($frm["partner"]) 
                            	&& !isClubMember($frm["partner"]) 
                            	&&($frm["opponentplayer1"] == $frm["partner"] || $frm["opponentplayer2"] == $frm["partner"])) {
                               $errors->opponentplayer1 = true;
                              
                           }
                           // If a regular player specifies a partner and an opposing team 
                           // make sure that all specified players were selected from the dropdown
                           // Do not allow guest reservations.
                           elseif($frm["whichone"]== "versesPlayer" && 
                           			get_roleid()==1 &&
                           			$frm["partner"]==""){
                           	 $msg .= "It appears that you did not select your partner from the dropdown menu.  Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
                           }
                        	elseif($frm["whichone"]== "versesPlayer" && 
                           			get_roleid()==1 &&
                           			$frm["opponentplayer1"]==""){
                           	 $msg .= "It appears that you did not select the first player in the opposing team from the dropdown menu. Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
                           }
                           elseif($frm["whichone"]== "versesPlayer" && 
                           			get_roleid()==1 &&
                           			$frm["opponentplayer2"]==""){
                           	 $msg .= "It appears that you did not select the second player in the opposing team from the dropdown menu. Remember, you have to actually select one of the names from the dropdown menu, you just can't type it in.";
                           }
 	
                  }

                //Make sure that the front desk is making a valid reservation
                elseif (isset($frm["usertype"]) && $frm["usertype"]=="frontdesk"){
						
						if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form(): Validating Front Desk");

                         if($frm["courttype"]=="singles"){
                                  
                                  if( isDebugEnabled(1) ) logMessage("court_reservation_validate_form(): Validating singles court");
                                  $player = $frm["playeronename"];
                                 
                                   //If a solo rervation by an admin make sure that playername is specified
                                   if($frm["matchtype"]==5 && empty( $frm["playeronename"]) ){
                                   		 $errors->playeronename = true;
                                         $msg .= "Please specify a player for the solo reservation.";
                                   }
                                   //Make sure the Solo reservation is valid
                                   elseif($frm["matchtype"]==5 && empty($frm["pl1change"]) && $frm["playeronename"]=="----------------------------" ){
                                   		$msg .= "Please specify a player for the solo reservation.";
                                   }
                                    //Make sure that they typed something in
                                    elseif ( ($frm["matchtype"]!=5 )
                                    		&& $frm["usertype"]=="frontdesk"  //Made from admin screen (don't check for normal users')
                                    		&& (empty($frm["playeronename"]) 	//Make sure the something has been submitted in the dropdowns
                                    			&& empty($frm["playertwoname"]) ) 
                                    		) {
                                          $errors->playeronename = true;
                                         $msg .= "You did not specify both players.";

                                     }
                                     //Make sure than non club members are not playing each other
                                     elseif ( (!isClubMemberName($frm["playeronename"]) && !isClubGuestName($frm["playeronename"]) )//Club Members and Club Guests can play each other
                                     			&& $frm["matchtype"]!=5 //Not a solo reservation
                                     			&& $frm["playeronename"] == $frm["playertwoname"] //Names aren't the same
                                     			) {
                                         $msg .= "Please specify different players.";
                                     }
                                     //Club Members or Club Guests cannot look for matches
                                     elseif(isClubMemberName($frm["playeronename"]) 
                                     		&& empty( $frm["playertwoname"] )) {
                                     	
                                     	$errors->playeronename = true;
                                         $msg .= "Please register the club member to advertise for this match.";
                                     }
                                      //Nor can club guests 
                                     elseif(isClubGuestName($frm["playeronename"]) 
                                     		&& empty( $frm["playertwoname"] )) {
                                     	
                                     	$errors->playeronename = true;
                                         $msg .= "Please register the club guest to advertise for this match.";
                                     }
                                     // No typed in guest neither
                                     elseif( empty($frm["pl1change"])  //Is the case when typed in
                                     		&& $frm["pl2change"]=="0" ) //Is the case when Looking for match is selected
                                     		{
                                     	
                                     	 $errors->playeronename = true;
                                         $msg .= "Please register the club guest to advertise for this match.";
                                     }
                                     //ONly Program admins can troll for a lesson
                                     elseif(  (!isProgramAdmin( $frm["playeroneid"] ) && !isProgramAdmin( $frm["playertwoid"]))
                                     			&&  $frm["matchtype"]==4 ){
                                     	$errors->playeronename = true;
                                         $msg .= "Lessons can only be given by professional, please.";
                                     }

         
                         }
                           //Check the doubles reservation
                           if($frm["courttype"]=="doubles"){

											if( isDebugEnabled(1) ) logMessage("court_reservation.validate_form(): Validating Doubles Reservation");
											
											if((!empty($frm["playeronename"]) && !empty($frm["playertwoname"]) )  && $frm["playeronename"]==$frm["playertwoname"]){
                                                    $errors->playeronename = true;
                                                    $msg .= "Please specify differnet people in team one.";
                                             }
                                             // Check for duplication in team two
                                             elseif( (!empty($frm["playerthreename"]) && !empty($frm["playerfourname"]) )  && $frm["playerthreename"]==$frm["playerfourname"]){
                                                    $errors->playeronename = true;
                                                    $msg .= "Please specify differnet people in team two.";
                                             }
                                        

                                            
                                            
                                             //Make sure that if there is a guest entered that everything must be filled in.  The way to check this is to find
                                             // a name set where an id is not set
                                             elseif(
                                             		//and any of the feilds are a guest (typed in)
                                             		
                                             			 isGuestPlayer(
                                             			$frm["playeroneid"],
                                             			$frm["playeronename"]
                                             			)){
                                             	
                                             	$errors->playeronename = true;
                                             	 $msg .= "It appears that you did not select all of the players from the dropdown menu. Remember, you have to actually select one of the names from the dropdown menu, you just can't type them in. Give it another shot there, Bucko.";
                                             	
                                             }
                                             // Check player 2
                                             elseif(
                                             		//and any of the feilds are a guest (typed in)
                                             		
                                             			 isGuestPlayer(
                                             			$frm["playertwoid"],
                                             			$frm["playertwoname"]
                                             			)){
                                             	
                                             	$errors->playertwoname = true;
                                             	 $msg .= "It appears that you did not select all of the players from the dropdown menu. Remember, you have to actually select one of the names from the dropdown menu, you just can't type them in. Give it another shot there, Buckaroo.";
                                             	
                                             }
                                             // Check player 3
                                             elseif(
                                             		//and any of the feilds are a guest (typed in)
                                             		
                                             			 isGuestPlayer(
                                             			$frm["playerthreeid"],
                                             			$frm["playerthreename"]
                                             			)){
                                             	
                                             	$errors->playerthreename = true;
                                             	 $msg .= "It appears that you did not select all of the players from the dropdown menu. Remember, you have to actually select one of the names from the dropdown menu, you just can't type them in. Give it another shot there, Captain.";
                                             	
                                             }
                                             // Check player 4
                                             elseif(
                                             		//and any of the feilds are a guest (typed in)
                                             		
                                             			 isGuestPlayer(
                                             			$frm["playerfourid"],
                                             			$frm["playerfourname"]
                                             			)){
                                             	
                                             	$errors->playerfourname = true;
                                             	 $msg .= "It appears that you did not select all of the players from the dropdown menu. Remember, you have to actually select one of the names from the dropdown menu, you just can't type them in. Give it another shot there bucko.";
                                             	
                                             }
                                             
                                              //Make sure that if player three and four are left empty that ids one or two are set (that team one is no guest)
                                             elseif( empty($frm["playerthreename"]) 
                                             			&& empty($frm["playerfourname"])
                                             			&& ( empty($frm["playeroneid"])
                                             			&& empty($frm["playertwoid"]) )
                                              ){
                                             	$errors->playeronename = true;
                                             	
                                             	$msg .= "You have to put at least one name in.  You're not giving me a lot to work with here.";
                                             }
                                             
                                             
                                             
                                              //Check for player 1 playing on both teams
                                             elseif( $frm["playeroneid"]==$frm["playerthreeid"] 
                                             		|| $frm["playeroneid"]==$frm["playerfourid"]
                                             	){
                                                    $errors->playeronename = true;
                                                    $msg .= "This guy is on both teams";
                                             }
                                             //Check for player 2 playing on both teams
                                             elseif( !empty($frm["playertwoname"]) && !isClubGuestName($frm["playertwoname"])
                                             && (
                                             		$frm["playertwoname"]==$frm["playerthreename"] 
                                             		|| $frm["playertwoname"]==$frm["playerfourname"])
                                             	){
                                                    
                                                    $errors->playeronename = true;
                                                    $msg .= "$frm[playertwoname] is on both teams.";
                                             }
                                             //Check for player 3 playing on both teams
                                            elseif(!empty($frm["playerthreename"]) && !isClubGuestName($frm["playerthreename"]) 
                                            && (
                                            	$frm["playerthreename"]==$frm["playeronename"] 
                                            	|| $frm["playerthreename"]==$frm["playertwoname"])
                                            ){
                                                    
                                                    $errors->playerthreename = true;
                                                    $msg .= "$frm[playerthreename] is on both teams....";
                                             }
                                              //Check for player 4 playing on both teams
                                            elseif(!empty($frm["playerfourname"]) && !isClubGuestName($frm["playerfourname"])
                                            && (
                                            	$frm["playerfourname"]==$frm["playeronename"] 
                                            	|| $frm["playerfourname"]==$frm["playertwoname"])
                                            ){
                                                    $errors->playerfourname = true;
                                                    $msg .= "$frm[playerfourname] is on both teams.";
                                             }
                           }

                }

         }
      else{

            if (empty($frm["eventid"])) {
                      $errors->event = true;
                      $msg .= "You did not specify an event. ";
               }
            elseif (empty($frm["repeat"])) {
                      $errors->repeat = true;
                      $msg .= "You did not specify the repeat interval. ";
               }
            elseif ($frm["repeat"] != "norepeat" && empty($frm["duration"])) {
                      $errors->duration = true;
                      $msg .= "You did not specify the duration interval. ";
               }
         }


        return $msg;
}


/**
 * 
 * Make a court reservation
 */
function insert_reservation(&$frm) {

		if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation");

		if ($frm['courttype'] == "event"){
				
				makeEventReservation($frm);

		 }
		else{



/* Update old reservation */
/* First thing we do is check to see if this was made from a
   players wanted form if so will we update the court appropriately. */

     //First we have to get the reservationid
      $residquery = "SELECT tblReservations.reservationid,tblReservations.matchtype
                             FROM tblReservations
                             WHERE (((tblReservations.courtid)='$frm[courtid]')
                             AND ((tblReservations.time)='$frm[time]'))
							 AND tblReservations.enddate IS NULL";

      $residresult =  db_query($residquery);

      //this is just a way to know if this is a new reservation.
      $reservation = mysql_num_rows($residresult);

      $resArray = mysql_fetch_array($residresult);
      $residval =  $resArray['reservationid'];
      $matchtype = $resArray['matchtype'];


   if ($frm['partner']=="frompwform"){

	/*
	The PA or CA can make update a reservation on someone elses behalf.  If
	they do the opponent http post var will contain the userid, when its just a 
	player updating a reservation (one where a player is looking for a match), the
	http post var named opponent will conain frompwform.  This is what this chuck of
	code does.
	*/
	
	if($frm['opponent']=='frompwform'){
		$opponent = get_userid();
	}
	else{
		$opponent = $frm['opponent'];
	}
	
      //Get the opponent id
      $opponentidquery = "SELECT userid
                     FROM tblkpUserReservations
                     WHERE reservationid = $residval AND userid != 0";

      $opponentidresult =  db_query($opponentidquery);
      $opponentidval =  mysql_result($opponentidresult,0);

      // Now we just need to update that reservation
      $qid = db_query("UPDATE tblkpUserReservations SET userid =$opponent
                       WHERE reservationid = $residval
                       AND userid = 0");



	$qid1 = db_query("UPDATE tblReservations
							  SET lastmodifier = ".get_userid()."
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
 elseif($frm['opponent']=="fromdoublespwandtwform"){
 	
 	if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation: A Player is updating reservation where one player is looking for a partner and a team is needed too (fromdoublespwandtwform)");
	
 	
 	//Get Court Type for making the team
 	$courttypefordoublesquery = "SELECT tblCourtType.courttypeid
                                     FROM (tblCourts
                                     INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid)
                                     WHERE (((tblCourts.courtid)='$frm[courtid]')
                                     AND ((tblCourtType.reservationtype)=2))";

        $courttypefordoublesresult = db_query($courttypefordoublesquery);
        $courttypefordoublesarray = mysql_fetch_array($courttypefordoublesresult);
        
      //Get the reservationID for making the reservation
      $residquery = "SELECT tblReservations.reservationid
                             FROM tblReservations
                             WHERE (((tblReservations.courtid)='$frm[courtid]')
                             AND ((tblReservations.time)='$frm[time]'))
							 AND tblReservations.enddate IS NULL";

      $residresult =  db_query($residquery);
      $residval =  mysql_result($residresult,0);
 	
 	
 	$qid1 = db_query("UPDATE tblReservations
							  SET lastmodifier = ".get_userid()."
                              WHERE reservationid = $residval");
                              
 	//Playwith is variable only used on this page for indicating who the player wants
 	//to play with, either the person who made the reservation or someone else.
 	if($frm['playwith']=="1"){
		
       $currentTeamID = getTeamIDForCurrentUser($courttypefordoublesarray[0],$frm['userid']);
       
       //Replace the individual with the new team
       $qid = db_query("UPDATE tblkpUserReservations
                       SET userid =$currentTeamID,
                       usertype = 1
                       WHERE reservationid = $residval
                       AND userid = $frm[userid]");
     	
 	}
 	//Sign up with a partner of this persons choosing
 	else{
 		 

 		 $currentTeamID = getTeamIDForCurrentUser($courttypefordoublesarray[0],$frm['partner']);
 		 
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
 // reservation_doublesplayer_and_team_wanted_form.php
 elseif($frm['opponent']=="fromdoublespwandpwform"){
 	
 	if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation: A player is updating reservation where two players were looking for a partner (fromdoublespwandpwform)");
	
 	
 	//Get Court Type for making the team
 	$courttypefordoublesquery = "SELECT tblCourtType.courttypeid
                                     FROM (tblCourts
                                     INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid)
                                     WHERE (((tblCourts.courtid)='$frm[courtid]')
                                     AND ((tblCourtType.reservationtype)=2))";

        $courttypefordoublesresult = db_query($courttypefordoublesquery);
        $courttypefordoublesarray = mysql_fetch_array($courttypefordoublesresult);
        
      
      $currentTeamID = getTeamIDForCurrentUser($courttypefordoublesarray[0],$frm['partner']);
 		 
		 //Update the last modifier
		 $qid1 = db_query("UPDATE tblReservations
							  SET lastmodifier = ".get_userid()."
	                          WHERE reservationid = $residval");
 		 
 		 // Now we just need to update that reservation
      	$qid = db_query("UPDATE tblkpUserReservations
                       SET userid =$currentTeamID,
                       usertype = 1
                       WHERE reservationid = $residval
                       AND userid = $frm[partner]
					   AND usertype = 0");
      
 	return $residval;
 	 
 }
 //Check to see if we are to add a team to a reservation
 elseif($frm['opponent']=="fromdoublespwform"){

	if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation: A Player is updating reservation where a team was looking for another team (fromdoublespwform)");
	
    //Get the tblcourttype id for this court
        $courttypefordoublesquery = "SELECT tblCourtType.courttypeid
                                     FROM (tblCourts
                                     INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid)
                                     WHERE (((tblCourts.courtid)='$frm[courtid]')
                                     AND ((tblCourtType.reservationtype)=2))";

        $courttypefordoublesresult = db_query($courttypefordoublesquery);
        $courttypefordoublesarray = mysql_fetch_array($courttypefordoublesresult);


      //First we have to get the reservationid
      $residquery = "SELECT tblReservations.reservationid
                             FROM tblReservations
                             WHERE tblReservations.courtid='$frm[courtid]'
                             AND tblReservations.time='$frm[time]'
							 AND tblReservations.enddate is NULL";
	
      $residresult =  db_query($residquery);
      $residval =  mysql_result($residresult,0);
       
       if( empty($frm['partnerid']) ){
       	
       	if( isDebugEnabled(1) ) logMessage("\tThe partner is not set, just adding the user");
	
       	 // Now we just need to update that reservation
          $qid = db_query("UPDATE tblkpUserReservations
                       SET userid = ".get_userid(). ",usertype = 0
                       WHERE reservationid = $residval
                       AND userid = 0");
                       
       
       }else{
       	
       	if( isDebugEnabled(1) ) logMessage("\tThe partner is set, adding the team.");
       	
       	 $currentTeamID = getTeamIDForCurrentUser($courttypefordoublesarray[0],$frm['partnerid']);
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
 elseif($frm['opponent']=="fromdoublesplayerwform"){

		if( isDebugEnabled(1) ) logMessage("court_reservation.insert_reservation: A Player is updating reservation where a player was looking for a partner (fromdoublesplayerwform)");
	

         //Get the tblcourttype id for this court
               $courttypefordoublesquery = "SELECT tblCourtType.courttypeid
                                            FROM (tblCourts
                                            INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid)
                                            WHERE (((tblCourts.courtid)='$frm[courtid]')
                                            AND ((tblCourtType.reservationtype)=2))";

        $courttypefordoublesresult = db_query($courttypefordoublesquery);
        $courttypefordoublesarray = mysql_fetch_array($courttypefordoublesresult);


       $currentTeamID = getTeamIDForCurrentUser($courttypefordoublesarray[0],$frm['partner']);

       //First we have to get the reservationid
      $residquery = "SELECT tblReservations.reservationid
                             FROM tblReservations
                             WHERE tblReservations.courtid='$frm[courtid]'
                             AND tblReservations.time='$frm[time]'
							 AND tblReservations.enddate is NULL";

      $residresult =  db_query($residquery);
      $residval =  mysql_result($residresult,0);

	  //Update the last modifier
	  $qid1 = db_query("UPDATE tblReservations
						  SET lastmodifier = ".get_userid()."
                          WHERE reservationid = $residval");

      //UPdate the tblkpUserReservation Table
      $qid = db_query("UPDATE tblkpUserReservations SET userid =$currentTeamID
                       WHERE reservationid = $residval
                       AND userid = $frm[partner]
                       AND usertype = 0");

      //Now set the usertype to reflect a team reservation
      $qid = db_query("UPDATE tblkpUserReservations SET usertype =1
                       WHERE reservationid = $residval
                       AND userid = $currentTeamID");
                       
      //Send out update emails
	  confirm_doubles($residval, false);
	  
	  return $residval;

 }

 else{


/* Make new reservation */

		$guesttype = 0;
		
		/*
		 * If there is a player name without an id, this is a guest.  If there is one guest found
		 * the reservation is marked as a guest reservation (front desk)
		 */
        
        if( $frm['courttype']=="singles" 
        		&& (
        		empty($frm['playeroneid']) && !empty($frm['playeronename']) 
        		|| empty($frm['playertwoid']) && !empty($frm['playertwoname']) 
        		) 

        		){
			
            $guesttype = 1;
        }
        // Normal players can also make guest reservations
        elseif( $frm['courttype']=="singles" 
        		&& (
        		empty($frm['opponent']) && !empty($frm['name']) 
        		) 

        		){
        	$guesttype = 1;
        }
        
        /* Or This is a guest reservation if its a a doubles reservation and
         */
        elseif( $frm['courttype']=="doubles" 
        && $frm['usertype']=="frontdesk"
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
         elseif( $frm['courttype']=="doubles" 
        && $frm['usertype']=="nonfrontdesk"
        && 
        	// if ther is a player name with no player id on any of the players
        	isGuestDoublesReservation( 
        			$frm['partner'],  
        			$frm['name'], 
        			$frm['opponentplayer1'], 
        			$frm['opponentname1'], 
        			$frm['opponentplayer2'], 
        			$frm['opponentname2']) 
        	 
        	 ){
            $guesttype = 1;
        }
  
	      /*
	       * For solo reservations, set this guesttype to 0 where playeroneid is set 
	       */
	       
			if( ($frm['matchtype']==5 &&  !empty($frm['playeroneid']) )) {
	      	  $guesttype = 0;
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
	
	
	        $resresult =  db_query($resquery);
	
	        //Now we need to get the reservationid.  (This is what we just inserted )
	        $residquery = "SELECT reservationid FROM tblReservations WHERE courtid= '$frm[courtid]'
	                       AND time='$frm[time]' AND enddate IS NULL";
	
	         $residresult =  db_query($residquery);
	         $residvarresult = db_fetch_object($residresult);


	           if ($frm['courttype']=="doubles"){   
	             makeDoublesReservation($frm ,$guesttype, $residvarresult->reservationid);
	           }
	           elseif( $frm['matchtype']=="5" ){   
	         		makeSoloReservation($frm, $residvarresult->reservationid);
	           }
		       else{
		       		makeSinglesReservation($frm ,$guesttype, $residvarresult->reservationid);
		       }


	}
}

return $residvarresult->reservationid;
}




/**
 * Makes an event reservations
 */
function makeEventReservation(&$frm){
	
	 if( isDebugEnabled(1) ) logMessage("Making an Event Reservation");
	
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
	
     	//if coming from the admin page
     	if( $frm['usertype']=="frontdesk"  ){
     		
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
     	else{
				$userid = get_userid();	

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
          $courttypefordoublesquery = "SELECT tblCourtType.courttypeid
                                       FROM (tblCourts
                                       INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid)
                                       WHERE (((tblCourts.courtid)='$frm[courtid]'))";
          $courttypefordoublesresult = db_query($courttypefordoublesquery);
          $courttypefordoublesarray = mysql_fetch_array($courttypefordoublesresult);

           // Now update the users (either front guest type or regular)

               if($frm['usertype']=="frontdesk"){
                       
                       		if( isDebugEnabled(1) ) logMessage("\tUsertype: Frontdesk");
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
			                        	
			                        	$teamid1 = getTeamIDForPlayers($courttypefordoublesarray[0],$frm['playeroneid'],$frm['playertwoid']);
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
				                        	$teamid2 = getTeamIDForPlayers($courttypefordoublesarray[0],$frm['playerthreeid'],$frm['playerfourid']);
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

                             $query = "INSERT INTO tblkpGuestReservations (
                                           reservationid, name
                                           ) VALUES (
                                           '$reservationid'
                                           ,'$frm[playeronename] - $frm[playertwoname]')";

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
               } //  if not front desk insert regular reservation
               else{

						if( isDebugEnabled(1) ) logMessage("\tUsertype: Something other than frontdesk");
						
						//Just put in the player names
						if($guesttype == 1){
							
							if( isDebugEnabled(1) ) logMessage("\tGuesttype: 1");
							
							 $query = "INSERT INTO tblkpGuestReservations (
                                           reservationid, name
                                           ) VALUES (
                                           '$reservationid'
                                           ,'$frm[name] - ". getFullNameForUserId( get_userid() )."')";

                             // run the query on the database
                            $result = db_query($query);

                             $query = "INSERT INTO tblkpGuestReservations (
                                                 reservationid, name
                                                 ) VALUES (
                                                 '$reservationid'
                                                 ,'$frm[opponentname1] - $frm[opponentname2]')";

                             // run the query on the database
                             $result = db_query($query);
						
						}
						
						//Not a guest Reservation, 
						else{

	                              if( isDebugEnabled(1) ) logMessage("\tGuesttype: 0");
	                              
	                              // If they choose to make the doubles reservation with just themselves, then
	                              //just add them, don't try to make a team'
	                              
	                              
	                               if(  $frm['whichone']=="soloreservation" ){ 
	                               		
	                               		
			                              $query = "INSERT INTO tblkpUserReservations (
			                                        reservationid, userid, usertype
			                                        ) VALUES (
			                                           $reservationid
			                                              ,".get_userid()."
			                                              ,0)";
			
			
			                              // run the query on the database
			                              $result = db_query($query);
	                               
	                               }else{
	                              
			                              //Get the team id for the current user and the partner
			                              $currentTeamID = getTeamIDForCurrentUser($courttypefordoublesarray[0],$frm['partner']);
			
			
			
			                              $query = "INSERT INTO tblkpUserReservations (
			                                        reservationid, userid, usertype
			                                        ) VALUES (
			                                           $reservationid
			                                              ,$currentTeamID
			                                              ,1)";
			
			
			                              // run the query on the database
			                              $result = db_query($query);
	
	                               }
	                               
							
		                           // Add the second entry of the reservation
		                           if(  $frm['whichone']=="lookformatchwithpartner" || $frm['whichone']=="soloreservation" ){ //Make reservation without anyone on the other team.
		                            
		                                 $teamtwo_usertype = 0;
		                                 
		                                 // run the query on the database
		                                 $query = "INSERT INTO tblkpUserReservations (
		                                         reservationid, userid, usertype
		                                         ) VALUES (
		                                                   '$reservationid'
		                                                   ,0
		                                                   ,0)";
		
		                                   // run the query on the database
		                                 $result = db_query($query);
		                                 
		
		                          }
		                          
		                          // frm['whichone'] will be set to versesPlayer
		                          else{
		

			                                 if( $frm['opponentplayer2']=="" && $frm['opponentplayer1']!=""  ){
			
			                                 
			                                 $teamtwo_usertype = 0;
			                                 $query = "INSERT INTO tblkpUserReservations (
			                                              reservationid, userid, usertype
			                                              ) VALUES (
			                                                        '$reservationid'
			                                                        ,$frm[opponentplayer1]
			                                                        ,0)";
			
			                                 }
			                                 elseif( $frm['opponentplayer1']=="" && $frm['opponentplayer2']!="" ){
			                                 	
			                                 	  $teamtwo_usertype = 0;
			                                 	 $query = "INSERT INTO tblkpUserReservations (
			                                              reservationid, userid, usertype
			                                              ) VALUES (
			                                                        '$reservationid'
			                                                        ,$frm[opponentplayer2]
			                                                        ,0)";
			                                 }
			                                 // add BOTH specified players to reservation
			                                 else{
			                                       //Get the team id for opponentplayer1 and opponentplayer2
			                                       $opposingTeamID = getTeamIDForPlayers($courttypefordoublesarray[0],$frm['opponentplayer1'], $frm['opponentplayer2']);
													
			                                       $query = "INSERT INTO tblkpUserReservations (
			                                              reservationid, userid, usertype
			                                              ) VALUES (
			                                                        '$reservationid'
			                                                        ,$opposingTeamID
			                                                        ,1)";
			
			                                 }
		
		
		                              // run the query on the database
		                               $result = db_query($query);
		
		
		                          }
		                          
		                            //Send out doubles confirmation for a full reservation when looking for a partner
		                              //we will send out the emails with the reservation details page or something.
		                               //If this is a single confirm as a singles match
				                     if( isset($teamone_usertype) && isset($teamtwo_usertype) ){
				                     	
				                     	 confirm_singles($reservationid, true);
				                     }
				                     else{
				                     	 confirm_doubles($reservationid, true);
				                     }
		                          
		             
		               }

               }
         
}


/**
 * Make a plain old singles reservation.  Thats right, a plain old singles reservation
 * This taks a form object, a guesttype, and the reservationid
 */
function makeSinglesReservation($frm, $guesttype, $reservationid){
	
	
     //Check to see if this is a frontdesk user
      if( $frm['usertype']=="frontdesk" ){

		
        $playerone = $frm['playeroneid'];
        $playertwo = $frm['playertwoid'];
        
        if( isDebugEnabled(1) ) logMessage("playerone: $playerone playertwo $playertwo and guesttype is $guesttype");
        
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
            //We don't have both of the playerids so we are going to book a guest reservation.
                   $query = "INSERT INTO tblkpGuestReservations (
                                    reservationid, name
                                    ) VALUES (
                                              '$reservationid'
                                              ,'$frm[playeronename]')";
                     
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
      // Not a front desk user
      else{

            if( $guesttype == 1){
            //We don't have both of the playerids so we are going to book a guest reservation.
                   $query = "INSERT INTO tblkpGuestReservations (
                                    reservationid, name
                                    ) VALUES (
                                              '$reservationid'
                                              ,'$frm[name]')";

                // run the query on the database
               $result = db_query($query);

             	$query = "INSERT INTO tblkpGuestReservations (
                            reservationid, name
                            ) VALUES (
                                      '$reservationid'
                                      ,'". getFullNameForUserIdWithEscapes( get_userid())."')";
                                      

               // run the query on the database
               $result = db_query($query);
      
            }else{
             
             //this is a singles reservation insert the userid in the userid field
               $query = "INSERT INTO tblkpUserReservations (
                      reservationid, userid, usertype
                      ) VALUES (
                                '$reservationid'
                                ," . get_userid() ."
                                ,0)";

              // run the query on the database
              $result = db_query($query);


                 if($frm['opponent']==""){
                        // run the query on the database
                        $query = "INSERT INTO tblkpUserReservations (
                                reservationid, userid, usertype
                                ) VALUES (
                                          '$reservationid'
                                          ,0
                                          ,0)";

                          // run the query on the database
                        $result = db_query($query);
                        
                        confirm_singles($reservationid, true);
                        
                        

                 } else{
                         $query = "INSERT INTO tblkpUserReservations (
                                reservationid, userid, usertype
                                ) VALUES (
                                          '$reservationid'
                                          ,'$frm[opponent]'
                                          ,0)";


                         // run the query on the database
                        $result = db_query($query);

                         //Update tblBoxHistory if this is a box league reservation
                        if($frm['matchtype']==1){
                                $boxid = getBoxIdForUser(get_userid());
                                $query = "INSERT INTO tblBoxHistory (
                                          boxid, reservationid
                                ) VALUES (
                                          $boxid
                                          ,'$reservationid')";
                                 $result = db_query($query);
                        }


                        //Send out singles confirmation
                        confirm_singles($reservationid, true);

                }
             }

	}

	
}




/*
 * This just sees if one of these is empty
 */
function isDoublesReservationHaveThreeOrMore($playeroneid, $playertwoid, $playerthreeid, $playerfourid){
	
	$counter = 0;
	
	if( !empty($playeroneid))
		 ++$counter;
	if( !empty($playertwoid))
		 ++$counter;
	if( !empty($playerthreeid))
		 ++$counter;
	if( !empty($playerfourid))
		 ++$counter;
		 
	if($counter<3)
		return false;
	else
		return true;
		
}


/*
 * Determines if one of these is NOT set (meaning that its not a full reservation)
 */
function isDoublesReservationFull($playeroneid, $playertwoid, $playerthreeid, $playerfourid){
	
	$counter = 0;
	
	if( !empty($playeroneid))
		 ++$counter;
	if( !empty($playertwoid))
		 ++$counter;
	if( !empty($playerthreeid))
		 ++$counter;
	if( !empty($playerfourid))
		 ++$counter;
		 
	if($counter<4)
		return false;
	else
		return true;
		
}

/*
 * Does a real simple check to see if the court is already reserved.
 */
function isCourtAlreadyReserved($courtid, $time){
	
	 $notReservedQuery = "SELECT reservations.reservationid FROM tblReservations reservations 
							WHERE reservations.courtid = $courtid 
							AND reservations.time = $time
							AND reservations.enddate IS NULL";
							
     $notReservedResult = db_query($notReservedQuery);
     $numberOfReservations = mysql_num_rows($notReservedResult);
     
     if($numberOfReservations < 1){
     	return false;
     }
     else{
     	return true;
     }
		         
		         
}


?>
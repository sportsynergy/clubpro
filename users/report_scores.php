<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/

include("../application.php");
require($_SESSION["CFG"]["libdir"]."/ladderlib.php");

$DOC_TITLE = "Report Scores";

//Set the http variables
$reservationid = $_REQUEST["reservationid"];
$source = $_REQUEST["source"];



require_loginwq();

/* form has been submitted, now reserve court */

if (match_referer() && isset($_POST["submitme"])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);


        if ( empty($errormsg) ) {
            

                include($_SESSION["CFG"]["templatedir"]."/header_yui.php");

					// If this is a box league
                     if($frm['matchtype']==1 ) {

						//Get Box Id
						$query = "SELECT history.boxid 
									FROM tblBoxHistory history 
									WHERE history.reservationid = $frm[reservationid]";
						$result = db_query($query);
						
						if( mysql_num_rows($result) == 1){
							$boxId = mysql_result($result,0);
						}
						
						// if not found, set it in box history (this was changed to a box league 
						// match when it was scored.
						if( empty($boxId) ){
							$boxId = getBoxIdTheseTwoGuysAreInTogether($frm['Player1'], $frm['Player2']);
							$query = "INSERT INTO tblBoxHistory ( boxid, reservationid ) VALUES ( $boxId, $frm[reservationid])";
							db_query($query);
							
						}
						
                         $temparray = array ("boxid" => $boxId);
                         $frm = array_merge ($frm, $temparray);

                         update_ladderscore($frm['score'], $frm['boxid'], $frm['winner'], $frm['Player1'], $frm['Player2'] );
                         update_gamesplayed($frm['Player1'], $frm['Player2'], $boxId);

                     }
                     

                     //Reset (in case it changed)
                     markMatchType($frm['reservationid'],$frm['matchtype']);
                     
                     //Record score
                     record_score($frm, $source);
                     update_streakval($frm);

                $goto = empty($_SESSION["wantsurl"]) ? $_SESSION["CFG"]["wwwroot"] : $_SESSION["wantsurl"];
                include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
                die;
        }
}

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/report_scores_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

        //Find out what type of reservation it is. if its a doubles reservation make sure the
        // current user is in one of the teams. If its a singles reservation make sure that make
        //sure that they are one of the players
        $reservationTypeQuery = "SELECT tblReservations.usertype
                                 FROM tblReservations
                                 WHERE (((tblReservations.reservationid)='$frm[reservationid]'))";

        $reservationTypeResult = db_query($reservationTypeQuery);
        $reservationTypeValue = mysql_result($reservationTypeResult,0);


        //Check singles reservation
        if($reservationTypeValue==0 && (get_userid() != $frm['Player1'] && get_userid() != $frm['Player2']) && get_roleid() == 1){

                $errors->reportscore = true;
                $msg .= "You have to be on one of two people that played to report the score";

        }
        /*Check doubles reservation */
        elseif( $reservationTypeValue==1 && (isCurrentUserOnTeam($frm['Player1'])==0) && (isCurrentUserOnTeam($frm['Player2'])==0) ){

                $errors->reportscore = true;
                $msg .= "You have to be on one of the teams that played to report the score";
        }
        elseif (empty($frm["winner"])) {
                $errors->winner = true;
                $msg .= "You did not specify a winner.";

         }
        elseif (isClubGuest($frm['Player1']) || isClubGuest($frm['Player2']) ){
        	 
        	 if(get_roleid() == 1 ){
        	 	$msg .= "Nice try to boost your ranking, but Club Guest matches can't be scored.  Seriously, instead coming up with new ways to cheat you should be out there hitting rails.";
        	 }else{
        	 	$msg .= "Club Guest matches cannot be scored.";
        	 }
        	  
        }
		elseif( isClubMember($frm['Player1']) || isClubMember($frm['Player2'])){
			   $msg .= "Club Member matches cannot be scored. Stop trying to cheat.";
		}
		elseif( $frm['matchtype']==1 ){
			 
			//When this match type has been changed (after the fact)
			$boxId = getBoxIdTheseTwoGuysAreInTogether($frm['Player1'], $frm['Player2']);
			$boxReservationId = getBoxReservation( $frm['Player1'], $frm['Player2'], $boxId);
 
			 if ( isset($boxReservationId) && $boxReservationId != $frm['reservationid']  ) //And that there isn't another box league reservation w/these two'
			 {
			 	 
			 	 $msg .= "These guys already have a scheduled box league match.";
			 }
			 
			
		}
		// There is a site level setting out there that prevents normal users from recording their score
		elseif( !isSelfScoreEnabled() && get_roleid() == 1 ){
			 $msg .= "Sorry, but it appears that your club administrator is not allowing you to record your score.";
		}
		
        return $msg;
}



?>
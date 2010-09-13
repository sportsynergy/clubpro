<?php


/*
 * $LastChangedRevision: 26 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2006-06-13 09:23:08 -0500 (Tue, 13 Jun 2006) $
 */

include ("../application.php");
require($_SESSION["CFG"]["libdir"]."/ladderlib.php");

$DOC_TITLE = "Record Scores";
$ct = $_REQUEST["ct"];

require_loginwq();


/* form has been submitted, try to create the new user account */
if (match_referer() && isset($_POST['submit'])) {

        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);


          if ($errormsg){
         	 $backtopage = $_SESSION["CFG"]["wwwroot"]."/admin/report_scores.php?ct=$ct";
         	 include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/errorpage.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
         }
         else{
         	
         	 // If this is a challenge match update the club ladder
             if( $frm['matchtype']=="challenge" ){
             	
		             	if( $frm['usertype'] == "0"){
		             		
		             		if( isUserInClubLadder($frm['player1'], $frm['courttype'], get_clubid() ) 
		             			&& isUserInClubLadder($frm['player2'], $frm['courttype'], get_clubid()) ){
		             		
			             		//SEt the winner and loser
			             		$winneruserid = $frm['player1'];
			             		$loseruserid =  $frm['player2'];
			             		
			             		adjustClubLadder($winneruserid,$loseruserid, $frm['courttype'], get_clubid() );
			             		
		             		}
		             		
		             	}
		             	elseif($frm['usertype'] == "1"){
		             		
		             		if( isUserInClubLadder($frm['player1'], $frm['courttype'], get_clubid() ) 
		             			&& isUserInClubLadder($frm['player2'], $frm['courttype'], get_clubid() )
		             			&& isUserInClubLadder($frm['player3'], $frm['courttype'], get_clubid())
		             			&& isUserInClubLadder($frm['player4'], $frm['courttype'], get_clubid())) {
		             				
		             			adjustDoublesClubLadder($frm['player1'],$frm['player2'], $frm['player3'], $frm['player4'], $frm['courttype'], get_clubid() );	
		             		
		             	}
		 
		             }
         	
         	 }
         	 
             $message = record_scores($frm);
             include($_SESSION["CFG"]["templatedir"]."/header.php");
             include($_SESSION["CFG"]["includedir"]."/include_message.php");
             include($_SESSION["CFG"]["templatedir"]."/footer.php");
             die;
       

         }

}

	$singlesCourtTypeDropDown = get_singlesCourtTypesForSite(get_siteid());
	$doublesCourtTypeDropDown = get_doublesCourtTypesForSite(get_siteid());



//Set the default
if(!isset($ct)){
	
	if( mysql_num_rows($singlesCourtTypeDropDown) > 0 ){
		$ct = "singles";
	}

	
}

include($_SESSION["CFG"]["templatedir"]."/header.php");

if($ct=="singles"){
	include($_SESSION["CFG"]["templatedir"]."/report_scores_singles_form.php");
}
else{
	include($_SESSION["CFG"]["templatedir"]."/report_scores_doubles_form.php");
}


include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {

	$errors = new Object;
	$msg = "";

	//Validate Singles
	if( $frm["ct"] == "singles"){
		
		if (empty($frm["player1"])) {
                $msg .= "You did not specify the first player.";
         }elseif (empty($frm["player2"])) {
                $msg .= "You did not specify the second player.";
         }elseif (empty($frm["courttype"])) {
                $msg .= "You did not specify the court type.";
        }elseif($frm["player1"] == $frm["player2"]){
        	   $msg .= "Please specify different players.";
        }
        
        
        //Validate the both player1 have rankings
		elseif (! isUserValidForCourtType( $frm["player1"], $frm["courttype"] ) ){
			 $msg .= getFullNameForUserId($frm["player1"])." is not enabled for ".getCourtTypeName($frm["courttype"]).".";
		}
		
		 //Validate the both player2 have rankings
		elseif ( ! isUserValidForCourtType( $frm["player2"], $frm["courttype"] ) ){
			 $msg .= getFullNameForUserId($frm["player2"])." is not enabled for ".getCourtTypeName($frm["courttype"]).".";
		}
		
		// If this is a box league make sure that both players are in the same box  
		elseif( $frm["matchtype"] == "boxleague" && !are_boxplayers($frm["player1"],$frm["player2"] )){
			$msg .= "It doesn't look like ".$frm["playeronename"]." and ".$frm["playertwoname"]." are in the same box league.";
		}
		
	}
	elseif ( $frm["ct"] == "doubles") {
		
		if (empty($frm["player1"])) {
               $msg .= "You did not specify the first player.";
        }
        elseif (empty($frm["player2"])) {
                $msg .= "You did not specify the second player.";
        }
        elseif (empty($frm["player3"])) {
                $msg .= "You did not specify the third player.";
        }
        elseif (empty($frm["player4"])) {
                $msg .= "You did not specify the fourth player.";
        }
        elseif (empty($frm["courttype"])) {
                $msg .= "You did not specify the court type.";
        }
        //Validate Player One
        elseif($frm["player1"] == $frm["player2"] || $frm["player1"] == $frm["player3"] || $frm["player1"] == $frm["player4"]){
        	   $msg .= "Please specify different players";
        }
        //Validate Player Two
        elseif($frm["player2"] == $frm["player3"] || $frm["player2"] == $frm["player4"] ){
        	   $msg .= "Please specify different players";
        }
         //Validate Player Three and Fourt
        elseif($frm["player3"] == $frm["player4"]  ){
        	   $msg .= "Please specify different players";
        }
        
         //Validate the both player1 have rankings
		elseif ( !isUserValidForCourtType( $frm["player1"], $frm["courttype"] ) ){
			 $msg .= getFullNameForUserId($frm["player1"])." is not enabled for ".getCourtTypeName($frm["courttype"]);
		}
		 //Validate the both player1 have rankings
		elseif (! isUserValidForCourtType( $frm["player2"], $frm["courttype"] ) ){
			 $msg .= getFullNameForUserId($frm["player2"])." is not enabled for ".getCourtTypeName($frm["courttype"]);
		}
		 //Validate the both player1 have rankings
		elseif ( !isUserValidForCourtType( $frm["player3"], $frm["courttype"] ) ){
			 $msg .= getFullNameForUserId($frm["player3"])." is not enabled for ".getCourtTypeName($frm["courttype"]);
		}
		 //Validate the both player1 have rankings
		elseif ( !isUserValidForCourtType( $frm["player4"], $frm["courttype"] ) ){
			 $msg .= getFullNameForUserId($frm["player4"])." is not enabled for ".getCourtTypeName($frm["courttype"]);
		}
		
	}
		
	return $msg;
}

/**
 * Records the scores
 */
function record_scores(&$frm){
	
	
	//Report Scores for doubles
	if( $frm["ct"] == "doubles") {
		
		//Get Winner Team Id
		$winnerTeamId = getTeamIDForPlayers($frm["courttype"], $frm["player1"], $frm["player2"]);
		
		//Get Loser Team Id
		$loserTeamId = getTeamIDForPlayers($frm["courttype"], $frm["player3"], $frm["player4"]);
		
		//Get rankings
		$winnersOldRanking = getUserRankingForUserType($winnerTeamId, $frm["courttype"], 1);
		$losersOldRanking = getUserRankingForUserType($loserTeamId, $frm["courttype"], 1);
		
		//Calculate Rankings
		$rankingArray =  calculateRankings($winnersOldRanking, $losersOldRanking);
		
		$newWinnerRanking = $rankingArray['winner'];
		$newLoserRanking = $rankingArray['loser'];	
		
		//Set rankings
		$winnersUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newWinnerRanking
							WHERE rankings.userid = $winnerTeamId
							AND rankings.courttypeid = ".$frm['courttype']."
							AND rankings.usertype = 1";
							
		db_query($winnersUpdate);
		
		$losersUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newLoserRanking
							WHERE rankings.userid = $loserTeamId
							AND rankings.courttypeid = ".$frm['courttype']."
							AND rankings.usertype = 1";
		
		db_query($losersUpdate);
		
		report_scores_doubles_simple($winnerTeamId, $loserTeamId, $winnersOldRanking, $newWinnerRanking, $losersOldRanking, $newLoserRanking, $frm["score"]);
		
		
		return  "<font class=bigbanner> Yipeeee for ".getFullNamesForTeamId($winnerTeamId)."!!!</font><br/><br/>".
				getFullNamesForTeamId($winnerTeamId)." ranking climbed from $winnersOldRanking to $newWinnerRanking<br>
						".getFullNamesForTeamId($loserTeamId)." ranking fell from $losersOldRanking to $newLoserRanking";
		
		
	}
	//Report Scores for singles
	elseif( $frm["ct"] == "singles" ){
		
		if(isDebugEnabled(1) ) logMessage("report_scores.record_scores: recording scores for a singles reservation with match type ". $frm['matchtype']);
		
		//Get rankings
		$winnersOldRanking = getUserRankingForUserType($frm["player1"], $frm["courttype"], 0);
		$losersOldRanking = getUserRankingForUserType($frm["player2"], $frm["courttype"], 0);
		
		
		//Calculate Rankings
		$rankingArray =  calculateRankings($winnersOldRanking, $losersOldRanking);
		
		//if the match type is challenge then run run this calculation twice
		if( $frm['matchtype'] == "challenge"){
			$rankingArray =  calculateRankings($rankingArray['winner'], $rankingArray['loser']);
		}
		
		
		$newWinnerRanking = $rankingArray['winner'];
		$newLoserRanking = $rankingArray['loser'];	
		
		//Set rankings
		$winnersUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newWinnerRanking
							WHERE rankings.userid = ".$frm['player1']."
							AND rankings.courttypeid = ".$frm['courttype']."
							AND rankings.usertype = 0";
							
		db_query($winnersUpdate);
		
		$losersUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newLoserRanking
							WHERE rankings.userid = ".$frm['player2']."
							AND rankings.courttypeid = ".$frm['courttype']."
							AND rankings.usertype = 0";
		
		db_query($losersUpdate);
		
		//if this is a box league update the scores
		if($frm['matchtype']=="boxleague"){
		 
		 
		 $boxId = getBoxIdTheseTwoGuysAreInTogether($frm['player1'], $frm['player2']);
		
		 if(isDebugEnabled(1) ) logMessage("report_scores.record_scores: This is a box league reservation for box $boxId");
		 update_ladderscore($frm['score'], $boxId , $frm['player1'], $frm['player1'], $frm['player2'] );
         update_gamesplayed($frm['player1'], $frm['player2'], $boxId);
		
		}
		
		
		
		// send out the email
		report_scores_singles_simple($frm["player1"], $frm["player2"], $winnersOldRanking, $newWinnerRanking, $losersOldRanking, $newLoserRanking, $frm["score"]);
		
		return  "<font class=bigbanner> Yay for ".getFullNameForUserId($frm["player1"])."!!!</font><br/><br/>".
				getFullNameForUserId($frm["player1"])."'s ranking climbed from $winnersOldRanking to $newWinnerRanking<br>
						".getFullNameForUserId($frm["player2"])."'s ranking fell from $losersOldRanking to $newLoserRanking";
		
		
	}
	
	
}

/**
 * Validates that the user has a ranking
 */
function isUserValidForCourtType($user, $courtType){
	
	$query = "SELECT rankings.ranking 
				FROM tblUserRankings rankings
				WHERE rankings.userid = $user
				AND rankings.courttypeid = $courtType
				AND rankings.usertype = 0";
				
	
	$result = db_query($query);
	
	if(mysql_num_rows($result) > 0){
		return true;
	}
	
	return false;
	
}

/**
 * Simple getter that gets court type name
 */
 function getCourtTypeName($courtTypeId){
 	
 	$query = "SELECT courttype.courttypename 
				FROM tblCourtType courttype
				WHERE courttype.courttypeid = $courtTypeId";
				
	$result = db_query($query);
	
	return mysql_result($result, 0);
 }
 
 /**
  * Simple getter that gets a ranking
  */
  function getUserRankingForUserType($userid, $courtypeid, $usertype){
  	
  	$$query = "SELECT rankings.ranking 
				FROM tblUserRankings rankings
				WHERE rankings.userid = $userid
				AND rankings.courttypeid = $courtypeid
				AND rankings.usertype = $usertype";
	
	$result = db_query($$query);
	
	return mysql_result($result, 0);
  	
  } 
?>
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */
/**
* Class and Function List:
* Function list:
* - validate_form()
* - record_scores()
* - isUserValidForCourtType()
* - getUserRankingForUserType()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
require ($_SESSION["CFG"]["libdir"] . "/courtlib.php");
require ($_SESSION["CFG"]["libdir"] . "/sendgrid-php/SendGrid_loader.php");
$DOC_TITLE = "Record Scores";
$ct = $_REQUEST["ct"];
require_loginwq();
require_priv("2");

/* form has been submitted, try to create the new user account */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    
    if (empty($errormsg)) {

        // If this is a challenge match update the club ladder
        
        if ($frm['matchtype'] == "challenge") {
            
            if ($frm['usertype'] == "0") {
                
                if (isUserInClubLadder($frm['player1'], $frm['courttype'], get_clubid()) && isUserInClubLadder($frm['player2'], $frm['courttype'], get_clubid())) {

                    //SEt the winner and loser
                    $winneruserid = $frm['player1'];
                    $loseruserid = $frm['player2'];
                    adjustClubLadder($winneruserid, $loseruserid, $frm['courttype'], get_clubid());
                }
            } elseif ($frm['usertype'] == "1") {
                
                if (isUserInClubLadder($frm['player1'], $frm['courttype'], get_clubid()) && isUserInClubLadder($frm['player2'], $frm['courttype'], get_clubid()) && isUserInClubLadder($frm['player3'], $frm['courttype'], get_clubid()) && isUserInClubLadder($frm['player4'], $frm['courttype'], get_clubid())) {
                    adjustDoublesClubLadder($frm['player1'], $frm['player2'], $frm['player3'], $frm['player4'], $frm['courttype'], get_clubid());
                }
            }
        }
        $message = record_scores($frm);
        include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
        include ($_SESSION["CFG"]["includedir"] . "/include_message.php");
        include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
        die;
    }
}
$singlesCourtTypeDropDown = get_singlesCourtTypesForSite(get_siteid());
$doublesCourtTypeDropDown = get_doublesCourtTypesForSite(get_siteid());
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/report_scores_admin_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {
    $errors = new Object;
    $msg = "";

    //Validate Singles
    
    if ($frm["usertype"] == "singles") {
        
        if (empty($frm["player1"])) {
            $msg.= "You did not specify the first player";
        } elseif (empty($frm["player2"])) {
            $msg.= "You did not specify the second player";
        } elseif (empty($frm["courttype"])) {
            $msg.= "You did not specify the court type";
        } elseif ($frm["player1"] == $frm["player2"]) {
            $msg.= "Please specify different players";
        }

        //Validate the both player1 have rankings
        elseif (!isUserValidForCourtType($frm["player1"], $frm["courttype"])) {
            $msg.= getFullNameForUserId($frm["player1"]) . " is not enabled for " . getCourtTypeName($frm["courttype"]) . ".";
        }

        //Validate the both player2 have rankings
        elseif (!isUserValidForCourtType($frm["player2"], $frm["courttype"])) {
            $msg.= getFullNameForUserId($frm["player2"]) . " is not enabled for " . getCourtTypeName($frm["courttype"]) . ".";
        }

        // If this is a box league make sure that both players are in the same box
        elseif ($frm["matchtype"] == "boxleague" && !are_boxplayers($frm["player1"], $frm["player2"])) {
            $msg.= "It doesn't look like " . $frm["playeronename"] . " and " . $frm["playertwoname"] . " are in the same box league.";
        }
    } elseif ($frm["usertype"] == "doubles") {
        
        if (empty($frm["player1"])) {
            $msg.= "You did not specify the first player.";
        } elseif (empty($frm["player2"])) {
            $msg.= "You did not specify the second player.";
        } elseif (empty($frm["player3"])) {
            $msg.= "You did not specify the third player.";
        } elseif (empty($frm["player4"])) {
            $msg.= "You did not specify the fourth player.";
        } elseif (empty($frm["courttype"])) {
            $msg.= "You did not specify the court type.";
        }

        //Validate Player One
        elseif ($frm["player1"] == $frm["player2"] || $frm["player1"] == $frm["player3"] || $frm["player1"] == $frm["player4"]) {
            $msg.= "Please specify different players";
        }

        //Validate Player Two
        elseif ($frm["player2"] == $frm["player3"] || $frm["player2"] == $frm["player4"]) {
            $msg.= "Please specify different players";
        }

        //Validate Player Three and Fourt
        elseif ($frm["player3"] == $frm["player4"]) {
            $msg.= "Please specify different players";
        }

        //Validate the both player1 have rankings
        elseif (!isUserValidForCourtType($frm["player1"], $frm["courttype"])) {
            $msg.= getFullNameForUserId($frm["player1"]) . " is not enabled for " . getCourtTypeName($frm["courttype"]);
        }

        //Validate the both player1 have rankings
        elseif (!isUserValidForCourtType($frm["player2"], $frm["courttype"])) {
            $msg.= getFullNameForUserId($frm["player2"]) . " is not enabled for " . getCourtTypeName($frm["courttype"]);
        }

        //Validate the both player1 have rankings
        elseif (!isUserValidForCourtType($frm["player3"], $frm["courttype"])) {
            $msg.= getFullNameForUserId($frm["player3"]) . " is not enabled for " . getCourtTypeName($frm["courttype"]);
        }

        //Validate the both player1 have rankings
        elseif (!isUserValidForCourtType($frm["player4"], $frm["courttype"])) {
            $msg.= getFullNameForUserId($frm["player4"]) . " is not enabled for " . getCourtTypeName($frm["courttype"]);
        }
    }
    return $msg;
}
/**
 * Records the scores
 */
function record_scores(&$frm) {

    //Report Scores for doubles
    
    if ($frm["usertype"] == "doubles") {

        //Get rankings
        //Get Winner Team Id

        $winnerTeamId = getTeamIDForPlayers($frm["courttype"], $frm["player1"], $frm["player2"]);
        $winnerResult = getUserIdsForTeamIdWithCourtType($winnerTeamId, $frm["courttype"]);
        $playerRow = mysql_fetch_array($winnerResult);
        $winnersOldRanking = $playerRow['ranking'];
        $playerRow = mysql_fetch_array($winnerResult);
        $winnersOldRanking+= $playerRow['ranking'];
        $winnersOldRanking = $winnersOldRanking / 2;

        //Get Loser Team Id
        $loserTeamId = getTeamIDForPlayers($frm["courttype"], $frm["player3"], $frm["player4"]);
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: The Winning Team Id $winnerTeamId has a ranking of $winnersOldRanking for courttype: " . $frm["courttype"]);
        $loserResult = getUserIdsForTeamIdWithCourtType($loserTeamId, $frm["courttype"]);
        $playerRow = mysql_fetch_array($loserResult);
        $losersOldRanking = $playerRow['ranking'];
        $playerRow = mysql_fetch_array($loserResult);
        $losersOldRanking+= $playerRow['ranking'];
        $losersOldRanking = $losersOldRanking / 2;
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: The losing Team Id $loserTeamId has a ranking of $losersOldRanking for courttype: " . $frm["courttype"]);

        //Calculate Rankings
        $rankingArray = calculateRankings($winnersOldRanking, $losersOldRanking);
        $newWinnerRanking = $rankingArray['winner'];
        $newWinnerRanking = round($newWinnerRanking, 3);
        $newLoserRanking = $rankingArray['loser'];
        $newLoserRanking = round($newLoserRanking, 3);

        //Set rankings for team
        $winnersUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newWinnerRanking
							WHERE rankings.userid = $winnerTeamId
							AND rankings.courttypeid = " . $frm['courttype'] . "
							AND rankings.usertype = 1";
        db_query($winnersUpdate);
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: Updating the winning team ranking from " . $winnersOldRanking . " to " . $newWinnerRanking);
        /**
         *
         * Because each of these players has two seperate rankings that are being adjusted, one, their team ranking
         * and two their individual rankings.  The individual ranking is very important because this is what is used
         * to determine future team rankings as well as what drives the rankings adjustment.  How this works now, is that
         * first the team rankings are adjusted this adjustment is what is applied to the players individual ranking.
         *
         */

        // Adjust the winning teams individual rankings
        $winningTeamsIncrease = $newWinnerRanking - $winnersOldRanking;
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: Both player 1 and player2 will have their individuals ranking increased by $winningTeamsIncrease");

        //Update player one's ranking
        $playerOneIndividualRanking = getUserRankingForUserType($frm["player1"], $frm["courttype"], 0);
        $newPlayerOneIndividualRanking = $winningTeamsIncrease + $playerOneIndividualRanking;
        $playerOneUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newPlayerOneIndividualRanking
							WHERE rankings.userid = " . $frm["player1"] . "
							AND rankings.courttypeid = " . $frm['courttype'] . "
							AND rankings.usertype = 0";
        db_query($playerOneUpdate);
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: Updating player1 (" . $frm["player1"] . ") ranking from $playerOneIndividualRanking to $newPlayerOneIndividualRanking");

        //Update player two's ranking
        $playerTwoIndividualRanking = getUserRankingForUserType($frm["player2"], $frm["courttype"], 0);
        $newPlayerTwoIndividualRanking = $winningTeamsIncrease + $playerTwoIndividualRanking;
        $playerTwoUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newPlayerTwoIndividualRanking
							WHERE rankings.userid = " . $frm["player2"] . "
							AND rankings.courttypeid = " . $frm['courttype'] . "
							AND rankings.usertype = 0";
        db_query($playerTwoUpdate);
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: Updating player2 (" . $frm["player2"] . ") ranking from $playerTwoIndividualRanking to $newPlayerTwoIndividualRanking");
        $losersUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newLoserRanking
							WHERE rankings.userid = $loserTeamId
							AND rankings.courttypeid = " . $frm['courttype'] . "
							AND rankings.usertype = 1";
        db_query($losersUpdate);
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: Updating the losing team ranking from " . $losersOldRanking . " to " . $newLoserRanking);

        // Adjust the losing teams individual rankings
        $losingTeamsDecrease = $losersOldRanking - $newLoserRanking;
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: Both player3 and player4 will have their individuals ranking decreased by $losingTeamsDecrease");

        //Update player three's ranking
        $playerThreeIndividualRanking = getUserRankingForUserType($frm["player3"], $frm["courttype"], 0);
        $newPlayerThreeIndividualRanking = $playerThreeIndividualRanking - $losingTeamsDecrease;
        $playerThreeUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newPlayerThreeIndividualRanking
							WHERE rankings.userid = " . $frm["player3"] . "
							AND rankings.courttypeid = " . $frm['courttype'] . "
							AND rankings.usertype = 0";
        db_query($playerThreeUpdate);
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: Updating player3 (" . $frm["player3"] . ") ranking from $playerThreeIndividualRanking to $newPlayerThreeIndividualRanking");

        //Update player four's ranking
        $playerFourIndividualRanking = getUserRankingForUserType($frm["player4"], $frm["courttype"], 0);
        $newPlayerFourIndividualRanking = $playerFourIndividualRanking - $losingTeamsDecrease;
        $playerThreeUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newPlayerFourIndividualRanking
							WHERE rankings.userid = " . $frm["player4"] . "
							AND rankings.courttypeid = " . $frm['courttype'] . "
							AND rankings.usertype = 0";
        db_query($playerThreeUpdate);
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: Updating player4 (" . $frm["player4"] . ") ranking from $playerFourIndividualRanking to $newPlayerFourIndividualRanking");

        // format -
        $winnersOldRanking = sprintf("%01.4f", $winnersOldRanking);
        $newWinnerRanking = sprintf("%01.4f", $newWinnerRanking);
        $losersOldRanking = sprintf("%01.4f", $losersOldRanking);
        $newLoserRanking = sprintf("%01.4f", $newLoserRanking);
        report_scores_doubles_simple($winnerTeamId, $loserTeamId, $winnersOldRanking, $newWinnerRanking, $losersOldRanking, $newLoserRanking, $frm["score"], $frm['matchtype']);
        return "<font class=bigbanner> Yipeeee for " . getFullNamesForTeamId($winnerTeamId) . "!!!</font><br/><br/>" . getFullNamesForTeamId($winnerTeamId) . " ranking climbed from $winnersOldRanking to $newWinnerRanking<br>
						" . getFullNamesForTeamId($loserTeamId) . " ranking fell from $losersOldRanking to $newLoserRanking<br><br>
						Click <a href=" . ">here</a> to put another one in.";
    }

    //Report Scores for singles
    elseif ($frm["usertype"] == "singles") {
        
        if (isDebugEnabled(1)) logMessage("report_scores.record_scores: recording scores for a singles reservation with match type " . $frm['matchtype']);

        //Get rankings
        $winnersOldRanking = getUserRankingForUserType($frm["player1"], $frm["courttype"], 0);
        $losersOldRanking = getUserRankingForUserType($frm["player2"], $frm["courttype"], 0);

        //Calculate Rankings
        $rankingArray = calculateRankings($winnersOldRanking, $losersOldRanking);

        //if the match type is challenge then run run this calculation twice
        
        if ($frm['matchtype'] == "challenge") {
            $rankingArray = calculateRankings($rankingArray['winner'], $rankingArray['loser']);
        }
        $newWinnerRanking = $rankingArray['winner'];
        $newWinnerRanking = round($newWinnerRanking, 3);
        $newLoserRanking = $rankingArray['loser'];
        $newLoserRanking = round($newLoserRanking, 3);

        //Set rankings
        $winnersUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newWinnerRanking
							WHERE rankings.userid = " . $frm['player1'] . "
							AND rankings.courttypeid = " . $frm['courttype'] . "
							AND rankings.usertype = 0";
        db_query($winnersUpdate);
        $losersUpdate = "UPDATE tblUserRankings rankings
							SET rankings.ranking = $newLoserRanking
							WHERE rankings.userid = " . $frm['player2'] . "
							AND rankings.courttypeid = " . $frm['courttype'] . "
							AND rankings.usertype = 0";
        db_query($losersUpdate);

        //if this is a box league update the scores
        
        if ($frm['matchtype'] == "boxleague") {
            $boxId = getBoxIdTheseTwoGuysAreInTogether($frm['player1'], $frm['player2']);
            
            if (isDebugEnabled(1)) logMessage("report_scores.record_scores: This is a box league reservation for box $boxId");
            update_ladderscore($frm['score'], $boxId, $frm['player1'], $frm['player1'], $frm['player2']);
            update_gamesplayed($frm['player1'], $frm['player2'], $boxId);
        }

        // send out the email
        report_scores_singles_simple($frm["player1"], $frm["player2"], $winnersOldRanking, $newWinnerRanking, $losersOldRanking, $newLoserRanking, $frm["score"], $frm['matchtype']);
        return "<font class=bigbanner> Yay for " . getFullNameForUserId($frm["player1"]) . "!!!</font><br/><br/>" . getFullNameForUserId($frm["player1"]) . "'s ranking climbed from $winnersOldRanking to $newWinnerRanking<br>
						" . getFullNameForUserId($frm["player2"]) . "'s ranking fell from $losersOldRanking to $newLoserRanking<br><br>
						Click <a href=" . ">here</a> to put another one in.";
    }
}
/**
 * Validates that the user has a ranking
 */
function isUserValidForCourtType($user, $courtType) {
    $query = "SELECT rankings.ranking 
				FROM tblUserRankings rankings
				WHERE rankings.userid = $user
				AND rankings.courttypeid = $courtType
				AND rankings.usertype = 0";
    $result = db_query($query);
    
    if (mysql_num_rows($result) > 0) {
        return true;
    }
    return false;
}
/**
 * Simple getter that gets a ranking
 */
function getUserRankingForUserType($userid, $courtypeid, $usertype) {
    $query = "SELECT rankings.ranking 
				FROM tblUserRankings rankings
				WHERE rankings.userid = $userid
				AND rankings.courttypeid = $courtypeid
				AND rankings.usertype = $usertype";
    $result = db_query($query);
    $userranking = mysql_result($result, 0);
    
    if (isDebugEnabled(1)) logMessage("report_scores.getUserRankingForUserType: Getting the ranking as $userranking for user $userid as usertype $usertype on courttype $courtypeid");
    return $userranking;
}
?>
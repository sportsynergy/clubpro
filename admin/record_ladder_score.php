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
require '../vendor/autoload.php';

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
        
       

        $message = record_scores($frm);

        include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
        include ($_SESSION["CFG"]["includedir"] . "/include_message.php");
        include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
        die;
    }
}



include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/report_ladder_scores_admin_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {
    $errors = new clubpro_obj;
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
    
    if (mysqli_num_rows($result) > 0) {
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

    $user_array = mysqli_fetch_array($result);
    $userranking = $user_array[0];
    
    if (isDebugEnabled(1)) logMessage("report_scores.getUserRankingForUserType: Getting the ranking as $userranking for user $userid as usertype $usertype on courttype $courtypeid");
    return $userranking;
}
?>
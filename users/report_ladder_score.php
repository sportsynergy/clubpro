<?php

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
require ($_SESSION["CFG"]["libdir"] . "/courtlib.php");
require '../vendor/autoload.php';

$DOC_TITLE = "Report Ladder Score";
require_loginwq();

// load ladder info
if (!empty($_POST['ladderid'])) {
    $_SESSION["ladder_id"] = $_POST['ladderid'];
}
$ladderid = $_SESSION["ladder_id"];
$ladderplayers = getLadder($ladderid);
$ladderdetails = getLadderDetails($ladderid);

/* form has been submitted, try to create the new user account */
if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    
    if (empty($errormsg)) {

        // If this is a challenge match update the club ladder
        $message = record_scores($frm);
        redirect($_SESSION["CFG"]["wwwroot"] . "/users/player_ladder.php");
        die;
    }
}

include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/report_ladder_score_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {
    $errors = new clubpro_obj;
    $msg = "";

    //Validate Singles
     if (empty($frm["rsuserid"])) {
        $errors->winner = true;
        $msg.= "You did not specify the winner";
    } elseif (empty($frm["rsuserid2"]))  {
        $errors->loser = true;
         $msg.= "You did not specify the loser";
    } 
   
    return $msg;
}
/**
 * Records the scores
 */
function record_scores(&$frm) {

    $hourplayed = $frm['hourplayed'];
    $score = $frm['score'];
    $minuteofday = $frm['minuteofday'];
    $timeofday = $frm['timeofday'];
    $league = isset($frm['league'])?"TRUE":"FALSE";
    $ladderid = $frm['ladderid'];
    $score = $frm['score'];
    $kind = "";

        // when players report
        if ( $frm['outcome'] == "defeated"){
            $winnerid = get_userid();
            $loserid = $frm['rsuserid3'];
            $kind = "by user";


        } elseif ( $frm['outcome'] == "lostto" ){
            $winnerid = $frm['rsuserid3'];
            $loserid = get_userid();
            $kind = "by user";

        } else {
            $winnerid = $frm['rsuserid'];
            $loserid = $frm['rsuserid2'];
            $kind = "by admin";
        }

        if(!isPlayerAbleToScoreLeagueMatch($winnerid, $loserid, $ladderid) && $league){
            if (isDebugEnabled(1)) logMessage("report_ladder_score: Players ($winnerid, $loserid) are not in a box league together in league $leagues, but this was recorded as a league match. This will still be recorded but just not as a league match.");
            $league = "FALSE";
        }

        if (isDebugEnabled(1)) logMessage("report_ladder_score: Reporting a ladder score: winner: $winnerid, loser: $loserid, hourplayed: $hourplayed, score: $score, minuteofday: $minuteofday, timeofday: $timeofday, kind: $kind, and league: $league");

        // Set the match time
        if ( $timeofday == "PM"){
            $hourplayed = $hourplayed + 12;
        }
        $curtime = $_SESSION["current_time"];
        $currYear = gmdate("Y", $curtime);
        $currMonth = gmdate("n", $curtime);
        $currDay = gmdate("j", $curtime);
        $hourplayed = str_pad($hourplayed, 2, "0", STR_PAD_LEFT);
        $matchtime = "$currYear-$currMonth-$currDay $hourplayed:$minuteofday:00";

        if (isDebugEnabled(1)) logMessage("report_ladder_score: Checking to see if this match has already been entered ");


        //Make sure this same exact thing hasn't been entered already
        $check = "SELECT count(*) from tblLadderMatch 
        				WHERE ladderid = $ladderid 
                        AND winnerid = $winnerid 
        				AND loserid = $loserid
                        AND match_time = '$matchtime'
        				AND enddate IS NULL";

        $checkResult = db_query($check);
        $dontexist = mysqli_result($checkResult, 0);

        if( $dontexist == 0){

            if (isDebugEnabled(1)) logMessage("report_ladder_score: this match was  not already recorded. Adding.. ");
   
            $query = "INSERT INTO tblLadderMatch (
                ladderid, score, winnerid, loserid, match_time, league
                ) VALUES (
                          $ladderid
                          ,'$score'
                          ,$winnerid
                          ,$loserid
                          ,'$matchtime'
                          , $league
                          )";
            
            db_query($query);
        } else {
            if (isDebugEnabled(1)) logMessage("report_ladder_score: this match was already recorded. going to do nothing.");
        }

       

    

    return;
    
}
/**
 * Validates that the user has a ranking
 */
function isUserValidForCourtType($user, $courtType) {
   
   return;
}

?>
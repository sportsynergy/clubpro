<?php

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
require "../vendor/autoload.php";
$DOC_TITLE = "Player Ladder";
require_loginwq();

//Get General Club info (make sure current_time is fresh as can be)
$clubquery = "SELECT * from tblClubs WHERE clubid='" . $clubid . "'";
$clubresult = db_query($clubquery);
$clubobj = db_fetch_object($clubresult);
$tzdelta = $clubobj->timezone * 3600;
$curtime = time() + $tzdelta;
$_SESSION["current_time"] = $curtime;


// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

if (!empty($_POST['ladderid'])) {
    $_SESSION["ladder_id"] = $_POST['ladderid'];
}
$ladderid = $_SESSION["ladder_id"];

/* form has been submitted */
if (isset($_POST['submit']) || isset($_POST['cmd'])) {
    $frm = $_POST;
    $userid = $frm['userid'];
    $clubid = get_clubid();

    if ($frm['cmd'] == 'removematch') {

        $laddermatchid = $frm['laddermatchid'];
        if (isDebugEnabled(1)) logMessage("player_ladder: removing this ladder match $laddermatchid");
        $query = "UPDATE tblLadderMatch SET enddate = CURRENT_TIMESTAMP WHERE id = $laddermatchid";
        $result = db_query($query);
    }

     else 
    if ($frm['cmd'] == 'moveupinladder') {
        
        if (isDebugEnabled(1)) logMessage("player_ladder: moving user $userid up in ladder $ladderid ");
        moveUpOneInClubLadder($ladderid, $clubid, $userid);
    } 

    else if ($frm['cmd'] == 'removefromladder') {

        //get current position
        $query = "SELECT ladderposition from tblClubLadder where ladderid = $ladderid AND userid = $userid AND enddate IS NULL";
        $result = db_query($query);
        $position = mysqli_result($result, 0);
        
        if (isDebugEnabled(1)) logMessage("player_ladder: removing user $userid to club ladder for ladderid $ladderid by user:" . get_userid() );
        $query = "UPDATE tblClubLadder SET enddate = NOW() WHERE userid = $userid AND  ladderid = $ladderid";
        db_query($query);

        //Move everybody else up
        moveEveryOneInClubLadderUp($ladderid, $position + 1);
    } 

    // Challenge Player
    else if ($frm['cmd'] == 'challengeplayer') {
        $challengeeid = $frm['challengeeid'];
        $challengerid = get_userid();
        $message = $frm['textarea'];
        
        if (isDebugEnabled(2)) logMessage("player_ladder: challengeplayer $challengerid has challenged $challengeeid");

        //Create the challenge match
        createChallengematch($challengerid, $challengeeid, $courttypeid);

        //lock the two players
        lockLadderPlayers($challengerid, $challengeeid, $ladderid);

        //send the email
        sendEmailsForLadderMatch($challengerid, $challengeeid, $message);
    
    } else if ($frm['cmd'] == 'removechallenge') {
        $challengematchid = $frm['challengematchid'];
        $challengerid = $frm['challengerid'];
        $challengeeid = $frm['challengeeid'];
        
        if (isDebugEnabled(2)) logMessage("player_ladder: removing challenge match $challengematchid");

        //enddate the challenge match
        $query = "UPDATE tblChallengeMatch SET enddate = NOW() WHERE id = $challengematchid";
        db_query($query);

        //unlock the players
        unlockLadderPlayers($challengerid, $challengeeid, $courttypeid);

        //send emails
        //TODO create emails for removing challenge ladder

    } else if ($frm['cmd'] == 'reportladderscore') {

        $hourplayed = $frm['hourplayed'];
        $score = $frm['score'];
        $minuteofday = $frm['minuteofday'];
        $timeofday = $frm['timeofday'];
        $league = isset($frm['league'])?"TRUE":"FALSE";
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
            if (isDebugEnabled(1)) logMessage("player_ladder: Players ($winnerid, $loserid) are not in a box league together in league $leagues, but this was recorded as a league match. This will still be recorded but just not as a league match.");
            $league = "FALSE";
        }

        if (isDebugEnabled(1)) logMessage("player_ladder: Reporting a ladder score: winner: $winnerid, loser: $loserid, hourplayed: $hourplayed, score: $score, minuteofday: $minuteofday, timeofday: $timeofday, kind: $kind, and league: $league");

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

        if (isDebugEnabled(1)) logMessage("player_ladder: Checking to see if this match has already been entered ");


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

            if (isDebugEnabled(1)) logMessage("player_ladder: this match was  not already recorded. Adding.. ");
   
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
            if (isDebugEnabled(1)) logMessage("player_ladder: this match was already recorded. going to do nothing.");
        }

       

    }

    
}

// Initialize view with data
$availbleSports = load_avail_sports();
$ladderplayers = getLadder($ladderid);
$ladderdetails = getLadderDetails($ladderid);

$playingInLadder = isIndividualPlayingInLadder(get_userid() , $ladderid);
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_ladder_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");


/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
/**
 * Puts an entry in the challenge match table
 *
 * @param $challengerid
 * @param $challengeeid
 * @param $courttypeid
 */
function createChallengematch($challengerid, $challengeeid, $courttypeid) {
    
    if (isDebugEnabled(1)) logMessage("player_ladder.createChallengematch: creating a challenge match for challenger: $challengerid and challengee: $challengeeid with courttype $courttypeid");
    $query = "INSERT INTO tblChallengeMatch (
		                challengerid, challengeeid, courttypeid, siteid
		                ) VALUES (
		                          $challengerid
		                          ,$challengeeid
		                          ,$courttypeid
		                          ," . get_siteid() . ")";
    db_query($query);
}

?>
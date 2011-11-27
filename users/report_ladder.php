<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/

include("../application.php");
require($_SESSION["CFG"]["libdir"]."/ladderlib.php");
require_loginwq();

$DOC_TITLE = "Report Ladder Match";

//Set the http variables
$challengematchid = $_REQUEST["challengematchid"];
$source = $_REQUEST["source"];

$ladderMatchResult = loadLadderMatch($challengematchid);
$ladderMatchArray = mysql_fetch_array($ladderMatchResult);



/* form has been submitted, now reserve court */

if (match_referer() && isset($_POST["submit"])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $wwwroot = $_SESSION["CFG"]["wwwroot"];


        if ( empty($errormsg) ) {
            
			if($frm['winner']=="challenger"){
				$winneruserid = $frm['challengerid'];
				$loseruserid = $frm['challengeeid'];
				$score = $frm['score'];
				
			} else {
				$winneruserid = $frm['challengeeid'];
				$loseruserid = $frm['challengerid'];
				$score = -$frm['score'];
			}
			
			
			// Mark the score on the challenge match
			scoreChallengeMatch($score, $frm['challengematchid']);
			
        	// Update the ladder
        	$details = adjustClubLadder($winneruserid,$loseruserid, $frm['courttypeid'], get_clubid() );
        	
        	//Send out emails
        	emailLadderMatch($winneruserid, $loseruserid, abs($score),  $details, $frm['challengeeid']);
        	
        	//Unlock the players
        	unlockPlayers($winneruserid, $loseruserid, $frm['courttypeid'] );
        	
        	//redirect the person back to the club ladder
        	header ("Location: $wwwroot/users/player_ladder.php");
               

					
        }
}

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/report_ladder_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

	$msg = "";
	return $msg;
	
}

/**
 * 
 * @param unknown_type $score
 * @param unknown_type $challengematchid
 */
function scoreChallengeMatch($score, $challengematchid){
	
	logMessage("report_ladder.scoreChallengeMatch: Scoring the challenge match $score and challengematchid $challengematchid");
	
	$query = "UPDATE tblChallengeMatch challenge SET challenge.score = '$score' WHERE id = '$challengematchid'";
	
	db_query($query);
	
}

/**
 * Emails the users the results.  If the challengee wins that means that the ladder stays the same.
 * 
 * @param $winnerid
 * @param $loserid
 * @param $score
 */
function emailLadderMatch($winnerid, $loserid, $score, $details, $challengeeid){
	
	if( isDebugEnabled(1) ) logMessage("report_ladder.emailLadderMatch: sending out emails to winner $winnerid and loser $loserid about the score $score and challengeeid $challengeeid");
	
	/* load up the user record for the winner */
	$query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$winnerid'";
	$qid = db_query($query);
	$winner = db_fetch_object($qid);
	
	/* load up the user record for the winner */
	$query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$loserid'";
	$qid = db_query($query);
	$loser = db_fetch_object($qid);
	
	
	/* email the user with the new account information */
	$var = new Object;
	$var->w_firstname = $winner->firstname;
	$var->w_fullname = $winner->firstname . " " . $winner->lastname;
	$var->l_firstname = $loser->firstname;
	$var->l_fullname = $loser->firstname . " " . $loser->lastname;
	$var->support = $_SESSION["CFG"]["support"];
	
	$var->w_oldspot = $details->winneroldspot;
	$var->w_newspot = $details->winnernewspot;
	$var->l_oldspot = $details->loseroldspot;
	$var->l_newspot = $details->losernewspot;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	
	$var->score = 3-$score;
		
	// If the guy who got challenged won, then no change in the ladder
	if( $challengeeid == $winnerid){
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/report_ladder_match_nochange.php", $var);
	} else {
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/report_ladder_match.php", $var);
	}
	
	$w_message = "Hello $var->w_firstname,\n";
	$w_message .= "$emailbody";
	
	$l_message = "Hello $var->l_firstname,\n";
	$l_message .= "$emailbody";
	
	if( isDebugEnabled(1) ) logMessage($l_message);
	if( isDebugEnabled(1) ) logMessage($w_message);

	mail("$var->w_fullname <$winner->email>", "$clubfullname -- Ladder Match Report", $w_message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");
	mail("$var->l_fullname <$loser->email>", "$clubfullname -- Ladder Match Report", $l_message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");
}

/**
 * Unlocks the players 
 * 
 * @param $winneruserid
 * @param $loseruserid
 * @param $courttypeid
 */
function unlockPlayers($winneruserid, $loseruserid,$courttypeid){

		if( isDebugEnabled(1) )logMessage("report_ladder.unlockPlayers: unlocking players $winneruserid and loseruserid $loseruserid for courttypeid $courttypeid");
		 
		$query = "UPDATE tblClubLadder ladder SET locked = 'n' WHERE ladder.userid = '$winneruserid' OR ladder.userid = '$loseruserid' 
					AND ladder.courttypeid = '$courttypeid' ";
		
		db_query($query);
		
	
}




?>
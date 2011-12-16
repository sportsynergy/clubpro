<?


include("../application.php");
require($_SESSION["CFG"]["libdir"]."/ladderlib.php");
require($_SESSION["CFG"]["libdir"]."/postageapplib.php");
$DOC_TITLE = "Player Ladder";
require_loginwq();


//TODO hardcoding for now
$courttypeid = 2;


/* form has been submitted */   
if ( isset($_POST['submit']) || isset($_POST['cmd'])   ) {

		$frm = $_POST;
        $userid = $frm['userid'];
        $clubid = get_clubid();

    	// Add User to Ladder
        if($frm['cmd']=='addtoladder'){
      
			if(isDebugEnabled(2) ) logMessage("player_ladder: addtoladder");
        	
        	//Check to see if player is already in ladder
        	$check = "SELECT count(*) from tblClubLadder 
        				WHERE userid = $userid 
        				AND clubid = $clubid 
        				AND courttypeid = $courttypeid 
        				AND enddate IS NULL";
        	
        	$checkResult = db_query($check);
        	$exists = mysql_result($checkResult,0);
        	
        	if( $exists==0){
        		
	        	$query = "SELECT count(*) from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND enddate IS NULL";
	        	$result = db_query($query);
	        	$position = mysql_result($result, 0) + 1;
	        	
	        	if(isDebugEnabled(2) ) logMessage("player_rankings: adding user $userid to club ladder for club $clubid for courttypeid $courttypeid in position $position");
	
	        	$query = "INSERT INTO tblClubLadder (
		                userid, courttypeid, ladderposition, clubid
		                ) VALUES (
		                          $userid
		                          ,$courttypeid
		                          ,$position
		                          ,$clubid)";
		                          
				db_query($query);
				
	        } else{
	        	
	        	if(isDebugEnabled(2) ) logMessage("player_ladder: user $userid is already playing in this ladder with court typeid $courttypeid ");	
	        }
        }
		
        else if($frm['cmd']=='moveupinladder' ){
        	
        	if(isDebugEnabled(2) ) logMessage("player_ladder: moving user $userid up in ladder $courttypeid ");
        	
        	moveUpOneInClubLadder($courttypeid, $clubid, $userid);
        }

		else if( $frm['cmd']=='removefromladder' ){
        	
        	//get current position
        	$query = "SELECT ladderposition from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND userid = $userid AND enddate IS NULL";
        	$result = db_query($query);
        	$position = mysql_result($result, 0);
        	
			if(isDebugEnabled(1) ) logMessage("player_ladder: removing user $userid to club ladder for club $clubid for courttypeid $courttypeid");
			
			$query = "UPDATE tblClubLadder SET enddate = NOW() WHERE userid = $userid AND  courttypeid = $courttypeid AND clubid = $clubid";
	                          
			db_query($query);
			
			//Move everybody else up
			moveEveryOneInClubLadderUp($courttypeid, $clubid, $position+1);
			
		}  

		else if ( $frm['cmd']=='challengeplayer'){
			
			$challengeeid = $frm['challengeeid'];
			$challengerid = get_userid();
			$message = $frm['textarea'];
			
			if(isDebugEnabled(2) ) logMessage("player_ladder: challengeplayer $challengerid has challenged $challengeeid");
			
			//Create the challenge match
			createChallengematch($challengerid, $challengeeid, $courttypeid);
			
			//lock the two players
			lockLadderPlayers($challengerid, $challengeeid, $courttypeid);
			
			//send the email
			sendEmailsForLadderMatch($challengerid, $challengeeid, $message);
			
			
		}
}

// Initialize view with data    

$availbleSports = load_avail_sports();
$ladderplayers = getLadder($courttypeid);
$playingInLadder = isPlayingInLadder(get_userid(), $courttypeid);

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/player_ladder_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/


/**
 * Locks the players in the ladder
 * 
 * @param $challengerid
 * @param $challengeeid
 */
function lockLadderPlayers($challengerid, $challengeeid, $courttypeid){
	
	if( isDebugEnabled(1) ) logMessage("player_ladder.lockLadderPlayers: locking challenger:  $challengerid and challengee:  $challengeeid on courttypeid $courttypeid");
	
	$query = "UPDATE tblClubLadder ladder SET ladder.locked = 'y' WHERE ladder.userid = $challengerid OR ladder.userid = $challengeeid
				AND ladder.enddate IS NULL and ladder.courttypeid = $courttypeid and ladder.clubid = ".get_clubid();
	
	db_query($query);
}


/**
 * Puts an entry in the challenge match table
 * 
 * @param $challengerid
 * @param $challengeeid
 * @param $courttypeid
 */
function createChallengematch($challengerid, $challengeeid, $courttypeid){
	
	if( isDebugEnabled(1) ) logMessage("player_ladder.createChallengematch: creating a challenge match for challenger: $challengerid and challengee: $challengeeid with courttype $courttypeid");
	
	$query = "INSERT INTO tblChallengeMatch (
		                challengerid, challengeeid, courttypeid, siteid
		                ) VALUES (
		                          $challengerid
		                          ,$challengeeid
		                          ,$courttypeid
		                          ,".get_siteid().")";
		                          
	db_query($query);
	
}


/**
 * Gets the ladder for the given court type
 * 
 * @param unknown_type $courttypeid
 */
function getLadder($courttypeid){
	
	if( isDebugEnabled(1) ) logMessage("player_ladder.getLadder: getting the players in the ladder for courttype $courttypeid");
	
	$rankquery = "SELECT 
						users.userid,
						ladder.ladderposition,
						ladder.going,
						users.firstname, 
						users.lastname,
						concat_ws(' ', users.firstname, users.lastname) as fullname,
						users.email,
						ladder.locked
                    FROM 
						tblUsers users, 
						tblClubLadder ladder
                    WHERE 
						users.userid = ladder.userid
                    AND ladder.clubid=".get_clubid()."
                    AND ladder.courttypeid=$courttypeid
					AND ladder.enddate is NULL
                    ORDER BY ladder.ladderposition";
	
	return db_query($rankquery);
}

/**
 * True is user is, false if player isn't
 * @param $userid
 */
function isPlayingInLadder($userid, $courttypeid){
	
	$query = "SELECT 1 FROM tblClubLadder WHERE userid = $userid AND courttypeid = $courttypeid AND clubid = ".get_clubid() ." AND enddate IS NULL";
    $result = db_query($query);
    $rows = mysql_num_rows($result);
    
    if($rows>0){
    	return true;
    } else{
    	return false;
    }
}

/**
 * Send off an email to the challenger and the challengee
 * 
 * @param unknown_type $challengerid
 * @param unknown_type $challengeeid
 */
function sendEmailsForLadderMatch($challengerid, $challengeeid, $message){
	
	if( isDebugEnabled(1) ) logMessage("player_ladder.sendEmailsForLadderMatch: sending out emails to challenger $challengerid and challengee $challengeeid ");
	
	//Set some variables
	$template = get_sitecode();
	$subject = get_clubname()." - Ladder Match";

	
	/* load up the user record for the winner */
	$query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$challengerid'";
	$qid = db_query($query);
	$challenger = db_fetch_object($qid);
	
	/* load up the user record for the winner */
	$query = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$challengeeid'";
	$qid = db_query($query);
	$challengee = db_fetch_object($qid);
	
	
	/* email the user with the new account information */
	$var = new Object;
	$var->challenger_firstname = $challenger->firstname;
	$var->challenger_fullname = $challenger->firstname . " " . $challenger->lastname;
	$var->challengee_firstname = $challengee->firstname;
	$var->challengee_fullname = $challengee->firstname ." ". $challengee->lastname;
	$var->support = $_SESSION["CFG"]["support"];
	$var->message = $message;
		
	$challenger_emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_ladder_match_challenger.php", $var);
	$challenger_emailbody = nl2br($challenger_emailbody);
	
	$challengee_emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_ladder_match_challengee.php", $var);
	$challengee_emailbody = nl2br($challengee_emailbody);
	
	// Provide Content for Challenger
	$challenger_email = array($challenger->email => array('name' => $challenger->firstname) );
	$content = new Object;
	$content->line1 = $challenger_emailbody;
	$content->clubname = get_clubname();
	$from_email = "Sportsynergy <player.mailer@sportsynergy.net>";
		
	//Send the email
     send_email($subject, $challenger_email, $from_email, $content, $template); 
     
     // Provide Content for Challengee
    $challengee_email = array($challengee->email => array('name' => $challengee->firstname) );
	$content = new Object;
	$content->line1 = $challengee_emailbody;
	$content->clubname = get_clubname();
	$from_email = "$var->challenger_fullname <$challenger->email>";
		
	//Send the email
     send_email($subject, $challengee_email, $from_email, $content, $template); 
	
	
}

?>
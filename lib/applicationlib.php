<?


/**
 * Logs in user
 * 
 * One very interesting thing here is that anyone can use a superpassword to login. This password is
 * wramijirap and is MD5 encoded as 25a694bd7f0a3f48e078f30c3afce1e5
 */
function verify_login($username, $password, $encodedpassword) {
/* verify the username and password.  if it is a valid login, return an array
 * with the username, firstname, lastname, and email address of the user */

       $superpassword = "25a694bd7f0a3f48e078f30c3afce1e5";

       if($encodedpassword){
       	 $password = md5($password);
       }
       
       if( isDebugEnabled(1) ) logMessage("applicationlib.verify_login: Logging in $username");
		
        $loginQuery = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email, clubuser.roleid, club.clubname
        			   FROM tblUsers users, tblClubUser clubuser, tblClubs club
					   WHERE users.username = '$username' 
					   AND users.userid = clubuser.userid
					   AND clubuser.clubid='" . get_clubid() . "'
					   AND club.clubid = clubuser.clubid
					   AND users.password = '$password'
        			   AND clubuser.enable='y' 
					   AND clubuser.enddate IS NULL";
        
							   
		$loginResult = db_query($loginQuery);
		
		// If the login fails see if the superpassword was used
		if ( mysql_num_rows($loginResult) == 0){
			
			//encode the superpassword
			
			// If they used the superpassword, then just get the user.
			if($superpassword == $password){
				
				$loginQuery = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email, clubuser.roleid, club.clubname
        			   FROM tblUsers users, tblClubUser clubuser, tblClubs club
					   WHERE users.username = '$username' 
					   AND users.userid = clubuser.userid
					   AND clubuser.clubid='" . get_clubid() . "'
					   AND club.clubid = clubuser.clubid
        			   AND clubuser.enable='y' 
					   AND clubuser.enddate IS NULL";
					   
				$loginResult = db_query($loginQuery);
			}
			
		}
		

        return db_fetch_array($loginResult);
}

/**
 * Loads user (logs in with no password)
 */
function load_user($userid){
	
	 $loginQuery = "SELECT users.userid, users.username, users.firstname, users.lastname, users.email, clubuser.clubid, clubuser.roleid, club.clubname
        			   FROM tblUsers users, tblClubUser clubuser, tblClubs club
					   WHERE users.userid = clubuser.userid
					   AND clubuser.clubid
					   AND clubuser.clubid='" . get_clubid() . "'
					   AND users.userid = '$userid' 
        			   AND clubuser.enable='y'
					   AND club.clubid = clubuser.clubid
					   AND clubuser.enddate IS NULL ";

		$loginResult = db_query($loginQuery);
		
		return db_fetch_array($loginResult);

}

/**
 * Returns all users with the given username.
 * Returns the array.
 */
function getAllUsersWithIdResult($username, $clubid){
	
	$usersQuery = "SELECT users.userid, users.firstname, users.lastname
					FROM tblUsers users, tblClubUser clubuser
					WHERE users.username = '$username'
					AND users.userid = clubuser.userid
					AND clubuser.clubid='" . get_clubid() . "'
					AND clubuser.enddate IS NULL";
	
	return  db_query($usersQuery);
	
}




/* This determines if the user is valid for a sport. Returns either TRUE or FALSE */
function amiValidForSite($siteid) {


	$amiauthforsiteQuery = "SELECT tblkupSiteAuth.userid, tblkupSiteAuth.siteid
	                              FROM tblkupSiteAuth
	                              WHERE (((tblkupSiteAuth.userid)=" . get_userid() . ") AND ((tblkupSiteAuth.siteid)=$siteid))";

	$amiauthforsiteResult = db_query($amiauthforsiteQuery);
	if (mysql_numrows($amiauthforsiteResult) == 0) {
		if( isDebugEnabled(1) ) logMessage("applicationlib.amiValidForSite: ". get_userid(). " is NOT valid for $siteid");
		return FALSE;
	} else {
		if( isDebugEnabled(1) ) logMessage("applicationlib.amiValidForSite: ". get_userid(). " is  valid for $siteid");
		return TRUE;
	}
}

/*
***********************************************************************************************************************
*/
/* This determines if the user is valid for a site. Returns either TRUE or FALSE */

function isValidForCourtType($courttypeid, $userid) {

	$amiauthforCourtTypeQuery = "SELECT tblUserRankings.courttypeid
	                                  FROM tblUserRankings
	                                  WHERE tblUserRankings.userid=$userid
	                                  AND tblUserRankings.courttypeid=$courttypeid
	                                  AND tblUserRankings.usertype=0";

	$amiauthforCourtTypeResult = db_query($amiauthforCourtTypeQuery);

	if (mysql_numrows($amiauthforCourtTypeResult) == 0) {
		if( isDebugEnabled(1) ) logMessage("applicationlib.amiValidForCourtType: $userid is NOT valid for $courttypeid");
		return FALSE;
	} else {
		if( isDebugEnabled(1) ) logMessage("applicationlib.amiValidForCourtType: $userid is valid for $courttypeid");
		return TRUE;
	}
}


/* 
 * Check to see if I am a buddy
 */

function amIaBuddyOf($userid) {

	$imabuddy = FALSE;

	$imabuddyQuery = "SELECT buddyid FROM tblBuddies WHERE userid=$userid";
	$imabuddyResult = db_query($imabuddyQuery);

	while ($imabuddyArray = mysql_fetch_array($imabuddyResult)) {

		if ($imabuddyArray['buddyid'] == get_userid()) {
			$imabuddy = TRUE;
		}
	}

	return $imabuddy;

}

/*
 * Checks to see if this person is a buddy.
 */
function isABuddyOfMine($buddyid){
	
	$isABuddy = FALSE;

	$imabuddyQuery = "SELECT buddyid FROM tblBuddies WHERE userid=".get_userid();
	$imabuddyResult = db_query($imabuddyQuery);

	while ($imabuddyArray = mysql_fetch_array($imabuddyResult)) {

		if ($imabuddyArray['buddyid'] == $buddyid) {
			$isABuddy = TRUE;
		}
	}

	return $isABuddy;
}




/* This is used to quickly set a match type.  Right now as far as I know there are 4 possible match types:

   0    practice match
   1    box league match
   2    challenge match
   3    buddy match

  As you can see this will set the resevation matchtype to whatever you pass in.
*/
function markMatchType($resid, $matchtype) {

	$markMatchTypeQuery = "Update tblReservations SET matchtype=$matchtype WHERE reservationid=$resid AND enddate IS NULL";
	$markMatchTypeResult = db_query($markMatchTypeQuery);

}

/*
***********************************************************************************************************************
            This will find out if the calling user is in a box of the courttype fo the court passed in as the argument.
            Not very fancy here, true if they are false if they are not.
*/

function is_inabox($courtid, $userid) {

	$amIinThisBox = FALSE;
	$courtTypeId = get_courtTypeForCourt($courtid);
	$amiinaboxquery = "SELECT boxleagues.courttypeid, boxleaguedetails.userid
								FROM tblBoxLeagues boxleagues, tblkpBoxLeagues boxleaguedetails
	                            WHERE boxleagues.boxid = boxleaguedetails.boxid
	                            AND boxleagues.courttypeid=$courtTypeId 
								AND boxleaguedetails.userid=$userid";

	// run the query on the database
	$amiinaboxresult = db_query($amiinaboxquery);

	if (mysql_num_rows($amiinaboxresult) > 0) {
		$amIinThisBox = TRUE;
	}

	return $amIinThisBox;
}
/*
***********************************************************************************************************************
*/
/* returns the match type for the given reservationid  */
function getMatchType($resid) {

	$matchtypequery = "SELECT matchtype FROM `tblReservations` WHERE reservationid=$resid";
	$matchtyperesult = db_query($matchtypequery);
	$matchtypevalue = mysql_result($matchtyperesult, 0);

	return $matchtypevalue;

}


/*
***********************************************************************************************************************
*/
function get_partnerbytid($tid) {
	/* When you need to just get the first and last name of a user and you only
	   seem to have there userid handy then this is the function for you.
	
	*/
	$firstandlastquery = "SELECT users.firstname, users.lastname
	                      FROM tblkpTeams teamdetails, tblUsers users
						  WHERE teamdetails.userid = users.userid
	                      AND teamdetails.teamid=$tid
	                      AND users.userid !=" . get_userid();

	$firstandlastresult = db_query($firstandlastquery);
	$firstandlastarray = mysql_fetch_array($firstandlastresult);

	return "$firstandlastarray[0] $firstandlastarray[1]";
}

/**
 * Figuers out if this user is in this league.
 */
function isUserInClubLadder($userid, $courttypeid, $clubid){
	
	$query = "SELECT 1 FROM tblClubLadder ladder WHERE ladder.userid = $userid AND ladder.courttypeid = $courttypeid AND ladder.clubid = $clubid";
	$result = db_query($query);
	if( mysql_num_rows($result) > 0 ){
		return true;
	}
	else{
		return false;
	}
	
}



/*
***********************************************************************************************************************

	
	Does not look at the reservation match type but only if the two players
	are in a box together and that haven't recorded the score yet. Returns 
	true if this is an unscore box league.
	

	
	*/
function isUnscoredBoxLeagueReservation($reservationid) {

	
	//Check reservation History
   $query = "SELECT * FROM tblBoxHistory history, tblkpUserReservations reservationdetails 
			 WHERE history.reservationid = $reservationid
			 AND reservationdetails.reservationid = history.reservationid
             AND reservationdetails.outcome = 0";
			
  $results = db_query($query);
 
  //If reservation hasnt't been scored
  if( mysql_num_rows($results)==2){

  	return true;
  }

	return false;
}

/**
 * Called by the court reservation page to valiidate that
	the the user is actually in a box leage with this opponent.
 */
function getBoxIdTheseTwoGuysAreInTogether( $playerOneId, $playerTwoId) {


	$playeronequery = "SELECT boxleagues.boxid, boxleaguedetails.userid
	                   FROM tblBoxLeagues boxleagues, tblkpBoxLeagues boxleaguedetails
	                   WHERE boxleagues.boxid = boxleaguedetails.boxid
	                   AND boxleaguedetails.userid=$playerOneId";

	// run the query on the database
	$playeroneresult = db_query($playeronequery);

	$p1stack = array ();
	$p2stack = array ();

	//Put all boxes for the user in an array
	while ($playeronearray = db_fetch_array($playeroneresult)) {
		array_push($p1stack, $playeronearray[0]);
	}


	$playertwoquery = "SELECT boxleagues.boxid, boxleaguedetails.userid
	                   FROM tblBoxLeagues boxleagues, tblkpBoxLeagues boxleaguedetails
	                   WHERE boxleagues.boxid = boxleaguedetails.boxid
	                   AND boxleaguedetails.userid=$playerTwoId";

	// run the query on the database
	$playertworesult = db_query($playertwoquery);
	//Put all boxes for the user in an array
	while ($playertwoarray = db_fetch_array($playertworesult)) {
		array_push($p2stack, $playertwoarray[0]);
	}

	$playersintersect = array_intersect($p1stack, $p2stack);

	return $playersintersect[0];
	
}

/**
 * Returns the box id when of the box that the players share.  This function is called by 
 * the court reservation page to validate that the the user is actually in a 
 * box leage with this opponent
*/

function are_boxplayers($playerone, $playertwo) {


	$playeronequery = "SELECT boxleagues.boxid, boxleaguedetails.userid
	                   FROM tblBoxLeagues boxleagues, tblkpBoxLeagues boxleaguedetails
	                   WHERE boxleagues.boxid = boxleaguedetails.boxid
	                   AND boxleaguedetails.userid=$playerone";

	// run the query on the database
	$playeroneresult = db_query($playeronequery);

	$p1stack = array ();
	$p2stack = array ();

	//Put all boxes for the user in an array
	while ($playeronearray = db_fetch_array($playeroneresult)) {
		array_push($p1stack, $playeronearray[0]);
	}


	$playertwoquery = "SELECT boxleagues.boxid, boxleaguedetails.userid
	                   FROM tblBoxLeagues boxleagues, tblkpBoxLeagues boxleaguedetails
	                   WHERE boxleagues.boxid = boxleaguedetails.boxid
	                   AND boxleaguedetails.userid=$playertwo";

	// run the query on the database
	$playertworesult = db_query($playertwoquery);
	//Put all boxes for the user in an array
	while ($playertwoarray = db_fetch_array($playertworesult)) {
		array_push($p2stack, $playertwoarray[0]);
	}

	$playersintersect = array_intersect($p1stack, $p2stack);

	if(count($playersintersect)>0){
		return true;
	}else{
		return false;
	}
}

/*
***********************************************************************************************************************
*/

function is_logged_in() {
	/* this function will return true if the user has logged in.  a user is logged
	 * in if the $_SESSION["user"] is set (by the login.php page) and also if the
	 * remote IP address matches what we saved in the session ($_SESSION["ip"])
	 * from login.php -- this is not a robust or secure check by any means, but it
	 * will do for now */

	return isset ($_SESSION) && isset ($_SESSION["user"]);
}

function require_login() {
	/* this function checks to see if the user is logged in.  if not, it will show
	 * the login screen before allowing the user to continue */

	if (!is_logged_in()) {
		$_SESSION["wantsurl"] = qualified_me();
		redirect($_SESSION["CFG"]["wwwroot"]."/login.php");
	}
}

function require_loginwq() {
	/* this function checks to see if the user is logged in.  if not, it will show
	 * the login screen before allowing the user to continue */

	if (!is_logged_in()) {
		$_SESSION["wantsurl"] = qualified_mewithq();
		redirect($_SESSION["CFG"]["wwwroot"]."/login.php");
	}
}

function get_clubid() {
	/* this function simply returns the clubid. */

	return $_SESSION["siteprefs"]["clubid"];

}


function get_siteid() {
	/* this function simply returns the siteid. */

	return $_SESSION["siteprefs"]["siteid"];

}


function isSiteAutoLogin() {
	/* this function simply returns the siteid. */


	return $_SESSION["siteprefs"]["enableautologin"]=='y'?true:false;
}


function isDisplayRecentActivity() {
	/* this function simply returns whether or not the recent activity should be displayed */
	return $_SESSION["siteprefs"]["displayrecentactivity"]=='y'?true:false;

}

function get_displaytime() {
	/* this function simply returns the daysahead or the parameter that defines how far in advance users can make. */


	return $_SESSION["siteprefs"]["displaytime"];

}

function isSoloReservationEnabled() {
		/* this function simply returns the whether or not the site has solo reservations enabled. */

	return $_SESSION["siteprefs"]["allowsoloreservations"]=='y'?true:false;
	

}

function isLadderRankingScheme() {
		/* this function simply returns the whether or not the site has solo reservations enabled. */

	return $_SESSION["siteprefs"]["rankingscheme"]=='ladder'?true:false;
	

}

function getChallengeRange() {
		/* this function simply returns the challenge range*/

	return $_SESSION["siteprefs"]["challengerange"];
	

}

function isPointRankingScheme() {
		/* this function simply returns the whether or not the site has solo reservations enabled. */

	return $_SESSION["siteprefs"]["rankingscheme"]=='point'?true:false;
	

}

function isSelfScoreEnabled() {
	/* this function simply returns the whether or not the site has self score enabled. */

	return $_SESSION["siteprefs"]["allowselfscore"]=='y'?true:false;
	

}

function isSiteEnabled() {
	/* this function simply returns the whether or not the site is enabled. */

	return $_SESSION["siteprefs"]["enable"]=='y'?true:false;
	

}

function getRankingAdjustment() {
	/* this function simply returns the site ranking adjustment. */


	return $_SESSION["siteprefs"]["rankingadjustment"];
	

}
function isSiteGuestReservationEnabled() {
	/* this function simply returns the siteid. */


	return $_SESSION["siteprefs"]["enableguestreservation"]=='y'?true:false;
}

function get_daysahead() {
	/* this function simply returns the daysahead or the parameter that defines how far in advance users can make. */

	return $_SESSION["siteprefs"]["daysahead"];

}


function isLiteVersion() {
	/* this function returns if the site is the free version. */
	return $_SESSION["siteprefs"]["isliteversion"]=='y'?true:false;
	
}

function isAllowAllSiteAdvertising(){
	return $_SESSION["siteprefs"]["allowallsiteadvertising"]=='y'?true:false;
}

function isNearRankingAdvertising(){
	return $_SESSION["siteprefs"]["allownearrankingadvertising"]=='y'?true:false;
}

function isDisplaySiteNavigation(){
	return $_SESSION["siteprefs"]["displaysitenavigation"]=='y'?true:false;
}

function get_roleid() {
	
	/* this function simply returns the roleid. */

	return $_SESSION["user"]["roleid"];

}

function get_userid() {
	/* this function simply returns the userid. */

	return $_SESSION["user"]["userid"];

}

function get_userfullname(){
	
	/* this function simply returns the logged in users first and last name. */
	
	return $_SESSION["user"]["firstname"] . " " . $_SESSION["user"]["lastname"];
}

function get_clubname(){
	
	/* this function simply returns the club name  */
	
	return $_SESSION["user"]["clubname"];
}


function get_tzdelta() {

	//gets the tzdelta
	$tzquery = "SELECT timezone from tblClubs WHERE clubid='" . get_clubid() . "'";
	$tzresult = db_query($tzquery);
	$tzdelta = mysql_result($tzresult, 0);
	return $tzdelta * 3600;

}

function require_priv($roleid) {
	/* this function checks to see if the user has the privilege $roleid.  if not,
	 * it will display an Insufficient Privileges page and stop */

	if ($_SESSION["user"]["roleid"] != $roleid) {
		include ($_SESSION["CFG"]["templatedir"]."/insufficient_privileges.php");
		die;
	}

}

function has_priv($roleid) {
	/* returns true if the user has the privilege $priv */

	if (isset ($_SESSION["user"])) {
		return $_SESSION["user"]["roleid"] == $roleid;
	}

}

function atleastof_priv($roleid) {
	/* returns true if the user has the privilege $priv */

	return $_SESSION["user"]["roleid"] >= $roleid;
}


/* returns a randomly generated password of length $maxlen.  inspired by
	 * http://www.phpbuilder.com/columns/jesus19990502.php3 */
function generate_password() {

	$maxlen = 10;
	
	if(isDebugEnabled(1) ) logMessage("applicationlib: generate_passowrd: ".$_SESSION["CFG"]["wordlist"]);
	
	$fillers = "1234567890!@#$%&*-_=+^";
	$wordlist = file($_SESSION["CFG"]["wordlist"]);

	srand((double) microtime() * 1000000);
	$word1 = trim($wordlist[rand(0, count($wordlist) - 1)]);
	$word2 = trim($wordlist[rand(0, count($wordlist) - 1)]);
	$filler1 = $fillers[rand(0, strlen($fillers) - 1)];

	return substr($word1 . $filler1 . $word2, 0, $maxlen);
}

function err(& $errorvar) {
	/* if $errorvar is set, then print an error marker << */

	if (isset ($errorvar)) {
		echo "<font color=#ff0000>&lt;&lt;</font>";
	}
}

function err2(& $errorvar) {
	/* like err(), but prints the marker >> */

	if (isset ($errorvar)) {
		echo "<font color=#ff0000>&gt;&gt;</font>";
	}
}

function username_exists($username) {
	/* returns the true if the username exists */

	$qid = db_query("SELECT 1 FROM tblUsers users, tblClubUser clubuser 
						WHERE users.username = '$username' 
						AND users.userid = clubuser.userid
						AND clubuser.enddate IS NULL
						AND clubuser.clubid = ". get_clubid()."");
						
	return db_num_rows($qid);
}

/**
 *  Used to see if there is another one.
 */
function username_already_exists($username, $userid) {
	/* returns the true if the username exists */

	$qid = db_query("SELECT users.username, users.userid FROM tblUsers users, tblClubUser clubuser 
						WHERE users.username = '$username' 
						AND users.userid = clubuser.userid
						AND clubuser.enddate IS NULL
						AND clubuser.clubid = ". get_clubid()."");
						
	$userArray = db_fetch_array($qid);

	//If no rows are returned or if the username/userid is unique then the username is unique.
	if(db_num_rows($qid)==0 || ($userArray['username'] == $username && $userArray['userid']==$userid)){
		return false;
	}
	else{
		return true;
	}



}

function email_exists($email) {
	/* returns true the email address exists */
	
	$query = "SELECT 1 FROM tblUsers users, tblClubUser clubuser
				WHERE users.email = '$email' 
				AND users.userid = clubuser.userid
				AND clubuser.clubid = ".get_clubid()."
				AND users.enddate IS NULL";
	$qid = db_query($query);
	
	return db_num_rows($qid);
}


/**
 * Reset the users password
 * 
 * @param $userid
 */
function reset_user_password($userid) {
	/* resets the password for the user with the username $username, and sends it
	 * to him/her via email */

	if(isDebugEnabled(1) ) logMessage("applicationlib: reset_user_password for userid: $userid");
	

	/* load up the user record */
	$qid = db_query("SELECT users.userid, users.username, users.firstname, users.lastname, users.email 
						FROM tblUsers users
						WHERE users.userid = '$userid'");
	$user = db_fetch_object($qid);

	/* reset the password */
	$newpassword = generate_password();
	
	if(isDebugEnabled(1) ) logMessage("applicationlib: reset_user_password the new password will be: $newpassword");
	
	$qid = db_query("UPDATE tblUsers SET password = '" . md5($newpassword) . "' WHERE userid = '$user->userid'");

	/* email the user with the new account information */
	$var = new Object;
	$var->username = $user->username;
	$var->fullname = $user->firstname . " " . $user->lastname;
	$var->newpassword = $newpassword;
	$var->support = $_SESSION["CFG"]["support"];

	$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/reset_password.php", $var);
	
	if(isDebugEnabled(1) ) logMessage("applicationlib: sending this email $emailbody");
	

	mail("$var->fullname <$user->email>", "Sportsynergy Account Information", $emailbody, "From: $var->support", "-fPlayerMailer@sportsynergy.com");
}


/*
*******************************************************************************************************
**   makeTeamForCurrentUser

     creates a team, assigns a reanking and returns the new teamid for the current user

*******************************************************************************************************
*/

function makeTeamForCurrentUser($sportname, $partnerid) {

	/* Set the team identifier     */

	$setteamquery = "INSERT INTO tblTeams (
	                courttypeid
	                ) VALUES (
	                           '$sportname')";

	// run the query on the database
	$setteamresult = db_query($setteamquery);

	/* Get the team id     */
	$lastinsert = mysql_insert_id();
	$addselfquery = "INSERT INTO tblkpTeams (
	                teamid, userid
	                ) VALUES ( $lastinsert
	                           ,'" . get_userid() . "')";

	// run the query on the database
	$addselfresult = db_query($addselfquery);

	/* Now add partner   */

	// add self to new team

	$addpartnerquery = "INSERT INTO tblkpTeams (
	                teamid, userid
	                ) VALUES ( $lastinsert
	                           ,$partnerid)";

	// run the query on the database
	$addpartnerresult = db_query($addpartnerquery);

	// Finally update the rankings for the new team

	//Get the users doubles ranking for each team member

	$usersrankquery = "SELECT tblUserRankings.ranking
	                      FROM tblUserRankings
	                      WHERE (((tblUserRankings.userid)=" . get_userid() . "
	                      Or (tblUserRankings.userid)=$partnerid)
	                      AND ((tblUserRankings.courttypeid)=$sportname)
	                      AND ((tblUserRankings.usertype)=0))";

	$usersrankresult = db_query($usersrankquery);
	$rank1 = mysql_result($usersrankresult, 0);

	$usersrankresult = db_query($usersrankquery);
	$rank2 = mysql_result($usersrankresult, 1);
	$averagerank = ($rank1 + $rank2) / 2;

	$rankquery = "INSERT INTO tblUserRankings (
	                userid, courttypeid, ranking, usertype
	                ) VALUES (
	                          '$lastinsert'
	                          ,'$sportname'
	                           ,'$averagerank'
	                           ,1)";

	$rankresult = db_query($rankquery);

	$teaminfoarray = array (
		$averagerank,
		$lastinsert
	);
	return $teaminfoarray;

}
/*
*******************************************************************************************************
**   makeTeamForPlayers

     creates a team, assigns a reanking and returns the new teamid for two different players

*******************************************************************************************************
*/

function makeTeamForPlayers($sportname, $player1id, $player2id) {

	/* Set the team identifier     */

	$setteamquery = "INSERT INTO tblTeams (
	                courttypeid
	                ) VALUES (
	                           '$sportname')";

	// run the query on the database
	$setteamresult = db_query($setteamquery);

	/* Get the team id     */
	$lastinsert = mysql_insert_id();
	$addselfquery = "INSERT INTO tblkpTeams (
	                teamid, userid
	                ) VALUES ( $lastinsert
	                           ,$player1id)";

	// run the query on the database
	$addselfresult = db_query($addselfquery);

	/* Now add partner   */

	// add self to new team

	$addpartnerquery = "INSERT INTO tblkpTeams (
	                teamid, userid
	                ) VALUES ( $lastinsert
	                           ,$player2id)";

	// run the query on the database
	$addpartnerresult = db_query($addpartnerquery);

	// Finally update the rankings for the new team

	//Get the users doubles ranking for each team member

	$usersrankquery = "SELECT tblUserRankings.ranking
	                      FROM tblUserRankings
	                      WHERE (((tblUserRankings.userid)=$player1id
	                      Or (tblUserRankings.userid)=$player2id)
	                      AND ((tblUserRankings.courttypeid)=$sportname)
	                      AND ((tblUserRankings.usertype)=0))";

	$usersrankresult = db_query($usersrankquery);
	$rank1 = mysql_result($usersrankresult, 0);

	$usersrankresult = db_query($usersrankquery);
	$rank2 = mysql_result($usersrankresult, 1);
	$averagerank = ($rank1 + $rank2) / 2;

	$rankquery = "INSERT INTO tblUserRankings (
	                userid, courttypeid, ranking, usertype
	                ) VALUES (
	                          '$lastinsert'
	                          ,'$sportname'
	                           ,'$averagerank'
	                           ,1)";

	$rankresult = db_query($rankquery);

	$teaminfoarray = array (
		$averagerank,
		$lastinsert
	);
	return $teaminfoarray;

}

/**
 * This is really just an array funnction that will return the first element that is a duplication in the list
 */
function findSelfTeam($array) {

	while ($teamid = array_pop($array)) {
		if (in_array($teamid, $array)) {
			return $teamid;
		}
	}
}

/************************************************************************************************************************/
/*
  This function is called after any reservation is made where the admin is available for a lesson

*/

function email_players_about_lesson($resid) {

	$rquery = "SELECT courts.courtname, reservations.time, users.firstname, users.lastname, courttype.courttypeid, rankings.ranking, reservations.matchtype, users.email, users.homephone, users.cellphone, users.workphone
	                 FROM tblCourts courts, tblReservations reservations, tblUsers users, tblCourtType courttype, tblUserRankings rankings, tblkpUserReservations reservationdetails
					 WHERE users.userid = rankings.userid
					 AND reservations.courtid = courts.courtid
	                 AND reservationdetails.reservationid = reservations.reservationid
	                 AND courttype.courttypeid = rankings.courttypeid
	                 AND courts.courttypeid = courttype.courttypeid
	                 AND reservationdetails.userid = users.userid
	                 AND reservations.reservationid = $resid
					 AND rankings.usertype=0";

	$rresult = db_query($rquery);
	$robj = mysql_fetch_object($rresult);
	$var = new Object;

	/* email the user with the new account information    */

	$var->firstname = $robj->firstname;
	$var->lastname = $robj->lastname;
	$var->email = $robj->email;
	$var->homephone = $robj->homephone;
	$var->cellphone = $robj->cellphone;
	$var->workphone = $robj->workphone;
	$var->courtname = $robj->courtname;
	$var->time = gmdate("l F j g:i a", $robj->time);
	$var->timestamp = $robj->time;
	$var->dns = $_SESSION["CFG"]["dns"];
	$var->wwwroot = $_SESSION["CFG"]["wwwroot"];
	$var->fullname = $robj->firstname . " " . $robj->lastname;
	$var->support = $_SESSION["CFG"]["support"];

	$var->signupurl = "http://".$var->dns."".$var->wwwroot."/users/court_reservation.php?time=".$var->timestamp."&courtid=".$var->courtid."&userid=".$var->userid;
	
	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";
	$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/lesson_wanted.php", $var);

	//Now get all players who receive players wanted notifications at the club
	$emailidquery = "SELECT tblUsers.firstname, tblUsers.lastname, tblUsers.email
	                        FROM tblUsers, tblClubUser
	                        WHERE tblClubUser.recemail='y'
							AND  tblUsers.userid = tblClubUser.userid
	                        AND tblClubUser.clubid=" . get_clubid() . "
	                        AND tblUsers.userid != " . get_userid() . "
	                        AND tblClubUser.enable='y'
							AND tblClubUser.enddate IS NULL";

	// run the query on the database
	$emailidresult = db_query($emailidquery);

	while ($emailidrow = db_fetch_row($emailidresult)) {

		//Prepare the message
		$message = "Hello $emailidrow[0],\n";
		$message .= "$emailbody";

		
		if( isDebugEnabled(1) ) logMessage($message);
		mail("$emailidrow[0] $emailidrow[1] <$emailidrow[2]>", "$clubfullname -- Lesson Available", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

	}

}
/************************************************************************************************************************/
/*
  This function is called after any reservation is made where the user is looking for a match
  
  Email Types:
  
  1 = Within club range
  2=  Buddies
  3 = Whole Club (valid ranges and enablement assumed)

*/
function email_players($resid, $emailType) {


	if( isDebugEnabled(1) ) logMessage("Emailing Players about reservation id: $resid for a $emailType kind of email");
	
	/* load up the reservation infomation   */

	//Check to see if the reservation is for a doubles court
	$usertypequery = "SELECT usertype FROM tblReservations WHERE reservationid=$resid";
	$usertyperesult = db_query($usertypequery);
	$usertypeval = mysql_result($usertyperesult, 0);

	
	if ($usertypeval == 0) {
		//email about a singles court

		$rquery = "SELECT courts.courtname, courts.courtid, reservations.time, users.userid, users.firstname, users.lastname, courttype.courttypeid, rankings.ranking,  users.email, users.homephone, users.cellphone, users.workphone, matchtype.name
		                 FROM tblCourts courts, tblReservations reservations, tblUsers users, tblCourtType courttype, tblUserRankings rankings, tblkpUserReservations reservationdetails, tblMatchType matchtype
						 WHERE users.userid = rankings.userid
						 AND reservations.courtid = courts.courtid
		                 AND reservationdetails.reservationid = reservations.reservationid
		                 AND courttype.courttypeid = rankings.courttypeid
		                 AND courts.courttypeid = courttype.courttypeid
		                 AND reservationdetails.userid = users.userid
						 AND matchtype.id  = reservations.matchtype
		                 AND reservations.reservationid = $resid
						 AND rankings.usertype=0";

		$rresult = db_query($rquery);

		$robj = mysql_fetch_object($rresult);
		$var = new Object;

		if( isDebugEnabled(1) ) logMessage("Courtid ".$robj->courtid);
		/* email the user with the new account information    */

		$var->userid = $robj->userid;
		$var->firstname = $robj->firstname;
		$var->lastname = $robj->lastname;
		$var->email = $robj->email;
		$var->homephone = $robj->homephone;
		$var->cellphone = $robj->cellphone;
		$var->workphone = $robj->workphone;
		$var->ranking = $robj->ranking;
		$var->courtname = $robj->courtname;
		$var->courtid = $robj->courtid;
		$var->matchtype = $robj->name;
		$var->time = gmdate("l F j g:i a", $robj->time);
		$var->timestamp = $robj->time;
		$var->dns = $_SESSION["CFG"]["dns"];
		$var->wwwroot = $_SESSION["CFG"]["wwwroot"];
		$var->fullname = $robj->firstname . " " . $robj->lastname;
		$var->support = $_SESSION["CFG"]["support"];
		
		//Set the URL
		//sample: http://localhost/clubpro/users/court_reservation.php?time=1285430400&courtid=5&userid=3
		$var->signupurl = "http://".$var->dns."".$var->wwwroot."/users/court_reservation.php?time=".$var->timestamp."&courtid=".$var->courtid."&userid=".$var->userid;

		$clubfullname = get_clubname();
		$var->clubfullname = $clubfullname;
		$var->clubadminemail = "PlayerMailer@sportsynergy.net";
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/singles_wanted.php", $var);

		if ($emailType == "3") {

			$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
	                       FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser
						   WHERE users.userid = rankings.userid
						   AND users.userid = clubuser.userid
	                       AND clubuser.clubid=" . get_clubid() . "
	                       AND clubuser.recemail='y'
	                       AND rankings.courttypeid=$robj->courttypeid
	                       AND users.userid != " . get_userid() . "
	                       AND clubuser.enable= 'y'
						   AND clubuser.enddate IS NULL";
		}
		elseif ($emailType == "2") {

			$emailidquery = "SELECT users.firstname, users.lastname, users.email
			                        FROM tblUsers users, tblBuddies buddies, tblClubUser clubuser
									WHERE users.userid = buddies.buddyid
			                        AND users.userid = clubuser.userid
	                                AND clubuser.clubid=" . get_clubid() . "
			                        AND users.userid!=" . get_userid() . "
			                        AND buddies.userid=" . get_userid() . "
			                        AND clubuser.enable= 'y'
									AND clubuser.enddate IS NULL";

		} 
		elseif ($emailType == "1") {
			
			//Get the rankdev of the club
			$rankdevquery = "SELECT rankdev FROM tblClubs WHERE clubid=" . get_clubid() . "";

			// run the query on the database
			$rankdevresult = db_query($rankdevquery);
			$rankdevval = mysql_result($rankdevresult, 0);

			$highrange = $robj->ranking + $rankdevval;
			$lowrange = $robj->ranking - $rankdevval;

			//Now get all players who receive players wanted notifications at the club and are within
			//the set skill range

			$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
				                       FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser
									   WHERE users.userid = rankings.userid
									   AND users.userid = clubuser.userid
				                       AND clubuser.clubid=" . get_clubid() . "
				                       AND rankings.ranking>$lowrange
				                       AND rankings.ranking<$highrange
				                       AND clubuser.recemail='y'
				                       AND rankings.courttypeid=$robj->courttypeid
				                       AND users.userid != " . get_userid() . "
				                       AND clubuser.enable='y'
									   AND clubuser.enddate IS NULL";

		}

		// run the query on the database
		$emailidresult = db_query($emailidquery);

		while ($emailidrow = db_fetch_row($emailidresult)) {

			//Prepare the message
			$message = "Hello $emailidrow[0],\n";
			$message .= "$emailbody";

			if( isDebugEnabled(1) ) logMessage($message);
			mail("$emailidrow[0] $emailidrow[1] <$emailidrow[2]>", "$clubfullname -- Player's MarketPlace", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

		}


	}

	//email about a doubles court
	else {


		$rquery = "SELECT DISTINCTROW 
							courts.courtname, 
							courts.courttypeid, 
							reservations.time, 
							users.firstname, 
							users.lastname,
							courts.courtid, 
							users.userid, 
							matchtype.name,
							teamdetails.teamid
		              FROM 
							tblReservations reservations, 
							tblkpUserReservations reservationdetails, 
							tblkpTeams teamdetails, 
							tblUsers users,  
							tblCourts courts, 
							tblMatchType matchtype,
							tblClubUser clubuser
					  WHERE reservationdetails.reservationid = reservations.reservationid
					  AND teamdetails.teamid = reservationdetails.userid
					  AND users.userid = teamdetails.userid
					  AND courts.courtid = reservations.courtid
					  AND matchtype.id = reservations.matchtype
					  AND reservationdetails.reservationid=$resid
					  AND users.userid = clubuser.userid
					  AND clubuser.clubid=" . get_clubid() ;
			
					  
		$rresult = db_query($rquery);
		$robj = mysql_fetch_object($rresult);

		$extraPlayerQuery = "SELECT reservationdetails.userid
		                        FROM tblReservations reservations, tblkpUserReservations reservationdetails
		                        WHERE reservations.reservationid = reservationdetails.reservationid
		                        AND reservationdetails.reservationid=$resid
		                        AND reservationdetails.usertype=0
								ORDER BY reservationdetails.userid";

		$extraPlayerResult = db_query($extraPlayerQuery);
		$extraPlayerArray = mysql_fetch_array($extraPlayerResult);
		
		//Get Court Type.  The reason this is done here is that in the cases of partial 
		//reservations, this is empty in the query above.
		
		$ctQuery = "SELECT courts.courttypeid
		                        FROM tblReservations reservations, tblCourts courts
		                        WHERE reservations.reservationid=$resid
		                        AND reservations.courtid = courts.courtid";

		$ctResult = db_query($ctQuery);
		$courtType = mysql_result($ctResult, 0);
		

		$player1 = $robj->userid;
		$var = new Object;

		/* email the user with the new account information    */

		$var->firstname1 = $robj->firstname;
		$var->lastname1 = $robj->lastname;
		$var->fullname1 = $robj->firstname . " " . $robj->lastname;
	    $var->teamid = $robj->teamid;

	    
		//Get the next result
		$robj = mysql_fetch_object($rresult);

		$player2 = $robj->userid;
		$var->firstname2 = $robj->firstname;
		$var->lastname2 = $robj->lastname;
		$var->fullname2 = $robj->firstname . " " . $robj->lastname;

		$var->courtid = $robj->courtid;
		$var->courtname = $robj->courtname;
		$var->matchtype = $robj->name;
		$var->time = gmdate("l F j g:i a", $robj->time);
		$var->timestamp = $robj->time;
		$var->dns = $_SESSION["CFG"]["dns"];
		$var->wwwroot = $_SESSION["CFG"]["wwwroot"];

		$var->support = $_SESSION["CFG"]["support"];

		$clubfullname = get_clubname();
		$var->clubfullname = $clubfullname;
		$var->clubadminemail = "PlayerMailer@sportsynergy.net";
		
		

		//if this reservation is made with a player looking for a partner, something will
		//be set in the extraPlayerQuery, if so display a different email message .
		//  $extraPlayerobj->userid will be 0 when taking a player removes himself
		//from a reservation where he was looking for a match.

		$extraPlayerUserId = 0;

		
		//Check for three players wanted
		if(db_num_rows($extraPlayerResult)==2 && $extraPlayerArray['userid']==0){
			
			
		//Obtain the court and matchtype information
		$rquery = "SELECT courts.courtname, matchtype.name, reservations.time, courts.courtid
					FROM tblMatchType matchtype, tblCourts courts, tblReservations reservations 
					WHERE reservations.reservationid=$resid
					AND reservations.courtid = courts.courtid
					AND matchtype.id = reservations.matchtype";
					
		$rresult = db_query($rquery);
		$robj = mysql_fetch_object($rresult);
		$var->courtname = $robj->courtname;
		$var->courtid = $robj->courtid;
		$var->matchtype = $robj->name;
		$var->timestamp = $robj->time;
		$var->time = gmdate("l F j g:i a", $robj->time);
		
		$extraPlayerArray = mysql_fetch_array($extraPlayerResult);
		
		$var->userid = $extraPlayerArray['userid'];
		
		$partnerQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                           FROM tblUsers
                           WHERE (((tblUsers.userid)=$var->userid))";
		
		$partnerResult = db_query($partnerQuery);
		$partnerobj = mysql_fetch_object($partnerResult);
		
		$var->single1 = $partnerobj->firstname . " " . $partnerobj->lastname;
		
		
		$var->signupurl = "http://".$var->dns."".$var->wwwroot."/users/court_reservation.php?time=".$var->timestamp."&courtid=".$var->courtid."&userid=".$var->userid;	
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/threePlayersWanted.php", $var);
			
		}
		//Check for two players wanted
		elseif(db_num_rows($extraPlayerResult)==2 && $extraPlayerArray['userid']!=0){
		
		//Obtain the court and matchtype information
		$rquery = "SELECT courts.courtname, matchtype.name, reservations.time 
					FROM tblMatchType matchtype, tblCourts courts, tblReservations reservations 
					WHERE reservations.reservationid=$resid
					AND reservations.courtid = courts.courtid
					AND matchtype.id = reservations.matchtype";
					
		$rresult = db_query($rquery);
		$robj = mysql_fetch_object($rresult);
		$var->courtname = $robj->courtname;
		$var->matchtype = $robj->name;
		$var->time = gmdate("l F j g:i a", $robj->time);
		
		//Single Player One
		$singlePlayerOne = $extraPlayerArray['userid'];
		$playerOneQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                           FROM tblUsers
                           WHERE ((tblUsers.userid)=$singlePlayerOne) ";
                           
		$playerOneResult = db_query($playerOneQuery);
		$playerOneobj = mysql_fetch_object($playerOneResult);
		$var->single1 = $playerOneobj->firstname . " " . $playerOneobj->lastname;
		
		//Single Player Two
		$extraPlayerArray = mysql_fetch_array($extraPlayerResult);
		$singlePlayerTwo = $extraPlayerArray['userid'];
		$playerTwoQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                           FROM tblUsers
                           WHERE ((tblUsers.userid)=$singlePlayerTwo) ";
		$playerTwoResult = db_query($playerTwoQuery);
		$playerTwoobj = mysql_fetch_object($playerTwoResult);
		
		$var->single2 = $playerTwoobj->firstname . " " . $playerTwoobj->lastname;
		
		$var->signupurl = "http://".$var->dns."".$var->wwwroot."/users/court_reservation.php?time=".$var->timestamp."&courtid=".$var->courtid."&userid=".$singlePlayerOne;
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/twoPlayersWanted.php", $var);
			
		}
		//Check for one player wanted
		elseif ($extraPlayerArray['userid'] != null && $extraPlayerArray['userid'] != 0) {

			$extraPlayerUserId = $extraPlayerArray['userid'];
			
			$partnerQuery = "SELECT tblUsers.firstname, tblUsers.lastname
			                           FROM tblUsers
			                           WHERE (((tblUsers.userid)=$extraPlayerUserId))";
			$partnerResult = db_query($partnerQuery);
			$partnerobj = mysql_fetch_object($partnerResult);

			$var->partner = $partnerobj->firstname . " " . $partnerobj->lastname;
			$var->signupurl = "http://".$var->dns."".$var->wwwroot."/users/court_reservation.php?time=".$var->timestamp."&courtid=".$var->courtid."&userid=".$extraPlayerUserId;
			$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/partner_wanted.php", $var);

		} 
		//Default for team wanted
		else {
			
			//guard against certain types of situations
			if( empty($var->timestamp) || empty($var->courtid) || empty($var->teamid) ){
				return;
			}
			$var->signupurl = "http://".$var->dns."".$var->wwwroot."/users/court_reservation.php?time=".$var->timestamp."&courtid=".$var->courtid."&userid=".$var->teamid;
			$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/doubles_wanted.php", $var);
		}

		//INitialize to avoid situations where these are not set, such as when emails are
		//being sent and not all players are set. 
		if( !isset($player1) ){
			$player1 = 0;
		}
		if( !isset($player2) ){
			$player2 = 0;
		}


		/*  
		 * Email Advertisments are either set to the whole club or the list of buddies 
		 * of the person making the reservation. 
		 */
		if ($emailType == "3") {

			$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
	                       FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser
						   WHERE users.userid = rankings.userid
						   AND users.userid = clubuser.userid
	                       AND clubuser.clubid=" . get_clubid() . "
	                       AND clubuser.recemail='y'
	                       AND rankings.courttypeid=$courtType
						   AND rankings.usertype=0
	                       AND users.userid != " . get_userid() . "
	                       AND clubuser.enable='y'
						   AND clubuser.enddate IS NULL";
		}
		elseif ($emailType == "2") {

			$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
			                        FROM tblUsers users, tblBuddies buddies, tblClubUser clubuser
			 						WHERE users.userid = buddies.buddyid
									AND users.userid = clubuser.userid
			                        AND clubuser.clubid=" . get_clubid() . "
			                        AND users.userid!=" . get_userid() . "
									AND users.userid NOT IN ($player1, $player2, $extraPlayerUserId)
			                        AND buddies.userid=" . get_userid() . "
			                        AND clubuser.enable='y'
									AND clubuser.enddate IS NULL";

		} else {

			//Get the rankdev of the club
			$rankdevquery = "SELECT rankdev FROM tblClubs WHERE clubid=" . get_clubid() . "";

			// run the query on the database
			$rankdevresult = db_query($rankdevquery);
			$rankdevval = mysql_result($rankdevresult, 0);

			// Get the Ranking of the current user (this based on the resid)
			$query = "SELECT rankings.ranking 
						FROM tblUserRankings rankings, tblReservations reservations, tblCourts courts
						WHERE reservations.reservationid = $resid
						AND courts.courtid = reservations.courtid
						AND courts.courttypeid = rankings.courttypeid
						AND rankings.usertype = 0
						AND rankings.userid = ".get_userid();
			
			
			$result = db_query($query);
			$ranking = mysql_result($result, 0);
			
			$highrange = $ranking + $rankdevval;
			$lowrange = $ranking - $rankdevval;


			//Now get all players who receive players wanted notifications at the club and are within
			//the set skill range
			$emailidquery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
				                         FROM tblUsers users, tblTeams teams, tblkpTeams teamdetails, tblUserRankings rankings, tblClubUser clubuser
										 WHERE users.userid = teamdetails.userid
				                         AND teams.teamid = teamdetails.teamid
				                         AND rankings.userid = teamdetails.teamid
									     AND users.userid = clubuser.userid
				                         AND clubuser.clubid=" . get_clubid() . "
				                         AND clubuser.recemail='y'
				                         AND rankings.ranking>$lowrange
				                         AND rankings.ranking<$highrange
										 AND rankings.courttypeid=$courtType
				                         AND rankings.usertype =1
				                         AND users.userid != $player1
				                         AND users.userid != $player2
										 AND users.userid != $extraPlayerUserId
				                         AND clubuser.enable='y'
										 AND clubuser.enddate IS NULL";
			
			
		}

		
		// run the query on the database
		$emailidresult = db_query($emailidquery);

		while ($emailidrow = db_fetch_row($emailidresult)) {

			//Prepare the message
			$message = "Hello $emailidrow[0],\n";
			$message .= "$emailbody";

			if( isDebugEnabled(1) ) logMessage($message);
			mail("$emailidrow[0] $emailidrow[1] <$emailidrow[2]>", "$clubfullname -- Player's MarketPlace", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

		}

	}

}

/************************************************************************************************************************/
/*
    This will advertise the reservation to box memebers (who haven't already played the current user)
*/

function email_boxmembers($resid, $boxid) {

	/* load up the reservation infomation   */
	$rquery = "SELECT courts.courtname, reservations.time, users.firstname, users.lastname, rankings.ranking, reservations.matchtype, users.email, users.homephone, users.cellphone, users.workphone
	                  FROM tblCourts courts, tblReservations reservations, tblUsers users, tblUserRankings rankings, tblkpUserReservations reservationdetails, tblCourtType courttype
					  WHERE users.userid = rankings.userid
	                  AND courts.courtid = reservations.courtid
	                  AND reservationdetails.reservationid = reservations.reservationid
	                  AND courttype.courttypeid = rankings.courttypeid
	                  AND courts.courttypeid = courttype.courttypeid
	                  AND users.userid = reservationdetails.userid
	                  AND reservations.reservationid=$resid
					  AND rankings.usertype=0";

	$rresult = db_query($rquery);
	//Get the next result
	$robj = mysql_fetch_object($rresult);

	/* email the user with the new account information    */
	$var = new Object;
	$var->matchtype = "league";
	$var->firstname = $robj->firstname;
	$var->lastname = $robj->lastname;
	$var->email = $robj->email;
	$var->homephone = $robj->homephone;
	$var->cellphone = $robj->cellphone;
	$var->workphone = $robj->workphone;
	$var->ranking = $robj->ranking;
	$var->courtname = $robj->courtname;
	$var->time = gmdate("l F j g:i a", $robj->time);
	$var->timestamp = $robj->time;
	$var->dns = $_SESSION["CFG"]["dns"];
	$var->wwwroot = $_SESSION["CFG"]["wwwroot"];
	$var->fullname = $robj->firstname . " " . $robj->lastname;
	$var->support = $_SESSION["CFG"]["support"];

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";
	$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/singles_wanted.php", $var);

	//Now get all boxmembers

	$emailidquery = "SELECT users.userid, users.firstname, users.lastname, users.email
	                        FROM tblUsers users,  tblkpBoxLeagues boxdetails
							WHERE users.userid = boxdetails.userid
	                        AND boxdetails.boxid=$boxid
							AND users.enddate IS NULL
							AND users.userid<>" . get_userid();

	// run the query on the database
	$emailidresult = db_query($emailidquery);
	while ($emailidrow = mysql_fetch_array($emailidresult)) {

		if (!hasPlayedBoxWith(get_userid(), $emailidrow[userid], $boxid)) {

			//Prepare the message
			$message = "Hello $emailidrow[1],\n";
			$message .= "$emailbody";
			
			if( isDebugEnabled(1) ) logMessage($message);
			mail("$emailidrow[1] $emailidrow[2] <$emailidrow[3]>", "$clubfullname -- Player's MarketPlace", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");
		}

	}
}

/************************************************************************************************************************/

function confirm_singles($resid, $isNewReservation) {

	$rquery = "SELECT courts.courtname, reservations.time, users.firstname, users.lastname, users.email, courts.courtid, reservations.matchtype, matchtype.name, reservations.usertype
			           FROM tblCourts courts, tblReservations reservations, tblUsers users, tblkpUserReservations reservationdetails, tblMatchType matchtype, tblClubUser clubuser
			           WHERE courts.courtid = reservations.courtid
			           AND users.userid = reservationdetails.userid
			           AND reservations.reservationid = reservationdetails.reservationid
			           AND reservations.reservationid=$resid
					   AND reservations.matchtype = matchtype.id
					   AND users.userid = clubuser.userid
			           AND clubuser.clubid=" . get_clubid();

	$rresult = db_query($rquery);
	$robj = mysql_fetch_object($rresult);

	/* email the user with the new account information    */
	$var = new Object;

	$var->courtname = $robj->courtname;
	$var->time = gmdate("l F j g:i a", $robj->time);
	$var->timestamp = $robj->time;
	$var->dns = $_SESSION["CFG"]["dns"];
	$var->wwwroot = $_SESSION["CFG"]["wwwroot"];
	$var->support = $_SESSION["CFG"]["support"];
	$var->courtid = $robj->courtid;
	$var->matchtype = $robj->name;

	//Get the first player
	$var->firstname1 = $robj->firstname;
	$var->lastname1 = $robj->lastname;
	$var->fullname1 = $robj->firstname . " " . $robj->lastname;

	//Get the second player
	$robj = mysql_fetch_object($rresult);

	$var->firstname2 = $robj->firstname;
	$var->lastname2 = $robj->lastname;
	$var->fullname2 = $robj->firstname . " " . $robj->lastname;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";

	if (db_num_rows($rresult) == 1) {
		
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_singles_looking.php", $var);
	
	}
	elseif ($robj->matchtype == 4) {
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_lesson.php", $var);
	}
	elseif ($robj->matchtype == 5) {
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_solo.php", $var);
	} else {
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_singles.php", $var);
	}

	// Set the Subject
	if ($isNewReservation) {
		$subject = "Court Reservation Notice";
	} else {
		$subject = "Updated Court Reservation Notice";
	}

	//Reset the result pointer to the begining
	mysql_data_seek($rresult, 0);

	while ($emailidrow = db_fetch_row($rresult)) {

		//If they don't even bother to put in an email address
		//don't waste your time trying to send them an email.
		//print "This is my email: $emailidrow[4]\n";
		if (!empty ($emailidrow[4])) {
			//Prepare the message
			$message = "Hello $emailidrow[2],\n";
			$message .= "$emailbody";

			mail("$emailidrow[2] $emailidrow[3] <$emailidrow[4]>", $subject, $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

		}
	}
}

/************************************************************************************************************************/

function cancel_singles($resid) {

	$rquery = "SELECT DISTINCTROW courts.courtname, reservations.time, users.firstname, users.lastname, users.email, courts.courtid, reservations.matchtype, matchtype.name
	           FROM tblCourts courts, tblUsers users, tblReservations reservations, tblkpUserReservations reservationdetails, tblUserRankings rankings, tblMatchType matchtype, tblClubUser clubuser
			   WHERE courts.courtid = reservations.courtid
			   AND rankings.userid = users.userid
			   AND reservationdetails.userid = users.userid
			   AND reservations.reservationid = reservationdetails.reservationid
	           AND reservations.reservationid=$resid
	           AND reservations.matchtype = matchtype.id
			   AND users.userid = clubuser.userid
			   AND clubuser.clubid=" . get_clubid();

	$rresult = db_query($rquery);
	$robj = mysql_fetch_object($rresult);

	/* email the user with the new account information    */
	$var = new Object;

	$var->courtname = $robj->courtname;
	$var->time = gmdate("l F j g:i a", $robj->time);
	$var->timestamp = $robj->time;
	$var->dns = $_SESSION["CFG"]["dns"];
	$var->wwwroot = $_SESSION["CFG"]["wwwroot"];
	$var->support = $_SESSION["CFG"]["support"];
	$var->courtid = $robj->courtid;
	$var->matchtype = $robj->name;

	//Get the first player
	$var->firstname1 = $robj->firstname;
	$var->lastname1 = $robj->lastname;
	$var->fullname1 = $robj->firstname . " " . $robj->lastname;

	//Get the second player
	$robj = mysql_fetch_object($rresult);

	$var->firstname2 = $robj->firstname;
	$var->lastname2 = $robj->lastname;
	$var->fullname2 = $robj->firstname . " " . $robj->lastname;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";

	if (db_num_rows($rresult) == 1) {
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/cancel_singles_looking.php", $var);
	} else
		if ($robj->matchtype == 4) {
			$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/cancel_lesson.php", $var);
		}
	elseif ($robj->matchtype == 5) {
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/cancel_solo.php", $var);
	} else {
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/cancel_singles.php", $var);
	}

	//Reset the result pointer to the begining
	mysql_data_seek($rresult, 0);

	while ($emailidrow = db_fetch_row($rresult)) {

		//Prepare the message
		$message = "Hello $emailidrow[2],\n";
		$message .= "$emailbody";

		if (!empty ($emailidrow[4])) {
			mail("$emailidrow[2] $emailidrow[3] <$emailidrow[4]>", "Court Cancellation Notice", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");
		}

	}

}


/********************************************************************************************
	This function only sends out emails to those players who are currently in the
	reservation, this includes players in a team or singles players who are still 
	looking for partner.
*/


function confirm_doubles($resid, $isNewReservation) {

	
	if( isDebugEnabled(1) ) logMessage("applicationlib.confirm_doubles: confirming reservation $resid isNewReservation $isNewReservation");
	
	$var = new Object;

	//Obtain the court and matchtype information
	$timeQuery = "SELECT courts.courtname, matchtype.name, reservations.time 
				FROM tblMatchType matchtype, tblCourts courts, tblReservations reservations 
				WHERE reservations.reservationid=$resid
				AND reservations.courtid = courts.courtid
				AND matchtype.id = reservations.matchtype";
	
	$timeResult = db_query($timeQuery);
	$timeObject = mysql_fetch_object($timeResult);
	
	$var->courtname = $timeObject->courtname;
	$var->matchtype = $timeObject->name;
	$var->time = gmdate("l F j g:i a", $timeObject->time);


	//Obtain player information  
	$playerQuery = "SELECT DISTINCTROW users.firstname, users.lastname, users.email
	            FROM tblReservations reservations, tblUsers users, tblkpTeams teamdetails, tblkpUserReservations reservationdetails
				WHERE reservationdetails.reservationid = reservations.reservationid
	 			AND teamdetails.teamid = reservationdetails.userid
	            AND users.userid = teamdetails.userid
	            AND reservationdetails.reservationid=$resid
				AND reservationdetails.usertype = 1";
	            
	$playerResult = db_query($playerQuery);
	$playerObject = mysql_fetch_object($playerResult);
	$numofrows = mysql_num_rows($playerResult);

	$var->dns = $_SESSION["CFG"]["dns"];
	$var->wwwroot = $_SESSION["CFG"]["wwwroot"];
	$var->support = $_SESSION["CFG"]["support"];


	//Get the first player of team 1
	$var->firstname1 = $playerObject->firstname;
	$var->lastname1 = $playerObject->lastname;
	$var->fullname1 = $playerObject->firstname . " " . $playerObject->lastname;

	//Get the second player of team 1
	$playerObject = mysql_fetch_object($playerResult);
	$var->firstname2 = $playerObject->firstname;
	$var->lastname2 = $playerObject->lastname;
	$var->fullname2 = $playerObject->firstname . " " . $playerObject->lastname;

	//Get the first player of team 2
	$playerObject = mysql_fetch_object($playerResult);
	$var->firstname3 = $playerObject->firstname;
	$var->lastname3 = $playerObject->lastname;
	$var->fullname3 = $playerObject->firstname . " " . $playerObject->lastname;

	//Get the second player of team 2
	$playerObject = mysql_fetch_object($playerResult);
	$var->firstname4 = $playerObject->firstname;
	$var->lastname4 = $playerObject->lastname;
	$var->fullname4 = $playerObject->firstname . " " . $playerObject->lastname;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";

	// Set the Subject
	if ($isNewReservation) {
		$subject = "Court Reservation Notice";
	} else {
		$subject = "Updated Court Reservation Notice";
	}

	//Check to see if there is a single wanting to play
	$extraPlayerQuery = "SELECT reservationdetails.userid, users.firstname, users.lastname, users.email
		                        FROM tblReservations reservations, tblkpUserReservations reservationdetails, tblUsers users
		                        WHERE reservations.reservationid = reservationdetails.reservationid
							    AND reservationdetails.userid = users.userid
		                        AND reservationdetails.reservationid=$resid
		                        AND reservationdetails.usertype=0";


	$extraPlayerResult = db_query($extraPlayerQuery);
	$extraPlayerobj = mysql_fetch_object($extraPlayerResult);


	//Prepare and send emails to single player where there is just one player in the whole reservation
	if(mysql_num_rows($extraPlayerResult)==1 && mysql_num_rows($playerResult)==0){
		
		$var->partner = $extraPlayerobj->firstname . " " . $extraPlayerobj->lastname;
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_doubles_looking_for_three.php", $var);

		//Prepare the message
		$message = "Hello $extraPlayerobj->firstname,\n";
		$message .= "$emailbody";
		
		if( isDebugEnabled(1) ) logMessage($emailbody);
		mail("$extraPlayerobj->firstname $extraPlayerobj->lastname <$extraPlayerobj->email>", $subject, $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");
		
	}

	//Prepare and send emails to single players where there is more than one person looking for a partner
	elseif(mysql_num_rows($extraPlayerResult)==2){
		
		$var->fullname1 = getFullNameForUserId($extraPlayerobj->userid);
		
		//Get the next player
		$extraPlayerobj = mysql_fetch_object($extraPlayerResult);
		$var->fullname2 = getFullNameForUserId($extraPlayerobj->userid);
		
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_doubles_for_players_looking.php", $var);
		
		//Reset Counter
		if( mysql_num_rows($extraPlayerResult)>0) mysql_data_seek($extraPlayerResult, 0);
		
		//Get First Player
		$message = "Hello $extraPlayerResult->firstname,\n";
		$message .= "$emailbody";
		
		if( isDebugEnabled(1) ) logMessage($emailbody);
		mail("$extraPlayerobj->firstname $extraPlayerobj->lastname <$extraPlayerobj->email>", $subject, $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

		//Get next player
		$extraPlayerobj = mysql_fetch_object($extraPlayerResult);
		$message = "Hello $extraPlayerResult->firstname,\n";
		$message .= "$emailbody";
		mail("$extraPlayerobj->firstname $extraPlayerobj->lastname <$extraPlayerobj->email>", $subject, $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");
		
		
	}
	
	//Prepare and send emails to single player where there is only one person needing a partner
	elseif(mysql_num_rows($extraPlayerResult)==1) {

		$var->partner = $extraPlayerobj->firstname . " " . $extraPlayerobj->lastname;
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_double_for_player_looking.php", $var);

		//Prepare the message
		$message = "Hello $extraPlayerobj->firstname,\n";
		$message .= "$emailbody";
		if( isDebugEnabled(1) ) logMessage($emailbody);
		mail("$extraPlayerobj->firstname $extraPlayerobj->lastname <$extraPlayerobj->email>", $subject, $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

	}
	
	//Now Send emails out to players that acutally are in a team
	else{
		
		// when only two rows returned this is a team looking for another team.
		if ($numofrows == 2) {
			$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_doubles_for_team_looking.php", $var);
		} 
		//Send out emails to four players, the variables were set earlier in this function
		else {
			$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/confirm_doubles.php", $var);
		}
	}
	
	//Send out emails to the teams
	//Reset the result pointer to the begining
	if( mysql_num_rows($playerResult)>0) mysql_data_seek($playerResult, 0);

	while ($playerObject = mysql_fetch_object($playerResult)) {

		//Prepare the message
		$message = "Hello $playerObject->firstname,\n";
		$message .= "$emailbody";
		if(isDebugEnabled(1) ) logMessage($emailbody);
		mail("$playerObject->firstname $playerObject->lastname <$playerObject->email>", $subject, $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");
	}
	


}

/************************************************************************************************************************/

function cancel_doubles($resid) {

	$rquery = "SELECT courts.courtname, reservations.time, users.firstname, users.lastname, users.email, courts.courtid, reservations.matchtype, matchtype.name
	            FROM tblCourts courts, tblReservations reservations, tblUsers users, tblkpUserReservations reservationdetails, tblUserRankings rankings, tblkpTeams teamdetails, tblMatchType matchtype, tblClubUser clubuser
				WHERE reservations.reservationid = reservationdetails.reservationid
	            AND teamdetails.teamid = reservationdetails.userid
	            AND users.userid = teamdetails.userid
	            AND rankings.userid = reservationdetails.userid
	            AND courts.courtid = reservations.courtid
			    AND reservations.matchtype = matchtype.id
	            AND reservations.reservationid=$resid
				AND users.userid = clubuser.userid
	            AND clubuser.clubid=" . get_clubid() . "
	            AND reservationdetails.usertype=1
	            AND rankings.usertype=1";

	$rresult = db_query($rquery);
	$robj = mysql_fetch_object($rresult);

	/* email the user with the new account information    */
	$var = new Object;

	$var->courtname = $robj->courtname;
	$var->time = gmdate("l F j g:i a", $robj->time);
	$var->timestamp = $robj->time;
	$var->dns = $_SESSION["CFG"]["dns"];
	$var->wwwroot = $_SESSION["CFG"]["wwwroot"];
	$var->courtid = $robj->courtid;
	$var->support = $_SESSION["CFG"]["support"];
	$var->matchtype = $robj->name;

	//Get the first player of team 1
	$var->firstname1 = $robj->firstname;
	$var->lastname1 = $robj->lastname;
	$var->fullname1 = $robj->firstname . " " . $robj->lastname;

	//Get the second player of team 1
	$robj = mysql_fetch_object($rresult);
	$var->firstname2 = $robj->firstname;
	$var->lastname2 = $robj->lastname;
	$var->fullname2 = $robj->firstname . " " . $robj->lastname;

	//Get the first player of team 2
	$robj = mysql_fetch_object($rresult);
	$var->firstname3 = $robj->firstname;
	$var->lastname3 = $robj->lastname;
	$var->fullname3 = $robj->firstname . " " . $robj->lastname;

	//Get the second player of team 2
	$robj = mysql_fetch_object($rresult);
	$var->firstname4 = $robj->firstname;
	$var->lastname4 = $robj->lastname;
	$var->fullname4 = $robj->firstname . " " . $robj->lastname;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";

	if (mysql_num_rows($rresult) == 4) {
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/cancel_doubles.php", $var);
	} else {
		$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/cancel_doubles_looking.php", $var);
	}

	//Reset the result pointer to the begining

	mysql_data_seek($rresult, 0);

	while ($emailidrow = db_fetch_row($rresult)) {

		//Prepare the message
		$message = "Hello $emailidrow[2],\n";
		$message .= "$emailbody";

		mail("$emailidrow[2] $emailidrow[3] <$emailidrow[4]>", "Court Cancellation Notice", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

	}
	

}

/**
 * Sends out emails to the players involved.
*/


function report_scores_singles_simple($wUserid, $lUserid, $wor, $wnr, $lor, $lnr, $score, $matchtype) {

	$rquery = "SELECT users.firstname, users.lastname, users.email
				FROM tblUsers users
				WHERE users.userid = $wUserid
				OR users.userid = $lUserid";
							
	$rresult = db_query($rquery);
	$robj = mysql_fetch_object($rresult);

	/* email the user with the new account information    */
	$var = new Object;
	if ($score == 0) {
		$var->howbad = "pounded";
		$var->loserscore = 0;
	}
	elseif ($score == 2) {
		$var->howbad = "edged out";
		$var->loserscore = 2;
	} else {
		$var->howbad = "defeated";
		$var->loserscore = 1;
	}

	$var->support = $_SESSION["CFG"]["support"];

	$var->winnersold = $wor;
	$var->winnersnew = $wnr;
	$var->losersold = $lor;
	$var->losersnew = $lnr;

	//Get the first player
	$var->winnerfull = getFullNameForUserId($wUserid);	

	//Get the next One
	$var->loserfull = getFullNameForUserId($lUserid);	
	$var->loserscore = $score;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";
	$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/report_scores_singles_simple.php", $var);

	//Reset the result pointer to the begining
	mysql_data_seek($rresult, 0);

	while ($emailidrow = db_fetch_row($rresult)) {

		//Prepare the message
		$message = "Hello $emailidrow[0],\n";
		$message .= "$emailbody";

		mail("$emailidrow[0] $emailidrow[1] <$emailidrow[2]>", "$clubfullname -- Score Report", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

	}
	
	$description = "$var->winnerfull $var->howbad $var->loserfull in a $matchtype match 3-$score ";
	logSiteActivity(get_siteid(), $description);

}

/************************************************************************************************************************/

function report_scores_singles($resid, $wor, $wnr, $lor, $lnr, $score) {

	$rquery = "SELECT DISTINCTROW courts.courtname, reservations.time, users.firstname, users.lastname, users.email, courts.courtid, reservationdetails.outcome, reservations.matchtype, users.gender, matchtype.name
	           FROM tblCourts courts, tblReservations reservations, tblUsers users, tblkpUserReservations reservationdetails, tblUserRankings rankings, tblMatchType matchtype, tblClubUser clubuser
			   WHERE courts.courtid = reservations.courtid
	           AND users.userid = rankings.userid
	           AND users.userid = reservationdetails.userid
	           AND reservations.reservationid = reservationdetails.reservationid
			   AND reservations.matchtype = matchtype.id
	           AND reservations.reservationid=$resid
			   AND users.userid = clubuser.userid
	           AND clubuser.clubid=" . get_clubid() . "
	           ORDER BY reservationdetails.outcome DESC";

	$rresult = db_query($rquery);
	$robj = mysql_fetch_object($rresult);

	/* email the user with the new account information    */
	$var = new Object;

	if ($robj->gender == 1) {
		$winnersex = "him";
	} else {
		$winnersex = "her";
	}

	$robj = mysql_fetch_object($rresult);

	if ($robj->gender == 1) {
		$losersex = "he";
	} else {
		$losersex = "she";
	}

	mysql_data_seek($rresult, 0);
	$robj = mysql_fetch_object($rresult);

	if ($score == 0) {
		$var->howbad1 = "beat";
		$var->howbad2 = "like $losersex owed $winnersex money";
		$var->loserscore = 0;
	}
	elseif ($score == 2) {
		$var->howbad1 = "defeated";
		$var->loserscore = 2;
	} else {
		$var->howbad1 = "defeated";
		$var->loserscore = 1;
	}

	$var->courtname = $robj->courtname;
	$var->matchtype = $robj->name;
	$var->time = gmdate("l F j g:i a", $robj->time);
	$var->date = gmdate("l F j", $robj->time);
	$var->hour = gmdate("g:i a", $robj->time);
	$var->timestamp = $robj->time;
	$var->support = $_SESSION["CFG"]["support"];
	$var->courtid = $robj->courtid;
	$var->winnersold = $wor;
	$var->winnersnew = $wnr;
	$var->losersold = $lor;
	$var->losersnew = $lnr;

	//Get the first player
	$var->winnerfname = $robj->firstname;
	$var->winnerlname = $robj->lastname;
	$var->winnerfull = $robj->firstname . " " . $robj->lastname;

	//Get the second player
	$robj = mysql_fetch_object($rresult);

	$var->loserfname = $robj->firstname;
	$var->loserlname = $robj->lastname;
	$var->loserfull = $robj->firstname . " " . $robj->lastname;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";
	$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/report_scores_singles.php", $var);

	//Reset the result pointer to the begining
	mysql_data_seek($rresult, 0);

	while ($emailidrow = db_fetch_row($rresult)) {

		//Prepare the message
		$message = "Hello $emailidrow[2],\n";
		$message .= "$emailbody";

		mail("$emailidrow[2] $emailidrow[3] <$emailidrow[4]>", "$clubfullname -- Score Report", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

	}
	
	$description = "$var->winnerfull defeated $var->loserfull in a $var->matchtype match 3-$score on $var->courtname $var->date at $var->hour";
	logSiteActivity(get_siteid(), $description);

}

/************************************************************************************************************************/

function report_scores_singlesbox($wid, $lid, $wor, $wnr, $lor, $lnr) {

	$winnernamequery = "SELECT tblUsers.firstname, tblUsers.lastname, tblUsers.email
	                    FROM tblUsers
	                    WHERE (((tblUsers.userid)=$wid))";

	$wresult = db_query($winnernamequery);
	$wobj = mysql_fetch_object($wresult);

	$losernamequery = "SELECT tblUsers.firstname, tblUsers.lastname, tblUsers.email
	                    FROM tblUsers
	                    WHERE (((tblUsers.userid)=$lid))";

	$lresult = db_query($losernamequery);
	$lobj = mysql_fetch_object($lresult);

	/* email the user with the new account information    */
	$var = new Object;
	$var->support = $_SESSION["CFG"]["support"];
	$var->winnersold = $wor;
	$var->winnersnew = $wnr;
	$var->losersold = $lor;
	$var->losersnew = $lnr;

	//Get the winner user information
	$var->winnerfname = $wobj->firstname;
	$var->winnerlname = $wobj->lastname;
	$var->winnerfull = $wobj->firstname . " " . $wobj->lastname;

	//Get the loser user information
	$var->loserfname = $lobj->firstname;
	$var->loserlname = $lobj->lastname;
	$var->loserfull = $lobj->firstname . " " . $lobj->lastname;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";
	$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/report_scores_singlesbox.php", $var);

	//Email the winner
	$message = "Hello $wobj->firstname,\n";
	$message .= "$emailbody";

	mail("$wobj->firstname $wobj->lastname <$wobj->email>", "$clubfullname -- Score Report", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

	//Email the loser
	$message = "Hello $emailidrow[2],\n";
	$message .= "$emailbody";

	mail("$lobj->firstname $lobj->lastname <$lobj->email>", "$clubfullname -- Score Report", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");


}

/**
 * Sends out emails to the players involved.
 * 
 * This is used for the admin records scores when there isn't a reservation id involved
 * 
 * 
*/


function report_scores_doubles_simple($wTeamid, $lTeamid, $wor, $wnr, $lor, $lnr, $score, $matchtype) {

	$rquery = "SELECT users.firstname, users.lastname, users.email
				FROM tblUsers users, tblkpTeams teamdetails
				WHERE users.userid = teamdetails.userid
				AND users.enddate IS NULL
				AND (teamdetails.teamid = $wTeamid
				OR teamdetails.teamid = $lTeamid)";
				
	$rresult = db_query($rquery);
	$robj = mysql_fetch_object($rresult);


	/* email the user with the new account information    */
	$var = new Object;
	if ($score == 0) {
		$var->howbad = "pounded";
		$var->loserscore = 0;
	}
	elseif ($score == 2) {
		$var->howbad = "edged out";
		$var->loserscore = 2;
	} else {
		$var->howbad = "defeated";
		$var->loserscore = 1;
	}

	$var->support = $_SESSION["CFG"]["support"];

	$var->winnersold = $wor;
	$var->winnersnew = $wnr;
	$var->losersold = $lor;
	$var->losersnew = $lnr;

	//Get the first player
	$var->winner = getFullNamesForTeamId($wTeamid);

	//Get the next One
	$robj = mysql_fetch_object($rresult);

	$var->loser = getFullNamesForTeamId($lTeamid);
	
	$var->loserscore = $score;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";
	$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/report_scores_doubles_simple.php", $var);

	//Reset the result pointer to the begining
	mysql_data_seek($rresult, 0);

	while ($emailidrow = db_fetch_row($rresult)) {

		//Prepare the message
		$message = "Hello $emailidrow[0],\n";
		$message .= "$emailbody";

		mail("$emailidrow[0] $emailidrow[1] <$emailidrow[2]>", "$clubfullname -- Score Report", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

	}
	
	$description = "$var->winner $var->howbad $var->loser in a $matchtype match 3-$score ";
	logSiteActivity(get_siteid(), $description);
	

}

/************************************************************************************************************************/

function report_scores_doubles($resid, $wor, $wnr, $lor, $lnr, $score) {

	$rquery = "SELECT DISTINCT courts.courtname, reservations.time, users.firstname, users.lastname, users.email, courts.courtid, reservationdetails.outcome, reservations.matchtype, matchtype.name
	            FROM tblCourts courts, tblReservations reservations, tblUsers users, tblkpUserReservations reservationdetails, tblkpTeams teamdetails, tblUserRankings rankings, tblMatchType matchtype,tblClubUser clubuser
				WHERE reservations.reservationid = reservationdetails.reservationid
	            AND teamdetails.teamid = reservationdetails.userid
	            AND users.userid = teamdetails.userid
	            AND rankings.userid = reservationdetails.userid
	            AND courts.courtid = reservations.courtid
				AND reservations.matchtype = matchtype.id
	            AND reservationdetails.reservationid=$resid
				AND users.userid = clubuser.userid
	            AND clubuser.clubid=" . get_clubid() . "
	            AND rankings.usertype=1
	            ORDER BY reservationdetails.outcome DESC";

	$rresult = db_query($rquery);
	$robj = mysql_fetch_object($rresult);

	/* email the user with the new account information    */
	$var = new Object;
	if ($score == 0) {
		$var->howbad = "pounded";
		$var->loserscore = 0;
	}
	elseif ($score == 2) {
		$var->howbad = "edged out";
		$var->loserscore = 2;
	} else {
		$var->howbad = "defeated";
		$var->loserscore = 1;
	}

	$var->courtname = $robj->courtname;
	$var->matchtype = $robj->name;
	$var->time = gmdate("l F j g:i a", $robj->time);
	$var->timestamp = $robj->time;
	$var->dns = $_SESSION["CFG"]["dns"];
	$var->wwwroot = $_SESSION["CFG"]["wwwroot"];
	$var->courtid = $robj->courtid;
	$var->support = $_SESSION["CFG"]["support"];
	$var->winnersold = $wor;
	$var->winnersnew = $wnr;
	$var->losersold = $lor;
	$var->losersnew = $lnr;
	
	$var->date = gmdate("l F j", $robj->time);
	$var->hour = gmdate("g:i a", $robj->time);

	//Get the first player of team 1
	$var->firstname1 = $robj->firstname;
	$var->lastname1 = $robj->lastname;
	$var->fullname1 = $robj->firstname . " " . $robj->lastname;

	//Get the second player of team 1
	$robj = mysql_fetch_object($rresult);
	$var->firstname2 = $robj->firstname;
	$var->lastname2 = $robj->lastname;
	$var->fullname2 = $robj->firstname . " " . $robj->lastname;

	//Get the first player of team 2
	$robj = mysql_fetch_object($rresult);
	$var->firstname3 = $robj->firstname;
	$var->lastname3 = $robj->lastname;
	$var->fullname3 = $robj->firstname . " " . $robj->lastname;

	//Get the second player of team 2
	$robj = mysql_fetch_object($rresult);
	$var->firstname4 = $robj->firstname;
	$var->lastname4 = $robj->lastname;
	$var->fullname4 = $robj->firstname . " " . $robj->lastname;

	$clubfullname = get_clubname();
	$var->clubfullname = $clubfullname;
	$var->clubadminemail = "PlayerMailer@sportsynergy.net";
	$emailbody = read_template($_SESSION["CFG"]["templatedir"]."/email/report_scores_doubles.php", $var);

	//Reset the result pointer to the begining
	mysql_data_seek($rresult, 0);

	while ($emailidrow = db_fetch_row($rresult)) {

		//Prepare the message
		$message = "Hello $emailidrow[2],\n";
		$message .= "$emailbody";

		mail("$emailidrow[2] $emailidrow[3] <$emailidrow[4]>", "$clubfullname -- Score Report", $message, "From: PlayerMailer@sportsynergy.net", "-fPlayerMailer@sportsynergy.com");

	}
	
	$description = "$var->fullname1 and $var->fullname2 $var->howbad $var->fullname3 and $var->fullname4 in a $var->matchtype match 3-$score on $var->courtname $var->date at $var->hour";
	logSiteActivity(get_siteid(), $description);

}

/***********************************************************************************************************************
 * required parameters:
 * 	reservationid
 * 	score
 */

function record_score(&$frm, $source) {

	 if(isDebugEnabled(1) ) logMessage("applicationlib.record_score: source is $source");
	 
	
	/* Record score */
	//The winner userid is passed in the post vars, the loser is passed
	//in either the player1 or player2 post vars.  Also, the outcome is
	// either a 0 which is a 3-0 match score, a 1 which is a 3-1 match
	// score or finally a 2 which is a 3-2 match score.

	// Set the winner and loser variables so we know who is who

	//If the boxid variable is set this is being called from
	//the web ladder or from a reservation was made as a league
	// reservation.  In any event we will figure out who who with
	// the fancy get_matchresults.

	// 03/27/2003    John now suggested that the box league matches count for
	//               twice that of a practice match
	// 10/01/2005    John now suggested that the challenge match count for thrice
	//               thhat of a practice match.

	$results = get_matchresults($frm['winner'], $frm['Player1'], $frm['Player2']);
	$winner = $results['winner'];
	$loser = $results['loser'];

	// Update the winners outcome
	$winnersquery = "UPDATE tblkpUserReservations SET outcome=3
	                      WHERE reservationid='$frm[reservationid]'
	                      AND userid=$winner";

	$winnerresult = db_query($winnersquery);

	// Update the losers outcome
	$loserssquery = "UPDATE tblkpUserReservations SET outcome='$frm[score]'
	                      WHERE reservationid='$frm[reservationid]'
	                      AND userid=$loser";

	$losersresult = db_query($loserssquery);

	// Get the courttypeid from tblReservations
	$ctidquery = "SELECT courts.courttypeid, reservations.reservationid, reservations.usertype, reservations.matchtype
	                      FROM tblCourts courts, tblReservations reservations
	                      WHERE courts.courtid = reservations.courtid
	                      AND reservations.reservationid='$frm[reservationid]'";

	$ctidresult = db_query($ctidquery);
	$ctidarray = db_fetch_array($ctidresult);
	$usertypeval = $ctidarray[2];


	/**
	 * Adjust the rankings
	 */

	if ($usertypeval == 1) {

		// Look up the individuals ranking for members of winning team
		// (this will be averaged to calculate
		
		$winnerResult = getUserIdsForTeamIdWithCourtType($winner, $ctidarray[0]);
		$playerRow = mysql_fetch_array($winnerResult);
        $winnerRanking = $playerRow['ranking'];
        $playerRow = mysql_fetch_array($winnerResult);
        $winnerRanking  += $playerRow['ranking'];
        $winnerRanking = $winnerRanking/2; 
         
        if(isDebugEnabled(1) ) logMessage("applicationlib.record_scores: Team Id $winner has a ranking of $winnerRanking");
	

         $loserResult = getUserIdsForTeamIdWithCourtType($loser, $ctidarray[0]);
		 $playerRow = mysql_fetch_array($loserResult);
         $loserRanking = $playerRow['ranking'];
         $playerRow = mysql_fetch_array($loserResult);
         $loserRanking  += $playerRow['ranking'];
         $loserRanking = $loserRanking/2; 
		
         if(isDebugEnabled(1) ) logMessage("applicationlib.record_scores: Team Id $loser has a ranking of $loserRanking");
	
		 // Calculate the Rankings
		 $rankingArray = calculateRankings($winnerRanking, $loserRanking);

		//If the match type is a two (challenge match) count the match two times
		if ($ctidarray[3] == 2) {
			$rankingArray = calculateRankings($rankingArray['winner'], $rankingArray['loser']);
		}

		//For winner team
		mysql_data_seek($winnerResult,0);
		mysql_data_seek($loserResult,0);
		

		$winners = array();          		          		
        $playerRow = mysql_fetch_array($winnerResult);
        array_push($winners, $playerRow['userid']);
        $playerRow = mysql_fetch_array($winnerResult);
        array_push($winners, $playerRow['userid']);
                     		
                     		
		$winnerAdjustment = $rankingArray['winner'] - $winnerRanking;
		
		$playerOneRankQuery = "SELECT rankings.ranking 
							   FROM tblUserRankings rankings 
							   WHERE rankings.userid = $winners[0] 
							   AND rankings.courttypeid = '$ctidarray[0]'
							   AND rankings.usertype = 0";
						
		$playerOneRankResult = db_query($playerOneRankQuery);
		$playerOneRanking = mysql_result($playerOneRankResult, 0);
		$playerOneNewRanking = 	$playerOneRanking + $winnerAdjustment;
			
		$playerOneAdjustment = db_query("
						           UPDATE tblUserRankings
						           SET ranking = $playerOneNewRanking
						           WHERE userid = '$winners[0]'
						           AND courttypeid = '$ctidarray[0]'");	
							
	   $playerTwoRankQuery = "SELECT rankings.ranking 
							   FROM tblUserRankings rankings 
							   WHERE rankings.userid = $winners[1] 
							   AND rankings.courttypeid = '$ctidarray[0]'
							   AND rankings.usertype = 0";
						
		$playerTwoRankResult = db_query($playerTwoRankQuery);
		$playerTwoRanking = mysql_result($playerTwoRankResult, 0);
		$playerTwoNewRanking = 	$playerTwoRanking + $winnerAdjustment;
			
		$playerOneAdjustment = db_query("
						           UPDATE tblUserRankings
						           SET ranking = $playerTwoNewRanking
						           WHERE userid = '$winners[1]'
						           AND courttypeid = '$ctidarray[0]'");	
		
		
		//For loser team
		$losers = array();          		          		
        $playerRow = mysql_fetch_array($loserResult);
        array_push($losers, $playerRow['userid']);
        $playerRow = mysql_fetch_array($loserResult);
        array_push($losers, $playerRow['userid']);
		
		$loserAdjustment = $loserRanking - $rankingArray['loser'];	  
		
		$playerThreeRankQuery = "SELECT rankings.ranking 
							   FROM tblUserRankings rankings 
							   WHERE rankings.userid = $losers[0] 
							   AND rankings.courttypeid = '$ctidarray[0]'
							   AND rankings.usertype = 0";
						
		$playerThreeRankResult = db_query($playerThreeRankQuery);
		$playerThreeRanking = mysql_result($playerThreeRankResult, 0);
		$playerThreeNewRanking = 	$playerThreeRanking - $loserAdjustment;
			
		$playerThreeAdjustment = db_query("
						           UPDATE tblUserRankings
						           SET ranking = $playerThreeNewRanking
						           WHERE userid = '$losers[0]'
						           AND courttypeid = '$ctidarray[0]'");	
							
	   $playerFourRankQuery = "SELECT rankings.ranking 
							   FROM tblUserRankings rankings 
							   WHERE rankings.userid = $losers[1] 
							   	AND rankings.courttypeid = '$ctidarray[0]'
							   AND rankings.usertype = 0";
						
		$playerFourRankResult = db_query($playerFourRankQuery);
		$playerFourRanking = mysql_result($playerFourRankResult, 0);
		$playerFourNewRanking = 	$playerFourRanking - $loserAdjustment;
			
		$playerOneAdjustment = db_query("
						           UPDATE tblUserRankings
						           SET ranking = $playerFourNewRanking
						           WHERE userid = '$losers[1]'
						           AND courttypeid = '$ctidarray[0]'");
		         

	}
	
	//Singles
	elseif ($usertypeval == 0) {

		$winneridquery = "SELECT rankings.ranking, users.firstname, users.lastname
		                  			FROM tblUsers users, tblUserRankings rankings
		                  			WHERE users.userid = rankings.userid
		                  			AND rankings.userid=$winner
		                  			AND rankings.courttypeid=$ctidarray[0]
									AND users.enddate IS NULL
		                  			AND rankings.usertype=0";

		$winneridresult = db_query($winneridquery);
		$winneridarray = db_fetch_array($winneridresult);

		$loseridquery = "SELECT rankings.ranking, users.firstname, users.lastname
		                         FROM tblUsers users, tblUserRankings rankings
		                         WHERE users.userid = rankings.userid
		                         AND rankings.userid=$loser
		                         AND rankings.courttypeid=$ctidarray[0]
								 AND users.enddate IS NULL
		                         AND rankings.usertype=0";

		$loseridresult = db_query($loseridquery);
		$loseridarray = db_fetch_array($loseridresult);

		$rankingArray = calculateRankings($winneridarray[0], $loseridarray[0]);
		
		if ($ctidarray[3] == 2) {
			$rankingArray = calculateRankings($rankingArray['winner'], $rankingArray['loser']);
		}
		
		$newWinnerRanking = $rankingArray['winner'];
		$newLoserRanking = $rankingArray['loser'];	
		
		// Update the winners ranking

		$losersrankq = db_query("
				           UPDATE tblUserRankings
				           SET ranking = $newWinnerRanking
				           WHERE userid = '$winner'
				           AND courttypeid = '$ctidarray[0]'");

		// Update the losers ranking
		$winnrsrankq = db_query("
				           UPDATE tblUserRankings
				           SET ranking = $newLoserRanking
				           WHERE userid = '$loser'
				           AND courttypeid = '$ctidarray[0]'
				           ");

	}


	/**
	 * Write out the message
	 */
	 
	if ($usertypeval == 1) {


		$wteamnamequery = "SELECT users.firstname, users.lastname
		                            FROM tblUsers users, tblkpTeams teamdetails
		                            WHERE users.userid = teamdetails.userid
		                            AND teamdetails.teamid=$winner
		                            ORDER BY users.userid";

		$wteamnameresult = db_query($wteamnamequery);
		$wteamnamearray = db_fetch_array($wteamnameresult);

		//Get the players first and last names of the losing team
		$lteamnamequery = "SELECT users.firstname, users.lastname
		                            FROM tblUsers users, tblkpTeams teamdetails
		                            WHERE users.userid = teamdetails.userid
		                            AND teamdetails.teamid=$loser
		                            ORDER BY users.userid";

		$lteamnameresult = db_query($lteamnamequery);
		$lteamnamearray = db_fetch_array($lteamnameresult);
	
		echo "<span class=bigbanner> Congratulations $wteamnamearray[0] $wteamnamearray[1] and ";

		// And now get the next partner
		$wteamnamearray = db_fetch_array($wteamnameresult);
		echo "$wteamnamearray[0] $wteamnamearray[1]!</span><br><br>";

		// Set the array pointer back to the front.
		unset ($wteamnameresult);

		$wteamnameresult = db_query($wteamnamequery);
		$wteamnamearray = db_fetch_array($wteamnameresult);

		echo "<div class=normal>$wteamnamearray[0] $wteamnamearray[1]'s ranking went up by ". round($winnerAdjustment,4)." to ".round($playerOneNewRanking,4)."</div>";
		$wteamnamearray = db_fetch_array($wteamnameresult);
		echo "<div class=normal>$wteamnamearray[0] $wteamnamearray[1]'s ranking went up by ". round($winnerAdjustment,4)."  to ".round($playerTwoNewRanking,4)."</div>";
		echo "<br>";
		echo "<div class=normal> $lteamnamearray[0] $lteamnamearray[1]'s ranking went down by ". round($winnerAdjustment,4)."  to ".round($playerThreeRanking,4)."</div>";
		$lteamnamearray = db_fetch_array($lteamnameresult);
		echo "<div class=normal> $lteamnamearray[0] $lteamnamearray[1]'s ranking went down by ". round($winnerAdjustment,4)."  to ".round($playerFourRanking,4)."</div>";
	

		//Send out the emails
		report_scores_doubles($frm['reservationid'], $winnerRanking, $rankingArray['winner'], $loserRanking, $rankingArray['loser'], $frm['score']);

	} else {  ?>

		<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
		<tr>
		<td class="normal">
		<font class="bigbanner"> Congratulations <?=$winneridarray[1]?> <?=$winneridarray[2] ?>!!</font><br><br>
		<?= $winneridarray[1]?> <?=$winneridarray[2]?>'s rating rose from <?=$winneridarray[0]?> to <?=$newWinnerRanking?>  <br>
		<?=$loseridarray[1]?>  <?=$loseridarray[2]?>'s rating fell from <?=$loseridarray[0]?> to <?=$newLoserRanking?> <br>
		<td>
		<tr>
		<tr>
		<td class="normal">
		<br/>
		
		<?
		if( isset($source) && $source == "ladder"){?>
			<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_ladder.php">Back to the ladder</a>
		<? } else { ?>
			<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>">Back to the scheduler</a>
		<? } ?>
		<td>
		<tr>
		</table>

		<?
		//Send out the emails
		report_scores_singles($frm['reservationid'], $winneridarray[0], $rankingArray['winner'], $loseridarray[0], $rankingArray['loser'], $frm['score']);

	}

}


/**
    This is done using the USSRA rating system.  This is calculated using the
	 following formula:
	 --------------------------------------------------------------------------
	 WRO= Winner's old rating
	 LRO = Loser's old rating
	 K = constant = .1
	 D = denominator = .5
	 PW = Probability that the winner would win = (1/power(10,(-(WRO-LRO)/D))+1))
	 PL = Probability that the lose would win = (1/Power(10,(-(LRO-WRO)/D))+1))
	 WRN = Winners's new raing = (WRO+K*(1-PW))
	 LRN = Loser's new rating = (LRO+K*K(0-PL)
	 --------------------------------------------------------------------------
	 This is using infomration found at www.us-squash.org **/

function calculateRankings($winnerOldRanking, $losersOldRanking) {

	$wro = $winnerOldRanking;
	$lro = $losersOldRanking;
	$k = .1;
	$d = .5;
	$pw = (1 / (pow(10, (- ($wro - $lro) / $d)) +1));
	$pl = (1 / (pow(10, (- ($lro - $wro) / $d)) +1));
	$wrn = ($wro + $k * (1 - $pw));
	$lrn = ($lro + $k * (0 - $pl));

	return array (
		"winner" => $wrn,
		"loser" => $lrn
	);

}


/**
 *  This is the function used by the box leagues to record how many 
 *  matches people have played. First we read in the previous games 
 *  played value for both the person calling this function as
	well as the person's opponent.
 */
function update_gamesplayed($playerOneId, $playerTwoId, $boxId) {

	$boxgamesplayedquery = "SELECT tblkpBoxLeagues.boxid, tblkpBoxLeagues.userid, tblkpBoxLeagues.games
	                         FROM tblkpBoxLeagues
	                         WHERE (((tblkpBoxLeagues.userid)=$playerOneId
	                         Or (tblkpBoxLeagues.userid)=$playerTwoId)
	                         AND ((tblkpBoxLeagues.boxid)=$boxId))";

	$boxgamesplayedresult = db_query($boxgamesplayedquery);
	
	while ($boxgamesplayedarray = mysql_fetch_row($boxgamesplayedresult)) {
		$onemoregame = $boxgamesplayedarray[2] + 1;
		$updategames = db_query("UPDATE tblkpBoxLeagues
	                               SET games = '$onemoregame'
	                               WHERE userid = '$boxgamesplayedarray[1]'
	                               AND boxid = '$boxgamesplayedarray[0]'
	                               ");
	}

}

/************************************************************************************************************************/

function update_streakval(& $frm) {

	// Get the courttypeid from tblReservations
	$ctidquery = "SELECT courts.courttypeid, reservations.reservationid, reservations.usertype
	                      FROM tblCourts courts, tblReservations reservations
	                      WHERE courts.courtid = reservations.courtid
	                      AND reservations.reservationid='$frm[reservationid]'";

	$ctidresult = db_query($ctidquery);
	$ctidarray = db_fetch_array($ctidresult);

	if (isset ($frm['boxid'])) {

		$results = get_matchresults($frm['winner'], $frm['Player1'], $frm['Player2']);
		$winner = $results['winner'];
		$loser = $results['loser'];

	} else {
		$winner = $frm['winner'];

		if ($frm['Player1'] == $frm['winner']) {
			$loser = $frm['Player2'];

		} else {
			$loser = $frm['Player1'];

		}
	}
	// End the streak for the loser

	$endStreakQuery = " UPDATE tblUserRankings
	                 SET hot = '0'
	                 WHERE userid = '$loser'
	                 AND courttypeid = '$ctidarray[0]'";

	$endStreakResult = db_query($endStreakQuery);

	//See if this puts the winner on a streak
	$streakquery = "SELECT outcome
	                  FROM tblkpUserReservations
	                  WHERE userid=$winner
	                  ORDER BY reservationid desc limit 0,5";

	$streakresult = db_query($streakquery);

	$numberOfWins = 0;
	while ($streakval = mysql_fetch_array($streakresult)) {

		$numberOfWins += $streakval['outcome'];
	}

	// If the streakval is greater than
	if ($numberOfWins > 10) {

		$updateQuery = "UPDATE tblUserRankings
		                        SET hot = '1'
		                        WHERE userid = '$winner'
		                        AND courttypeid = '$ctidarray[0]'";

		$qid = db_query($updateQuery);

	}

}

/***********************************************************************************************************************
 * 
 * This is called in the report scores function which may or maynot be reporting the score of
 * a box league match, if if is a bax league match it may or maynot be done through the same page
 * one being the report scores link, the other through the reservation.  We will look for the outcome
 * http_post_var to determine this one.
 * 
 * */

function get_matchresults($winner, $player1, $player2) {

	if ($player1 == $winner) {
		$loser = $player2;

	} else {
		$loser = $player1;

	}

	$results = array (
		"winner" => $winner,
		"loser" => $loser
	);
	return $results;
}

/************************************************************************************************************************/

function update_ladderscore($losersgamepoints, $boxid, $winner, $player1, $player2) {
	
	if(isDebugEnabled(1) ) logMessage("applicationlib.update_ladderscore: losersgamepoints: $losersgamepoints\nboxid: $boxid\nwinner: $winner\nplayer1: $player1\nplayer2: $player2");
	
	// Per John OBrien on 10/10/2002 the box leagues players are to be ranked in the
	// box league with a box league score.  For every match recorded through the boxleague
	// the players score is incremented one for winning plus one for playing.  As for the loser
	// their score is only increment one for playing.

	/* 11/22/2002 - alright.  one more LAST change to the way this fricking scoring works.
	                          if A beats B (3-2) A is supposed to get FIVE points: one point
	                          for showing up,one point for each game, and one point for the win
	*/

	//We are not going to use the box results for now since all scores are reported through the reservation
	$results = get_matchresults($winner, $player1, $player2);
	
	
	//Dont really understand why we have to trim these, i guess its not really that important.
	$winner = $results['winner'];
	$winner = rtrim ($winner);
	
	$loser = $results['loser'];
	$loser = rtrim ($loser);
	
	 if(isDebugEnabled(1) ) logMessage("applicationlib.update_ladderscore: setting winner: $winner which is ".strlen($winner)." loser $loser ".strlen($loser));
	
	$pointsforshowing = 1;
	$pointsforwinning = 1;
	$winnersgamepoints = 3;

	//First we need to get the score for each player from the database.
	$ladderscorequery = "SELECT tblkpBoxLeagues.boxid, tblkpBoxLeagues.userid, tblkpBoxLeagues.score
	                       FROM tblkpBoxLeagues
	                       WHERE tblkpBoxLeagues.boxid='$boxid'
	                       AND (tblkpBoxLeagues.userid=$loser OR tblkpBoxLeagues.userid=$winner)";

	$ladderscoreresult = db_query($ladderscorequery);

	while ($ladderscorearray= db_fetch_array($ladderscoreresult)) {
		//First off we are going to increment the score for each player

		$resultbox = $ladderscorearray[0];
		$resultuser = $ladderscorearray[1];
		$resultscore = $ladderscorearray[2];
		
		if(isDebugEnabled(1) ) logMessage("applicationlib.update_ladderscore: updating ladder score for $resultuser where loser is $loser and winner is $winner.");
			
		$newwinnerscore = $resultscore + $pointsforshowing + $winnersgamepoints + $pointsforwinning;
		$newloserscore = $resultscore + $losersgamepoints + $pointsforshowing;

		//Give the loser props...for trying
		if ($resultuser == $loser) {
			
			if(isDebugEnabled(1) ) logMessage("applicationlib.update_ladderscore: updating ladder score for $resultuser to $newloserscore");
			
			$qid = db_query("UPDATE tblkpBoxLeagues
			                SET score = $newloserscore
			                WHERE boxid = '$boxid'
			                AND userid = '$resultuser'");
		}

		//Now we have to give the winner props..for winning
		if ($resultuser == $winner) {

			if(isDebugEnabled(1) ) logMessage("applicationlib.update_ladderscore: updating ladder score for $resultuser to $newwinnerscore");
			
			
			$qid = db_query("UPDATE tblkpBoxLeagues
			                SET score = $newwinnerscore
			                WHERE boxid = '$boxid'
			                AND userid = '$resultuser'");
		}


	}

}


/************************************************************************************************************************/
//Returns the day number of the week

function getDOW($thisday) {

	$daynumber = 0;

	if ($thisday == "Sunday") {
		$daynumber = 0;
	}
	elseif ($thisday == "Monday") {
		$daynumber = 1;
	}
	elseif ($thisday == "Tuesday") {
		$daynumber = 2;
	}
	elseif ($thisday == "Wednesday") {
		$daynumber = 3;
	}
	elseif ($thisday == "Thursday") {
		$daynumber = 4;
	}
	elseif ($thisday == "Friday") {
		$daynumber = 5;
	}
	elseif ($thisday == "Saturday") {
		$daynumber = 6;
	}

	return $daynumber;
}

/*
 *******************************************************************************************************
 **   getTeamIDForCurrentUser
 *******************************************************************************************************
*/
function getTeamIDForCurrentUser($sportid, $partner) {

	if(isDebugEnabled(1) ) logMessage("applicationlib.getTeamIDForCurrentUser($sportid, $partner)");

	//find teams for current user
	$currentuserteamquery = "SELECT teamdetails.teamid
	                                  FROM tblTeams teams, tblkpTeams teamdetails
	                                  WHERE teams.teamid = teamdetails.teamid
	                                  AND teamdetails.userid=" . get_userid();

	// run the query on the database
	$currentuserteamresult = db_query($currentuserteamquery);

	//Build an single dimensional array for current user teams
	$currentUserStack = array ();
	while ($currentuserteamarray = mysql_fetch_array($currentuserteamresult)) {
		array_push($currentUserStack, $currentuserteamarray['teamid']);
	}

	//find teams for current users partner
	$currentuserpartnerteamquery = "SELECT teamdetails.teamid
	                                         FROM tblTeams teams, tblkpTeams teamdetails
	                                         WHERE teams.teamid = teamdetails.teamid
	                                         AND teamdetails.userid=$partner";

	// run the query on the database
	$currentuserpartnerteamresult = db_query($currentuserpartnerteamquery);

	//Build an single dimensional array for current users partners teams
	$currentUserPartnerStack = array ();
	while ($currentuserpartnerteamarray = mysql_fetch_array($currentuserpartnerteamresult)) {
		array_push($currentUserPartnerStack, $currentuserpartnerteamarray['teamid']);
	}

	$teamexistsarray = array_intersect($currentUserStack, $currentUserPartnerStack);

	//print "This is my teamarray: $teamexistsarray[0]";
	if (count($teamexistsarray) > 0) {
		//found  a team
		$teamid = current($teamexistsarray);

	} else {
		//had to make a team
		$teamidarray = makeTeamForCurrentUser($sportid, $partner);
		$teamid = $teamidarray[1];
	}

	return $teamid;

}

/*
*******************************************************************************************************
**   getTeamIDForPlayers
*******************************************************************************************************
*/
function getTeamIDForPlayers($sportid, $player1, $player2) {

	//find teams for player 1
	$player1teamquery = "SELECT teamdetails.teamid
	                              FROM tblTeams teams, tblkpTeams teamdetails
	                              WHERE teams.teamid = teamdetails.teamid
	                              AND teamdetails.userid=$player1";

	// run the query on the database
	$player1teamresult = db_query($player1teamquery);

	//Build an single dimensional array for player ones teams
	$playerOnesTeamsStack = array ();
	while ($player1teamarray = mysql_fetch_array($player1teamresult)) {
		array_push($playerOnesTeamsStack, $player1teamarray['teamid']);

	}

	//find teams for player 2
	$player2teamquery = "SELECT teamdetails.teamid
	                              FROM tblTeams teams, tblkpTeams teamdetails
	                              WHERE teams.teamid = teamdetails.teamid
	                              AND teamdetails.userid=$player2";

	// run the query on the database
	$player2teamresult = db_query($player2teamquery);

	//Build an single dimensional array for player ones teams
	$playerTwosTeamsStack = array ();
	while ($player2teamarray = mysql_fetch_array($player2teamresult)) {
		array_push($playerTwosTeamsStack, $player2teamarray['teamid']);

	}

	$teamexistsarray = array_intersect($playerOnesTeamsStack, $playerTwosTeamsStack);

	$numofrows = count($teamexistsarray);

	//Where this is one player1 and player2 are different people
	if (count($teamexistsarray) == 1) {

		//found  a team
		$teamid = current($teamexistsarray);

	}
	/* This will happen when a team is needed for the same person
	 * only really application for club guest and club member teams
	 */
	elseif (count($teamexistsarray) > 1) {

		$teamid = findSelfTeam($teamexistsarray);

		//Set up the double member teams (where allowed)
		//this will only need to be done once per double
		//member team.
		if(!isset($teamid)){
			$teamidarray = makeTeamForPlayers($sportid, $player1, $player2);
			$teamid = $teamidarray[1];
		}
	
	} else {

		//had to make a team
		$teamidarray = makeTeamForPlayers($sportid, $player1, $player2);
		$teamid = $teamidarray[1];

	}

	return $teamid;

}
/************************************************************************************************************************/

function isCurrentUserOnTeam($teamid) {

	$imOnTheTeam = 0;

	$query = "SELECT tblkpTeams.userid
	                 FROM tblkpTeams
	                 WHERE tblkpTeams.teamid=$teamid";

	// run the query on the database
	$result = db_query($query);
	
	if( mysql_num_rows($result)< 2){
		return 0;
	}
	$playerone = mysql_result($result, 0);
	$playertwo = mysql_result($result, 1);

	if (get_userid() == $playerone || get_userid() == $playertwo) {
		$imOnTheTeam = 1;
		if( isDebugEnabled(1) ) logMessage("applicationlib.isCurrentUserOnTeam: Current User is on team");	
	}

	
	
	return $imOnTheTeam;
}
/************************************************************************************************************************/
/* Load in the clubs availble sports */
function load_avail_sports() {

	$sportquery = "SELECT DISTINCT courts.courttypeid, courttype.courttypename, courttype.reservationtype
	               FROM tblCourts courts, tblCourtType courttype
				   WHERE courts.courttypeid = courttype.courttypeid
	               AND courts.siteid=" . get_siteid();

	$sportresult = db_query($sportquery);

	return $sportresult;

}

/************************************************************************************************************************/
/* Load in the clubs availble timezones */
function load_avail_timezones() {

	$tzquery = "SELECT tz.name, tz.offset
	               FROM tblTimezones tz";

	$tzresult = db_query($tzquery);

	return $tzresult;

}

/************************************************************************************************************************/

/* This will retrieve the availabe sites*/

function load_avail_sites() {

	$getAllClubSitesQuery = "SELECT * from  tblClubSites WHERE clubid = " . get_clubid() . "";
	$getAllClubSitesResult = db_query($getAllClubSitesQuery);

	return $getAllClubSitesResult;

}

/************************************************************************************************************************/

/* This will retrieve the site parameters */
function load_parameter_options($parameterid){
	
	if( isDebugEnabled(1) ) logMessage("applicationlib.load_parameter_options for parameter: ". $parameterId );
	
	$query = "SELECT parameteroption.optionname, parameteroption.optionvalue
				FROM tblParameterOptions parameteroption
				WHERE parameteroption.parameterid = $parameterid";
	
	$result = db_query($query);
	
	return $result;
	
}


/************************************************************************************************************************/

/* This will retrieve the site parameters */
function load_site_parameters() {
	
	if( isDebugEnabled(1) ) logMessage("applicationlib.load_site_parameters for site: ". get_siteid());
    
	
	$getAllClubSitesQuery = "SELECT parameter.parameterid, parameter.parameterlabel, parametertype.parametertypename, parameteraccesstype.parameteraccesstypename
								FROM tblParameter parameter, tblParameterType parametertype, tblParameterAccess parameteraccess, tblParameterAccessType parameteraccesstype
								WHERE parameter.parametertypeid = parametertype.parametertypeid
								AND parameteraccess.parameterid = parameter.parameterid
								AND parameteraccess.roleid = ".get_roleid()."
								AND parameteraccess.parameteraccesstypeid = parameteraccesstype.parameteraccesstypeid
								AND parameter.siteid = " . get_siteid() . "
								ORDER BY parameter.parameterid";
	
	$getAllClubSitesResult = db_query($getAllClubSitesQuery);

	return $getAllClubSitesResult;
	
}

/************************************************************************************************************************/

/* This will retrieve the site parameter */
function load_site_parameter($parameterid, $userid) {
	 
	if( isDebugEnabled(1) ) logMessage("applicationlib.load_site_parameter: $parameterid for user $userid" );
    
	
	$query = "SELECT parametervalue.parametervalue
								FROM tblParameterValue parametervalue
								WHERE parametervalue.userid = $userid
								AND parametervalue.parameterid = $parameterid
								AND parametervalue.enddate IS NULL";
	
	$result = db_query($query);

	if( mysql_num_rows($result) > 0 ){
		return mysql_result($result, 0);
	}else{
		return "";
	}
	
	
}

/************************************************************************************************************************/

/* This will retrieve the site parameter option name*/
function load_parameter_option_name($parameterid, $optionvalue) {
	 
	if( isDebugEnabled(1) ) logMessage("applicationlib.load_parameter_option_name: $parameterid for user $optionvalue" );
    
	
	$query = "SELECT parameteroption.optionname
								FROM tblParameterOptions parameteroption
								WHERE parameteroption.parameterid = '$parameterid'
								AND parameteroption.optionvalue = '$optionvalue'";
	
	$result = db_query($query);

	if( mysql_num_rows($result) > 0 ){
		return mysql_result($result, 0);
	}else{
		return "";
	}
	
}


/************************************************************************************************************************/
/* This will retrieve the users profile*/

function load_user_profile($userid) {


	$qid = db_query("SELECT users.userid, 
							users.username, 
							users.firstname, 
							users.lastname, 
							users.email, 
							users.homephone, 
							users.workphone, 
							users.cellphone, 
							users.pager, 
							clubuser.recemail,
							clubuser.roleid,  
							users.useraddress, 
							clubuser.enable, 
							clubuser.msince, 
							clubuser.memberid, 
							users.gender,
							clubuser.lastlogin
						FROM tblUsers users, tblClubUser clubuser
						WHERE users.userid = $userid
							AND clubuser.userid = users.userid");

	return db_fetch_array($qid);

}
/************************************************************************************************************************/

/* This will retrieve the registered sports*/

function load_registered_sports($userid) {

	$registeredSportsQuery = "SELECT rankings.courttypeid, rankings.ranking,courttype.courttypename,courttype.reservationtype
	                         FROM tblUserRankings rankings, tblCourtType courttype
							 WHERE rankings.courttypeid = courttype.courttypeid
	                         AND rankings.userid=$userid
	                         AND rankings.usertype=0";

	$registeredSportsResult = db_query($registeredSportsQuery);

	if( isDebugEnabled(1) ) logMessage("applicationlib: found ". mysql_num_rows($registeredSportsResult). " registered sports for user: ". $userid);
        
	return $registeredSportsResult;

}

/************************************************************************************************************************/

function load_auth_sites($userid) {

	$authSitesQuery = "SELECT siteauth.siteid,clubsites.sitename
	                  FROM tblkupSiteAuth siteauth, tblClubSites clubsites
	                  WHERE clubsites.siteid = siteauth.siteid
	                  AND  siteauth.userid = $userid
					  AND clubsites.clubid = ". get_clubid();
					  
	$authSitesResult = db_query($authSitesQuery);

	return $authSitesResult;

}

/************************************************************************************************************************/

/*
   This is used to get the courttype for singles courttypes.
*/

function get_singlesCourtTypesForSite($currentSiteId) {

	$courttypeQuery = "SELECT DISTINCT courttype.courttypeid, courttype.courttypename
	                   FROM tblCourtType courttype, tblCourts courts
	                   WHERE courts.siteid = $currentSiteId
					   AND courts.courttypeid = courttype.courttypeid
					   AND (courttype.reservationtype = 0 
							OR courttype.reservationtype = 1)";

	return  db_query($courttypeQuery);


}

/************************************************************************************************************************/

/*
  This is used to get the courttype for doubles courttypes.
*/

function get_doublesCourtTypesForSite($currentSiteId) {

	$courttypeQuery = "SELECT courttype.courttypeid, courttype.courttypename
	                   FROM tblCourtType courttype, tblCourts courts
	                   WHERE courts.siteid = $currentSiteId
					   AND courts.courttypeid = courttype.courttypeid
					   AND (courttype.reservationtype = 2 
							OR courttype.reservationtype = 1)
						ORDER BY courttype.courttypeid";

	return db_query($courttypeQuery);
	

}



/************************************************************************************************************************/

/*
  Gets the available court types for the site
*/

function get_courtTypeForCourt($court) {

	$courttypeQuery = "SELECT tblCourts.courttypeid
	                   FROM tblCourts
	                   WHERE (((tblCourts.courtid)=$court))";

	$courttypeResult = db_query($courttypeQuery);
	return mysql_result($courttypeResult, 0);

}

/*
***********************************************************************************************************************
*/
/* returns the court type for the given reservationid */

function get_courtTypeForReservationId($resid) {

	$matchtypequery = "SELECT courts.courttypeid 
						FROM tblReservations reservation, tblCourts courts 
						WHERE reservation.courtid = courts.courtid
						AND reservation.reservationid=$resid";
				
	$matchtyperesult = db_query($matchtypequery);
	$matchtypevalue = mysql_result($matchtyperesult, 0);

	return $matchtypevalue;

}


/************************************************************************************************************************/
/*
This is by all player selection dropdowns.  It makes sure the player is authorized for the site
and also has a ranking for the sport.
*/

function get_all_players_dropdown($currentCourtSiteID) {

	$query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
	                FROM tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth, tblClubUser clubuser
					WHERE users.userid = rankings.userid
	                AND users.userid = siteauth.userid
	                AND clubuser.roleid!= 4
					AND users.userid = clubuser.userid
	                AND clubuser.clubid=" . get_clubid() . "
	                AND users.lastname != 'Guest'
	                AND users.firstname != 'Club'
	                AND siteauth.siteid=$currentCourtSiteID
	                AND users.userid != " . get_userid() . "
	                AND clubuser.enable='y'
					AND clubuser.enddate IS NULL
	                ORDER BY users.lastname";

	return db_query($query);

}

/************************************************************************************************************************/
/*
This is by all player selection dropdowns.  It makes sure the player is authorized for the site
and also has a ranking for the sport.
*/

function get_player_dropdown($currentCourtSiteID, $courtid) {

	$query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
	                FROM tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth, tblClubUser clubuser
					WHERE users.userid = rankings.userid
	                AND users.userid = siteauth.userid
	                AND clubuser.roleid!= 4
					AND users.userid = clubuser.userid
	                AND clubuser.clubid=" . get_clubid() . "
	                AND users.lastname != 'Guest'
	                AND users.firstname != 'Club'
	                AND siteauth.siteid=$currentCourtSiteID
	                AND users.userid != " . get_userid() . "
	                AND rankings.courttypeid=" . get_courtTypeForCourt($courtid) . "
	                AND rankings.usertype=0
	                AND clubuser.enable='y'
					AND clubuser.enddate IS NULL
	                ORDER BY users.lastname";

	return db_query($query);

}

/************************************************************************************************************************/
/*
This is by all player selection dropdowns.  It makes sure the player is authorized for the site
and also has a ranking for the sport.
*/

function get_player_dropdown_withme($currentCourtSiteID, $courtid) {

	$query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
	                FROM tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth, tblClubUser clubuser
					WHERE users.userid = rankings.userid
	                AND users.userid = siteauth.userid
	                AND clubuser.roleid!= 4
					AND users.userid = clubuser.userid
	                AND clubuser.clubid=" . get_clubid() . "
	                AND siteauth.siteid=$currentCourtSiteID
	                AND rankings.courttypeid=" . get_courtTypeForCourt($courtid) . "
	                AND rankings.usertype=0
	                AND clubuser.enable='y'
					AND clubuser.enddate IS NULL
	                ORDER BY users.lastname";

	return db_query($query);

}

/************************************************************************************************************************/
/*
This is by all player selection dropdowns.  It makes sure the player is authorized for the site
and also has a ranking for the sport.  **Important: THis does not exlude club guest from being displayed
and really is only used to generate the list of members for modifing a doubles reservation.
*/

function get_player_dropdown_with_current($currentCourtSiteID, $courtid) {

	$query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
					FROM tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth,tblClubUser clubuser
	                WHERE users.userid = rankings.userid
	                AND users.userid = siteauth.userid
	                AND clubuser.roleid!= 4
					AND users.userid = clubuser.userid
	                AND clubuser.clubid=" . get_clubid() . "
	                AND siteauth.siteid=$currentCourtSiteID
	                AND rankings.courttypeid=" . get_courtTypeForCourt($courtid) . "
	                AND rankings.usertype=0
					AND clubuser.enddate IS NULL
	                ORDER BY users.lastname";

	return db_query($query);

}

/************************************************************************************************************************/
/*
This will the the hisotry of the games played between the current user and the userid passed in.  This will figure out the
sports the two users have in common as well as the win loss hisotryAn array
will be returned that will look like this:

[0] wins of userid 1 against user 2 for specified sport
[1] wins of userid 2 against user 1 for specified sport
[2] winning percentage of user 1 vs user 2 for specified sport

*/

function get_record_history($userid1, $userid2, $courttypeid) {

	
	if( isDebugEnabled(1) ) logMessage("applicationlib.get_record_history: getting the record history for userid: ".$userid1." and userid: ". $userid2." for court type: ". $courttypeid);
       
	// 1. get all resevationsids for user 1
	// 2. get all reservationids for user 2
	// for like reservations record user1 wins and user two wins
	$userOneWins = 0;
	$userTwoWins = 0;

	//Get the first user

	$userOneReservationsQuery = "SELECT reservations.reservationid
	                                 FROM tblkpUserReservations reservationdetails, tblReservations reservations, tblCourts courts
	                                 WHERE reservationdetails.reservationid = reservations.reservationid
	                                 AND reservations.courtid = courts.courtid
	                                 AND courts.courttypeid=$courttypeid
									 AND reservationdetails.userid=$userid1";

	$userOneReservationsResult = db_query($userOneReservationsQuery);
	$userOneArray = return_array($userOneReservationsResult);

	//Now get the second user

	$userTwoReservationsQuery = "SELECT reservations.reservationid
	                                 FROM tblkpUserReservations reservationdetails, tblReservations reservations, tblCourts courts
	                                 WHERE reservationdetails.reservationid = reservations.reservationid
	                                 AND reservations.courtid = courts.courtid
	                                 AND courts.courttypeid=$courttypeid
									 AND reservationdetails.userid=$userid2";

	$userTwoReservationsResult = db_query($userTwoReservationsQuery);
	$userTwoArray = return_array($userTwoReservationsResult);

	$usersMatches = array_intersect($userOneArray, $userTwoArray);

	for ($i = 0; $i < count($usersMatches); $i++) {

		$reservationid = current($usersMatches);

		$matchResultsQuery = "SELECT tblkpUserReservations.outcome, tblkpUserReservations.userid
		                                    FROM tblkpUserReservations
		                                    WHERE (((tblkpUserReservations.reservationid)=$reservationid))
		                                    ORDER BY tblkpUserReservations.outcome DESC  ";

		$matchResultsResult = db_query($matchResultsQuery);
		$firstRecordArray = mysql_fetch_array($matchResultsResult);

		//If outcome is not zero we know that the match was scored
		if ($firstRecordArray['outcome'] != 0) {
			if ($firstRecordArray['userid'] == $userid1) {
				++ $userOneWins;
			} else
				if ($firstRecordArray['userid'] == $userid2) {
					++ $userTwoWins;
				}
		}

		next($usersMatches);

	}

	//Don't divide by zero
	if ($userOneWins != 0) {
		$userOnePercentage = $userOneWins / ($userOneWins + $userTwoWins);
		$userOnePercentageFormated = sprintf("%01.2f", $userOnePercentage * 100);
	} else {
		$userOnePercentageFormated = "0.00";
	}

	
	if( isDebugEnabled(1) ) logMessage("applicationlib.get_record_history: Got the record userOneWins:".$userOneWins." userTwoWins:".$userTwoWins." and percentage: ". $userOnePercentageFormated);
   
	return array (
		$userOneWins,
		$userTwoWins,
		$userOnePercentageFormated
	);

}

/************************************************************************************************************************/
/*
     This function takes in a single dimension sql result and returns an array of its values
*/
function return_array($sqlResult) {

	$stack = array ();
	while ($record = mysql_fetch_array($sqlResult)) {
		array_push($stack, $record[0]);
	}

	return $stack;
}
/************************************************************************************************************************/
/*
     This function returns all of the months
*/

function get_months() {

	$monthArray = array (
		"January",
		"February",
		"March",
		"April",
		"May",
		"June",
		"July",
		"August",
		"September",
		"October",
		"November",
		"December"
	);

	return $monthArray;

}

/************************************************************************************************************************/
/*
     This function returns true if nobody has that email address.  
     
     @retun the clubuserid of the offending email address
*/

function verifyEmailUniqueOutsideClub($email, $userid, $clubid ) {

	if( isDebugEnabled(1) ) logMessage("applicationlib.verifyEmailUniqueOutsideClub: $email, $userid and $clubid");
	
	$qid = db_query("SELECT users.userid, clubuser.id 
					FROM tblUsers users, tblClubUser clubuser 
					WHERE users.email = '$email' 
					AND users.enddate is NULL 
					AND users.userid = clubuser.userid
					and clubuser.clubid != $clubid
					AND clubuser.enddate IS NULL");

	while ($row = mysql_fetch_array($qid)) {
		if ($row['userid'] != $userid) {
			return $row['id'];
		}
	}

	return;

}

/************************************************************************************************************************/
/*
     This function returns true if nobody has that email address.  
     
     @retun the clubuserid of the offending email address
*/

function verifyEmailUniqueAtClub($email, $userid, $clubid ) {

	
	if( isDebugEnabled(1) ) logMessage("applicationlib.verifyEmailUniqueAtClub: $email, $userid and $clubid");
	
	$qid = db_query("SELECT users.userid, clubuser.id 
					FROM tblUsers users, tblClubUser clubuser 
					WHERE users.email = '$email' 
					AND users.enddate is NULL 
					AND users.userid = clubuser.userid
					AND clubuser.clubid = $clubid
					AND clubuser.enddate IS NULL");

	while ($row = mysql_fetch_array($qid)) {
		if ($row['userid'] != $userid) {
			return $row['id'];
		}
	}

	return;

}

/**
 * 
 * @param $email
 * @param $clubid
 */
function isEmailUniqueAtClub($email, $clubid){
	
	if( isDebugEnabled(1) ) logMessage("applicationlib.isEmailUniqueAtClub: $email and $clubid");
	
	$qid = db_query("SELECT users.userid, clubuser.id 
					FROM tblUsers users, tblClubUser clubuser 
					WHERE users.email = '$email' 
					AND users.enddate is NULL 
					AND users.userid = clubuser.userid
					AND clubuser.clubid = $clubid
					AND clubuser.enddate IS NULL");

	if( mysql_num_rows($qid)>0){
		return false;
	}else{
		return true;
	}


	
}

/************************************************************************************************************************/
/*
     Just do some really basic validity checks on an email address
*/

function is_email_valid($emailaddress) {

	if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $emailaddress)) {
		return TRUE;
	}

	return FALSE;

}

/************************************************************************************************************************/
/*
     This function returns a list of players for admin searches
*/

function get_admin_player_search($searchname) {

	$playerquery = "SELECT users.firstname, users.lastname, users.email, users.workphone, users.homephone, users.userid, clubuser.msince, users.cellphone
	                        FROM tblUsers users, tblClubUser clubuser
	                        WHERE users.userid = clubuser.userid
							AND clubuser.clubid =" . get_clubid() . "
	                        AND (users.firstname Like '$searchname%'
	                        	OR users.lastname Like '$searchname%')
	                        AND clubuser.enddate is NULL
							ORDER BY users.lastname";

	return db_query($playerquery);

}

/************************************************************************************************************************/
/*
     This function returns a list of players for player searches
*/

function get_player_search($searchname) {

	$playerquery = "SELECT user.userid, user.firstname, user.lastname, user.email, user.workphone, user.homephone, user.cellphone
	                        FROM tblUsers user, tblClubUser clubuser
	                        WHERE user.userid = clubuser.userid
							AND clubuser.clubid=" . get_clubid() . "
	                        AND (user.firstname Like '$searchname%'
	                        OR user.lastname Like '$searchname%')
	                        AND clubuser.enable='y'
	                        AND clubuser.roleid != 4
							AND clubuser.enddate IS NULL
							ORDER BY user.lastname";

	return db_query($playerquery);

}

/************************************************************************************************************************/
/*
     This function decides is the box is expired
*/

function isBoxExpired($time, $boxnum) {

	$expired = FALSE;

	if ($boxnum > 0) {

		//get the box endtimestamp
		$expiredQuery = "SELECT enddatestamp FROM tblBoxLeagues where boxid = $boxnum";
		$expiredResult = db_query($expiredQuery);
		$boxtime = mysql_result($expiredResult, 0);
		$boxplus = $boxtime +86400;

		//give them the whole day
		if ($time > $boxtime +86400) {
			$expired = TRUE;
		}
	}

	return $expired;
}

/************************************************************************************************************************/
/*
     Since it was finally decided to limit the the number of boxes someone can play in at one time to.....well...one, we now
     have at our disposal the  getBoxIdForUser function which will return the boxid for the userid passed in.  If a user
     is not is a box at all -1 will be retuned.
*/

function getBoxIdForUser($userid) {

	$boxUserQuery = "SELECT boxid from tblkpBoxLeagues where userid = $userid";
	$boxUserResult = db_query($boxUserQuery);

	if (mysql_num_rows($boxUserResult) > 0) {
		$boxid = mysql_result($boxUserResult, 0);
	} else {
		$boxid = -1;
	}

	return $boxid;
}

/************************************************************************************************************************/
/*
    This will check to see if the the two users have played each other. We are not going to do alot of validation here so
    its important to make sure that these are two VALID box leage players before calling this.
*/

function getBoxReservation($userid1, $userid2, $boxid) {

	$hasPlayedWith = FALSE;

	$p1stack = array ();
	$p2stack = array ();

	//find box matches for player one
	$findHistoryQuery1 = "SELECT reservations.reservationid
	                             FROM tblkpUserReservations reservationdetails, tblReservations reservations, tblBoxHistory boxhistory
	                             WHERE reservationdetails.reservationid = reservations.reservationid
	                             AND reservations.reservationid = boxhistory.reservationid
	                             AND boxhistory.boxid=$boxid
	                             AND reservationdetails.userid=$userid1";

	$findHistoryResult1 = db_query($findHistoryQuery1);
	while ($findHistoryArray1 = db_fetch_array($findHistoryResult1)) {
		array_push($p1stack, $findHistoryArray1[0]);
	}

	//find box matches for player two
	$findHistoryQuery2 = "SELECT reservations.reservationid
	                             FROM tblkpUserReservations reservationdetails, tblReservations reservations, tblBoxHistory boxhistory
	                             WHERE reservationdetails.reservationid = reservations.reservationid
	                             AND reservations.reservationid = boxhistory.reservationid
	                             AND boxhistory.boxid=$boxid
	                             AND reservationdetails.userid=$userid2";

	$findHistoryResult2 = db_query($findHistoryQuery2);
	while ($findHistoryArray2 = db_fetch_array($findHistoryResult2)) {
		array_push($p2stack, $findHistoryArray2[0]);
	}

	$playersintersect = array_intersect($p1stack, $p2stack);


	return $playersintersect[0];

}

/**
 * Real Simple.  Returns true if the reservation has already been scored, meaning
 * something has been entered in the outcome.
 */
function hasPlayedBoxWith($userid1, $userid2, $boxid){
	
	$reservationId = getBoxReservation($userid1, $userid2, $boxid);
	
	if( isset($reservationId) ){
		return true;
	}
	else{
		return false;
	}
}

/************************************************************************************************************************/
/*
    This will retireive the sitecode
*/

function get_sitecode() {


	$siteid = $_SESSION["siteprefs"]["siteid"];

	$sitecodequery = "SELECT sitecode from tblClubSites where siteid=$siteid";
	$sitecodeResult = db_query($sitecodequery);
	return mysql_result($sitecodeResult, 0);

}

/************************************************************************************************************************/
/*
     This function returns a list of players for player searches
*/

function get_allclubs_dropdown() {

	$playerquery = "SELECT *
	                        FROM tblClubs
	                        WHERE enable=1 ";

	return db_query($playerquery);

}

/************************************************************************************************************************/
/*
     This function returns a list of players for player searches
*/

function get_allsites_dropdown() {

	$playerquery = "SELECT tblClubSites.*, tblClubs.clubname
	                      FROM tblClubSites
	                      INNER JOIN tblClubs ON tblClubSites.clubid = tblClubs.clubid";

	return db_query($playerquery);

}

/************************************************************************************************************************/
/*
     This function returns a list of players for player searches
*/

function getMonthName($month) {
	if ($month == "1") {
		$monthName = "January";
	}
	elseif ($month == "2") {
		$monthName = "Februray";
	}
	elseif ($month == "3") {
		$monthName = "March";
	}
	elseif ($month == "4") {
		$monthName = "April";
	}
	elseif ($month == "5") {
		$monthName = "May";
	}
	elseif ($month == "6") {
		$monthName = "June";
	}
	elseif ($month == "7") {
		$monthName = "July";
	}
	elseif ($month == "8") {
		$monthName = "August";
	}
	elseif ($month == "9") {
		$monthName = "September";
	}
	elseif ($month == "10") {
		$monthName = "October";
	}
	elseif ($month == "11") {
		$monthName = "November";
	}
	elseif ($month == "12") {
		$monthName = "December";
	}

	return $monthName;
}

/************************************************************************************************************************/
/*
     This function returns a the two userid for the team specified
*/

function getUserIdsForTeamId($teamid) {

	$playersQuery = "SELECT teamdetails.userid, users.firstname, users.lastname
	                   FROM tblkpTeams teamdetails, tblUsers users
	                   WHERE teamdetails.teamid=$teamid
	                   AND teamdetails.userid = users.userid
	                   ORDER BY users.userid";

	$playersResult = db_query($playersQuery);

	
	return $playersResult;

}


/************************************************************************************************************************/
/*
     This function returns a the two userid for the team specified
*/

function getUserIdsForTeamIdWithCourtType($teamid, $courtTypeId) {

	$playersQuery = "SELECT teamdetails.userid, users.firstname, users.lastname, rankings.ranking
	                   FROM tblkpTeams teamdetails, tblUsers users, tblUserRankings rankings
	                   WHERE teamdetails.teamid=$teamid
	                   AND teamdetails.userid = users.userid
					   AND rankings.userid = users.userid
					   AND rankings.usertype = 0
					   AND rankings.courttypeid = $courtTypeId
					   ORDER BY users.userid";

	$playersResult = db_query($playersQuery);

	
	return $playersResult;

}


/************************************************************************************************************************/
/*
     This will return true if the user specified is a club guest and false if not.
*/

function isClubGuest($userid) {

	if($userid=="" || empty($userid) ){
		return false;
	}
	$isClubGuest = FALSE;

	$isClubGuestQuery = "SELECT firstname, lastname from tblUsers
	                              WHERE userid = $userid";

	$isClubGuestResult = db_query($isClubGuestQuery);

	$isClubGuestArray = mysql_fetch_array($isClubGuestResult);

	if ($isClubGuestArray['firstname'] == "Club" && $isClubGuestArray['lastname'] == "Guest") {

		$isClubGuest = TRUE;
	}

	return $isClubGuest;

}

/**
 * Used to determine if a player has been selected in a drop down.
 */
function isPlayerSpecified($userid){
	
	if($userid ==""){
		return false;
	}
	
	return true;
}

/*
 * This will return true if the passed in user has a roleid is 2 or Program administrator
 */
function isProgramAdmin($userid) {

	//This damn thing might be empty, fine, I'll check it
	if (empty ($userid))
		return false;

	$query = "SELECT clubuser.roleid 
				  FROM tblClubUser clubuser
				  WHERE clubuser.userid = $userid";
	$result = db_query($query);
	$array = mysql_fetch_array($result);

	if ($array['roleid'] == 2) {
		return true;
	} else {
		return false;
	}
}

/*
 * Figures out if this is a CLub Member
 */
function isClubMember($userid) {

	if(empty($userid)){
		return false;
	}
	
	$query = "SELECT users.firstname, users.lastname 
				  FROM tblUsers users
				  WHERE users.userid = $userid";
	$result = db_query($query);
	$array = mysql_fetch_array($result);

	if ($array['firstname'] == "Club" && $array['lastname'] == "Member") {
		return true;
	} else {
		return false;
	}

}

/*
 * Figures out if this is a CLub Member (just based on the name)
 */
function isClubMemberName($username) {

	if ($username == "Club Member") {
		return true;
	} else {
		return false;
	}

}

/*
 * Figures out if this is a CLub Gueset (just based on the name)
 */
function isClubGuestName($username) {

	if ($username == "Club Guest") {
		return true;
	} else {
		return false;
	}

}

/************************************************************************************************************************/
/*
	Validates the Skill Range Policies
	Only support singles court types for now
*/
function validateSkillPolicies($opponentid, $currentuserid, $courtid, $courttype, $time) {

	
	if( isDebugEnabled(1) ) logMessage("applicationlib.validateSkillPolicies: Validating Skill Range Policies: opponent: $opponentid, Current User: $currentuserid, courtid = $courtid, time= $time Court Type: $courttype ");
	
	
	//Make an exception for the club guest
	if (isClubGuest($opponentid)) {
		return TRUE;
	}

	$result = load_skill_policies(get_siteid());
	while ($row = mysql_fetch_array($result)) {

		$starttime = $row['starttime'];
		$endtime = $row['endtime'];

		$startTimeArray = explode(":", $starttime);
		$endTimeArray = explode(":", $endtime);
		$starthour = $startTimeArray[0];
		$endhour = $endTimeArray[0];

		//Check to see if court applies
		if ($row['courtid'] == $courtid || $row['courtid'] == NULL) {

			//Check to see if Day applies
			$dow = gmdate("w", $time);
			if ($row['dayid'] == $dow || $row['dayid'] == NULL) {

				//Check to see if this is within the window
				if (withinWindow(gmdate("H", $time), $starthour, $endhour)) {

					//Check for a Singles Reservations
					/*
					Note: Doubles reservations are not validated because it would require too much work to
					court_reservation.php.
					
					*/
					
					if ($courttype == 'singles') {

						//Check individual ranking
						$individualQuery = "SELECT rankings.ranking
                                                 FROM tblUserRankings rankings,
                                                   tblCourtType courttype,
                                                   tblCourts courts
                                                 WHERE (
                                                      rankings.userid =$opponentid
                                                      OR rankings.userid =$currentuserid
                                                 )
                                              AND rankings.courttypeid = courttype.courttypeid
                                              AND courts.courttypeid = courttype.courttypeid
                                              AND courts.courtid = $courtid
                                              AND rankings.usertype =0";

						$individualQueryResult = db_query($individualQuery);

						//Make sure both users have a rankings
						if (mysql_num_rows($individualQueryResult) != 2) {
							if( isDebugEnabled(1) ) logMessage("applicationlib.validateSkillPolicies: One of these two players doesn't have a ranking ");
							return FALSE;
						}

						$ranking1 = mysql_result($individualQueryResult, 0);
						//mysql_data_seek($individualQueryResult,1);
						$ranking2 = mysql_result($individualQueryResult, 1);

						//Do the calculation
						if ((abs($ranking1 - $ranking2) > $row['skillrange'])) {
							if( isDebugEnabled(1) ) logMessage("applicationlib.validateSkillPolicies: ".abs($ranking1 - $ranking2) ." is greater than the skill range of ".$row['skillrange'].".  Not letting this happen.");
							return FALSE;
						}
						else{
							if( isDebugEnabled(1) ) logMessage("applicationlib.validateSkillPolicies: ".abs($ranking1 - $ranking2) ." is less than the skill range of ".$row['skillrange'].".  This is ok.");
						}
						

					} //end if singles check

				} //endif within window

			} //endif day validiation

		} //endif court validiation

	} //end main while loop

	if( isDebugEnabled(1) ) logMessage("applicationlib.validateSkillPolicies: Everything looks ok with this reservation ");
	
	return TRUE;

}

/************************************************************************************************************************/
/*
     This will return TRUE if the resevation is being made in a primetime window   I realize that this appears really complicated,
     but really its not that bad. 
     
     court id- the court id
     time - the time
     opponent - the opponent. Used for validating allow looking for a match
	* 
*/
function validateSchedulePolicies($courtid, $time, $opponent) {


	if( isDebugEnabled(1) ) logMessage("applicationlib.validateSchedulePolicies: Validating Scheduling Policies: courtid = $courtid, time= $time, partner= $opponent ");

	$result = load_reservation_policies(get_siteid());
	while ($row = mysql_fetch_array($result)) {

		$starttime = $row['starttime'];
		$endtime = $row['endtime'];

		$startTimeArray = explode(":", $starttime);
		$endTimeArray = explode(":", $endtime);
		$starthour = $startTimeArray[0];
		$endhour = $endTimeArray[0];

	
		//Check to see if court applies
		if ($row['courtid'] == $courtid || $row['courtid'] == NULL) {

			//Check to see if Day applies
			$dow = gmdate("w", $time);
			if ($row['dayid'] == $dow || $row['dayid'] == NULL) {

				//Check to see if this is within the window
				if (withinWindow(gmdate("H", $time), $starthour, $endhour)) {

					//When $row['courtid'] that means we have to check all courts
					if ($row['courtid'] == NULL || $row['courtid'] == $courtid) {

						//Check to see if they exceeded window
						if ($row['schedulelimit'] <= getAllReservationsMadeToday($starthour, $endhour, $time)) {
							$message = "Policy '".$row['policyname']."' doesn't allow more than ".$row['schedulelimit']. " reservations per day";
							if( isDebugEnabled(1) ) logMessage($message);	
							return $message;
						}
						
						//Checking looking for match
						// if the partner is set and empty that means its a no specified opponent from the singles, if its not
						//set and not defined that means this is being run on a doubles reservation in which case we do not apply
						// allow looking for match policies.
						if( $row['allowlooking']=='n' && isset($opponent)){
							if( isDebugEnabled(1) ) logMessage("applicationlib.validateSchedulePolicies: this policy doesn't allow looking for a match");
							$message = "Policy called '".$row['policyname']."' doesn't allow players looking for a match";
							if( isDebugEnabled(1) ) logMessage($message);	
							return $message;
						}
						
						// Checking the back to back policies.
						if( $row['allowback2back']=='n' ){
							if( isDebugEnabled(1) ) logMessage("applicationlib.validateSchedulePolicies: this policy doesn't allow back2back, looking into this.");
							$courtDuration = getCourtDuration($courtid, $time, $dow );
							$previousReservationTime  = $time - ($courtDuration * 60 * 60);
							
							if( isInReservation($courtid, $previousReservationTime, get_userid() ) ){
								$message = "A policy called '".$row['policyname']."' doesn't allow back to back reservations.";
								if( isDebugEnabled(1) ) logMessage($message);	
								return $message;
							}
							
							$nextReservationTime = $time + ($courtDuration * 60 * 60);
							
							if( isInReservation($courtid, $nextReservationTime, get_userid() ) ){
								$message = "A policy called '".$row['policyname']."' doesn't allow back to back reservations.";
								if( isDebugEnabled(1) ) logMessage($message);	
								return $message;
							}
							
							
						}
						
						
					} else {

						
						//Check to see if they exceeded window
						if ($row['schedulelimit'] <= getCourtReservationsMadeToday($courtid, $starthour, $endhour, $time)) {
							$message = "Policy '".$row['policyname']."' doesn't allow more than ".$row['schedulelimit']. " reservations per day";
							if( isDebugEnabled(1) ) logMessage($message);	
							return $message;
						}

					}

				}

			}

		}

	}
	return '';

}


/************************************************************************************************************************/
/*
     This will return TRUE if the resevation is being made in a window
*/
function withinWindow($reservationtime, $starthour, $endhour) {

	;
	//When starttime is null, endtime should also be NULL which means that it does not specify a window,all applies.
	if ($starthour == NULL) {
		return TRUE;
	}

	if ($starthour <= $reservationtime && $endhour > $reservationtime) {

		return TRUE;
	}

	return FALSE;

}

/************************************************************************************************************************/
/*

*/

function getAllReservationsMadeToday($starthour, $endhour, $time) {

	//Then Check all day
	if ($starthour == NULL) {
		return countNumberOfAllResevationsMadeToday($time);
	} else {
		return countNumberOfAllResevationsMadeTodayInWindow($starthour, $endhour, $time);
	}

}

/************************************************************************************************************************/
/*

*/

function getCourtReservationsMadeToday($courtid, $starthour, $endhour, $time) {

	//Then Check all day
	if ($starthour == NULL) {
		return countNumberOfCourtResevationsMadeToday($courtid, $time);
	} else {
		return countNumberOfCourtResevationsMadeTodayInWindow($courtid, $starthour, $endhour, $time);
	}

}

/************************************************************************************************************************/
/*

*/
function countNumberOfAllResevationsMadeToday($time) {

	
	if( isDebugEnabled(1) ) logMessage("applicationlib.countNumberOfAllResevationsMadeToday: checking to see if reservations are made for time: $time");	
	
	//For each court in the site
	$courtQuery = "Select courts.courtid from tblCourts courts where courts.siteid=".get_siteid();
	$courtResult = db_query($courtQuery);
	$totalReservations = 0;
	
	//Get Teams
	$teams = getTeamsForCurrentUser();
	
	while($courtidArray = mysql_fetch_array($courtResult)){
	
			$courtid = $courtidArray['courtid'];
			
			//Have to get the open/close time for today
			$starttime = getOpenTimeToday($time, $courtid);
			$endtime = getCloseTimeToday($time, $courtid);
		
			$singlesQuery = "SELECT count(reservations.time)
			                        FROM tblReservations reservations, tblkpUserReservations details
			                        WHERE reservations.reservationid = details.reservationid
			                        AND details.userid = " . get_userid() . "
			                        AND reservations.enddate is NULL
			                        AND reservations.usertype = 0
								    AND reservations.courtid=$courtid
			                        AND reservations.time >= $starttime
			                        AND reservations.time < $endtime";
		
			$singlesResult = db_query($singlesQuery);
		
			$totalReservations += mysql_result($singlesResult, 0);

			//Now get the number of doubles reservations
			/*
			    1.) Get the the court type ids for the site doubles or multi reservationtype
			    2.) For each one, get the list of teams ids
			    3.) Look for all reservations with team id
			    4.) Count them.
			*/
		
		if(mysql_num_rows($teams) > 0 ){
			
			    $teamINClause = "";
				
				//Reset the teams
				mysql_data_seek($teams,0);
	
				for ($i = 0; $i < mysql_num_rows($teams); ++ $i) {
			
					$team = mysql_fetch_array($teams);
			
					if ($i != 0) {
						$teamINClause .= ",";
					}
					$teamINClause .= "$team[teamid]";
			
				}
			
				$doublesQuery = "SELECT count(reservations.time)
				                        FROM tblReservations reservations, tblkpUserReservations details, tblCourts courts
				                        WHERE reservations.reservationid = details.reservationid
				                        AND reservations.usertype = 1
										AND courts.courtid=$courtid
				                        AND reservations.time >= $starttime
				                        AND reservations.time < $endtime
				                        AND details.userid IN ($teamINClause)
				                        AND reservations.enddate IS NULL";
			
				$doublesResult = db_query($doublesQuery);
			
				$totalReservations += mysql_result($doublesResult, 0);
			
		}
			
		}
		
	return $totalReservations;

}

/************************************************************************************************************************/
/*
     This will return TRUE if the resevation is being made in a window
*/
function countNumberOfAllResevationsMadeTodayInWindow($starthour, $endhour, $time) {

	$starttime = getTimeToday($starthour, $time);
	$endtime = getTimeToday($endhour, $time);
	$totalReservations = 0;

	$singlesQuery = "SELECT count(reservations.time)
	                        FROM tblReservations reservations, tblkpUserReservations details
	                        WHERE reservations.reservationid = details.reservationid
	                        AND reservations.usertype = 0
	                        AND reservations.enddate IS NULL
	                        AND details.userid = " . get_userid() . "
	                        AND reservations.time >= $starttime
	                        AND reservations.time < $endtime";

	$singlesResult = db_query($singlesQuery);
	$totalReservations = mysql_result($singlesResult, 0);
	
	//Now get the number of doubles reservations
	/*
	    1.) Get the the court type ids for the site doubles or multi reservationtype
	    2.) For each one, get the list of teams ids
	    3.) Look for all reservations with team id
	    4.) Count them.
	*/

	$teamINClause = "";
	$teams = getTeamsForCurrentUser();
	$rows = mysql_num_rows($teams);

	for ($i = 0; $i < $rows; ++ $i) {

		$team = mysql_fetch_array($teams);

		if ($i != 0) {
			$teamINClause .= ",";
		}
		$teamINClause .= "$team[teamid]";

	}

	// If this person is on a team, set.	
	if($rows > 0 ){	
		
		$doublesQuery = "SELECT count(reservations.time)
		                        FROM tblReservations reservations, tblkpUserReservations details
		                        WHERE reservations.reservationid = details.reservationid
		                        AND reservations.usertype = 1
		                        AND reservations.enddate IS NULL
		                        AND reservations.time >= $starttime
		                        AND reservations.time < $endtime
		                        AND details.userid IN ($teamINClause)";
	
		$doublesResult = db_query($doublesQuery);
		$totalReservations +=  mysql_result($doublesResult, 0);

	}

	return $totalReservations;

}

/************************************************************************************************************************/
/*

*/
function countNumberOfCourtResevationsMadeToday($courtid, $time) {

	$starttime = getOpenTimeToday($time, $courtid);
	$endtime = getCloseTimeToday($time, $courtid);

	if( isDebugEnabled(1) ) logMessage("applicationlib.countNumberOfCourtResevationsMadeToday: checking to see if reservations are made: $courtid and $time");	
	
	$singlesQuery = "SELECT count(reservations.time)
	                      FROM tblReservations reservations, tblkpUserReservations details
	                      WHERE reservations.reservationid = details.reservationid
	                      AND reservations.usertype = 0
	                      AND reservations.enddate IS NULL
	                      AND details.userid = " . get_userid() . "
	                      AND reservations.time > $starttime
	                      AND reservations.time < $endtime";

	$singlesResult = db_query($singlesQuery);
	$totalReservations = mysql_result($singlesResult,0);

	//Now get the number of doubles reservations
	/*
	    1.) Get the the court type ids for the site doubles or multi reservationtype
	    2.) For each one, get the list of teams ids
	    3.) Look for all reservations with team id
	    4.) Count them.
	*/

	$teamINClause = "";
	$teams = getTeamsForCurrentUser();
	$rows = mysql_num_rows($teams);

	for ($i = 0; $i < $rows; ++ $i) {

		$team = mysql_fetch_array($teams);

		if ($i != 0) {
			$teamINClause .= ",";
		}
		$teamINClause .= "$team[teamid]";

	}

	if($rows > 0){
		
		$doublesQuery = "SELECT count(reservations.time)
		                        FROM tblReservations reservations, tblkpUserReservations details
		                        WHERE reservations.reservationid = details.reservationid
		                        AND reservations.usertype = 1
		                        AND reservations.enddate IS NULL
		                        AND reservations.time >= $starttime
		                        AND reservations.time < $endtime
		                        AND details.userid IN ($teamINClause)";
	
		$doublesResult = db_query($doublesQuery);
		$totalReservations += mysql_result($doublesResult, 0);
	}
	
	

	return $totalReservations;

}

/************************************************************************************************************************/
/*

*/
function countNumberOfCourtResevationsMadeTodayInWindow($starthour, $endhour, $time) {

	$starttime = getTimeToday($starthour, $time);
	$endtime = getTimeToday($endhour, $time);

	if( isDebugEnabled(1) ) logMessage("applicationlib.countNumberOfCourtResevationsMadeTodayInWindow: checking to see if reservations are made: starthour: $starthour endhour: $endhour and time: $time");	
	
	$singlesQuery = "SELECT count(reservations.time)
	                      FROM tblReservations reservations, tblkpUserReservations details
	                      WHERE reservations.reservationid = details.reservationid
	                      AND reservations.usertype = 0
	                      AND reservations.enddate IS NULL
	                      AND details.userid = " . get_userid() . "
	                      AND reservations.time > $starttime
	                      AND reservations.time < $endtime";

	$singlesResult = db_query($singlesQuery);
	$totalReservations = mysql_result($singlesResult,0);
	
	//Now get the number of doubles reservations
	/*
	    1.) Get the the court type ids for the site doubles or multi reservationtype
	    2.) For each one, get the list of teams ids
	    3.) Look for all reservations with team id
	    4.) Count them.
	*/

	$teamINClause = "";
	$teams = getTeamsForCurrentUser();
	$rows = mysql_num_rows($teams);

	for ($i = 0; $i < $rows; ++ $i) {

		$team = mysql_fetch_array($teams);

		if ($i != 0) {
			$teamINClause .= ",";
		}
		$teamINClause .= "$team[teamid]";

	}

	if($rows > 0 ){

		$doublesQuery = "SELECT count(reservations.time)
		                        FROM tblReservations reservations, tblkpUserReservations details
		                        WHERE reservations.reservationid = details.reservationid
		                        AND reservations.usertype = 1
		                        AND reservations.enddate IS NULL
		                        AND reservations.time >= $starttime
		                        AND reservations.time < $endtime
		                        AND details.userid IN ($teamINClause)";
	
		$doublesResult = db_query($doublesQuery);
		$totalReservations += mysql_result($doublesResult, 0);

	}
	
	return $totalReservations;

}

/************************************************************************************************************************/
/*

*/

function getTeamsForCurrentUser() {

	/*
	  1.) Get the the court type ids for the site doubles or multi reservationtype
	  2.) For each one, get the list of teams ids
	
	  */

	$teamsQuery = "SELECT teamdetails.teamid
	                           FROM tblkpTeams teamdetails
	                           WHERE teamdetails.userid = " . get_userid();

	return db_query($teamsQuery);;

}

/************************************************************************************************************************/
/*

*/
function getOpenTimeToday($time, $courtid) {

	$day = gmdate("j", $time);
	$month = gmdate("n", $time);
	$year = gmdate("Y", $time);
	$dow = gmdate("w", $time);

	// int hour, int minute, int second, int month, int day, int year
	$query = "SELECT hours.opentime
	                   FROM tblCourtHours hours
					   WHERE hours.courtid=$courtid
	                   AND hours.dayid=$dow";

	$result = db_query($query);
	$opentime = mysql_result($result, 0);
	$openTimeArray = explode(":", $opentime);
	$timestamp = gmmktime($openTimeArray[0], 0, 0, $month, $day, $year);

	return $timestamp;

}

/************************************************************************************************************************/
/*

*/
function getCloseTimeToday($time, $courtid) {

	$day = gmdate("j", $time);
	$month = gmdate("n", $time);
	$year = gmdate("Y", $time);
	$dow = gmdate("w", $time);

	// int hour, int minute, int second, int month, int day, int year
	$query = "SELECT hours.closetime
	                   FROM tblCourtHours hours
					   WHERE hours.courtid=$courtid
	                   AND hours.dayid=$dow";

	$result = db_query($query);
	$closetime = mysql_result($result, 0);
	$closeTimeArray = explode(":", $closetime);
	$timestamp = gmmktime($closeTimeArray[0], 0, 0, $month, $day, $year);

	return $timestamp;

}


/************************************************************************************************************************/
/*

*/
function getDurationToday($time, $courtid) {

	$day = gmdate("j", $time);
	$month = gmdate("n", $time);
	$year = gmdate("Y", $time);
	$dow = gmdate("w", $time);

	// int hour, int minute, int second, int month, int day, int year
	$query = "SELECT hours.duration
	                   FROM tblCourtHours hours
					   WHERE hours.courtid=$courtid
	                   AND hours.dayid=$dow";

	$result = db_query($query);
	$duration = mysql_result($result, 0);
	

	return $duration;

}


/************************************************************************************************************************/
/*

*/
function getTimeToday($hour, $time) {

	$day = gmdate("j", $time);
	$minute = gmdate("i", $time);
	$month = gmdate("n", $time);
	$year = gmdate("Y", $time);
	$timestamp = gmmktime($hour, $minute, 0, $month, $day, $year);

	return $timestamp;

}

/*****************************************************************/
/*
     Will load the windows
*/
function load_reservation_policies($siteid) {

	$query = "SELECT policy.policyname,
	                          policy.policyid,
	                          policy.description,
	                          policy.schedulelimit,
	                          policy.dayid,
	                          policy.courtid,
	                          policy.siteid,
	                          policy.starttime,
	                          policy.endtime,
	                          policy.allowlooking,
	                          policy.allowback2back
	                  FROM tblSchedulingPolicy policy
	                  WHERE policy.siteid = $siteid";

	return db_query($query);

}

/*****************************************************************/
/*
     
*/
function load_reservation_policy($policyid) {

	$query = "SELECT policy.policyname,
	                          policy.policyid,
	                          policy.description,
	                          policy.schedulelimit,
	                          policy.dayid,
	                          policy.courtid,
	                          policy.siteid,
	                          policy.starttime,
	                          policy.endtime,
	                          policy.allowlooking,
	                          policy.allowback2back
	                  FROM tblSchedulingPolicy policy
	                  WHERE policy.policyid = $policyid";

	$result = db_query($query);
	return mysql_fetch_array($result);

}
/*****************************************************************/
/*
     Will load the windows
*/
function load_skill_policies($siteid) {

	$query = "SELECT policy.policyname,
	                          policy.policyid,
	                          policy.description,
	                          policy.skillrange,
	                          policy.dayid,
	                          policy.courtid,
	                          policy.starttime,
	                          policy.endtime
	                  FROM tblSkillRangePolicy policy
	                  WHERE policy.siteid = $siteid";

	return db_query($query);

}

/**
 * 
 * @param $eventid
 */
function load_court_event($eventid){
	
	$query = "SELECT events.eventname,
	                          events.playerlimit,events.eventid
	                   FROM tblEvents events
	                   WHERE events.eventid = $eventid";

	$result = db_query($query);
	return mysql_fetch_array($result);
}

/**
 * 
 * @param $eventid
 */
function load_court_events($siteid){
	
	$query = "SELECT events.eventid, events.eventname,
	                          events.playerlimit
	                   FROM tblEvents events
	                   WHERE events.siteid = $siteid";
	
	
	
	$result = db_query($query);
	
	if( isDebugEnabled(1) ) logMessage("applicationlib.load_court_events: loading court events for site $siteid. Found ". mysql_num_rows($result) . " in all");	
	
	return db_query($query);
	
	
}

/*****************************************************************/
/*
     Will load the windows
*/
function load_skill_range_policy($policyid) {

	$query = "SELECT policy.policyid,
	                          policy.policyname,
	                          policy.description,
	                          policy.skillrange,
	                          policy.dayid,
	                          policy.courtid,
	                          policy.siteid,
	                          policy.starttime,
	                          policy.endtime
	                   FROM tblSkillRangePolicy policy
	                   WHERE policy.policyid = $policyid";

	$result = db_query($query);
	return mysql_fetch_array($result);

}

/**
 * Returns a court duration given a court id and time
 * @return current court duration in minutes.  
 * 
 * 	This does not support hours exceptions!!
 */
function getCourtDuration($courtid, $time, $dow){
	
	$query = "SELECT duration 
				FROM tblCourtHours hours
				WHERE courtid = $courtid
				AND dayid = $dow";
	
	$result = db_query($query);
	return mysql_result($result,0);
}

/**
 * 
 * @return boolean
 * 
 * Only works for singles reservations at the moment
 * 
 */
function isInReservation($courtid, $time, $userid){
	
	$query = "SELECT 1 FROM tblReservations reservation, tblkpUserReservations reservationdetails
				WHERE reservation.reservationid = reservationdetails.reservationid
				AND reservation.courtid = $courtid
				AND reservation.time = $time
				AND reservation.usertype=0
				AND reservationdetails.userid = $userid";
	
	$result = db_query($query);
	
	if(mysql_num_rows($result) > 0 ){
		return TRUE;
	}else{
		return FALSE;
	}
	
	
}


/**
 * This should only be set in the clubpro/admin.php
 */
function isSystemAdministrationConsole(){
	
	if( isset($_SESSION["siteprefs"]["clubid"]) && $_SESSION["siteprefs"]["clubid"] == 0 ){
		return true;
	}
	else{
		return false;
	}
}

/*****************************************************************/
/*
     Retreives all site preferences
*/
function getSitePreferencesForCourt($courtid) {

	if( isDebugEnabled(1) ) logMessage("applicationlib.getSitePreferencesForCourt: getting site preferences for site for court: $courtid");	
	
	$query = "SELECT 
					sites.siteid, 
					sites.allowselfcancel, 
					sites.clubid,
					sites.daysahead,
					sites.enableautologin,
					sites.displaytime,
					sites.allowsoloreservations,
					sites.rankingadjustment,
					sites.allowselfscore,
					sites.enable,
					sites.isliteversion,
					sites.allowallsiteadvertising,
					sites.allownearrankingadvertising,
					sites.enableguestreservation,
					sites.displaysitenavigation,
					sites.rankingscheme,
					sites.displayrecentactivity,
					sites.challengerange
	        FROM tblClubSites sites, tblCourts courts
			WHERE sites.siteid = courts.siteid
      AND courts.courtid = $courtid";

	$qid = db_query($query);
	$array =  db_fetch_array($qid);
	
	$anyboxesquery = "SELECT tblBoxLeagues.boxid
                         FROM tblBoxLeagues
                          WHERE tblBoxLeagues.siteid=".$array['siteid'];

    $anyboxesresult = db_query($anyboxesquery);
    
	if(mysql_num_rows($anyboxesresult)>0){
     	$array['boxenabled'] = 'true';
     }else{
     	$array['boxenabled'] = 'false';
     }

	return $array;
}

/*****************************************************************/
/*
     Retreives all site preferences
*/
function getSitePreferences($siteid) {

	if( isDebugEnabled(1) ) logMessage("applicationlib.getSitePreferences: Getting Site Preferences for Site: $siteid");	
	
	$query = "SELECT 
					sites.siteid, 
					sites.allowselfcancel, 
					sites.clubid,
					sites.daysahead,
					sites.enableautologin,
					sites.displaytime,
					sites.allowsoloreservations,
					sites.rankingadjustment,
					sites.allowselfscore,
					sites.enable,
					sites.isliteversion,
					sites.allowallsiteadvertising,
					sites.allownearrankingadvertising,
					sites.enableguestreservation,
					sites.displaysitenavigation,
					sites.displayrecentactivity,
					sites.rankingscheme,
					sites.challengerange
	        FROM tblClubSites sites, tblBoxLeagues box
			WHERE sites.siteid = '$siteid'";

	$qid = db_query($query);

	$array = db_fetch_array($qid);
	
	$anyboxesquery = "SELECT tblBoxLeagues.boxid
                         FROM tblBoxLeagues
                          WHERE tblBoxLeagues.siteid=$siteid";

    $anyboxesresult = db_query($anyboxesquery);

     if(mysql_num_rows($anyboxesresult)>0){
     	$array['boxenabled'] = 'true';
     }else{
     	$array['boxenabled'] = 'false';
     }
	
	return $array;
}

/*
 * Retreives all site attributes.  Unlike a preference, these are derived.
 * 
 * Others can be added
 * 
 * Current site attributes are: COURT_SPORT
 */
 function getSiteAttributes($siteid){
 	
 	$attributeArray = array();
 	
 	$court_sport = "court_sport";
 	$web_ladder = "web_ladder";
 	/*
 	 * If the site contains any of the supported sports:
 	 *   + Tenns (5)
 	 *   + Badmitton (3)
 	 *   + Squash (4)
 	 *   + Racquetball (6)
 	 *  
 	 */
 	
 	$sportQuery = "SELECT DISTINCT 1 from tblCourts courts, tblCourtType courttype
					WHERE courts.courttypeid = courttype.courttypeid
					AND courts.siteid = $siteid
					AND (courttype.sportid = 5 OR courttype.sportid = 3 OR courttype.sportid = 4 OR courttype.sportid = 6)";
	
	$$sportResult = db_query($sportQuery);
	
	if(mysql_num_rows($$sportResult) > 0){
		array_push($attributeArray, $court_sport);
	}
	
	/*
	 * If the site has an assigned box league.
	 */
	 $anyboxesquery = "SELECT tblBoxLeagues.boxid
                              FROM tblBoxLeagues
                              WHERE (((tblBoxLeagues.siteid)=$siteid))";

     $anyboxesresult = db_query($anyboxesquery);

    if(mysql_num_rows($anyboxesresult)>0){
    	array_push($attributeArray, $web_ladder);
    }
	
	return $attributeArray;
 	
 }
 
 
 /*
 * Simply checks the existence of the court attribute
 * Store this as a session varaible
 */
 function isSiteBoxLeageEnabled(){
 	
	return $_SESSION["siteprefs"]["boxenabled"]=='true'?true:false;
 }

/*****************************************************************/
/*
     Gets the usertype of a reservation (1=Doubles, 0=Singles)
*/
function isDoublesReservation($reservationID){
	
	$query = "SELECT reservations.usertype 
				FROM tblReservations reservations 
				WHERE reservations.reservationid = $reservationID";
				
	$qid = db_query($query);
	$usertype = mysql_result($qid, 0);
	return $usertype==1 ? true: false;
	
		
}

/*****************************************************************
  * Adds name value pairs to an array and return how many were added.
  */
 function array_push_associative(&$arr) {
   $args = func_get_args();
   $ret = 0;
   foreach ($args as $arg) {
       if (is_array($arg)) {
           foreach ($arg as $key => $value) {
               $arr[$key] = $value;
               $ret++;
           }
       }else{
           $arr[$arg] = "";
       }
   }
   return $ret;
}

//Will return true if is club administrator and is in the reservation
function isCAButNotinReservation($courtid, $time){
  

   $isCA = FALSE;
   $isGuestMatch = FALSE;
   $isInReservation = FALSE;

   if(get_roleid()==2){
      $isCA = TRUE;
   }
    $getCourtInfoQuery = "SELECT *
                          FROM tblReservations
                          WHERE tblReservations.courtid = '$courtid'
                          AND tblReservations.time = '$time'";
    $getCourtInfoResults = db_query($getCourtInfoQuery);
    $getCourtInfoArray = mysql_fetch_array($getCourtInfoResults);

    //Check if this is a guest reservation
    if($getCourtInfoArray['guesttype']==1){
        $isGuestMatch = TRUE;
    }

    //Check singles reservation
     elseif($getCourtInfoArray['usertype']==0){
                   $userlookupQuery = "SELECT tblkpUserReservations.userid
                                       FROM tblkpUserReservations
                                       INNER JOIN tblReservations ON tblkpUserReservations.reservationid = tblReservations.reservationid
                                       WHERE (((tblReservations.time)=$time)
                                       AND ((tblReservations.courtid)=$courtid))";

                   $userlookupResult = db_query($userlookupQuery);
                            while($userlookupArray = mysql_fetch_array($userlookupResult)){
                                 if($userlookupArray['userid']==get_userid()){
                                      $isInReservation = TRUE;
                                 }
                            }
     }


   //Check doubles reservation with teams
    elseif($getCourtInfoArray['usertype']==1){

             $doublesQuery = "SELECT tblkpUserReservations.userid, tblkpUserReservations.usertype
                                  FROM tblkpUserReservations
                                  INNER JOIN tblReservations ON tblkpUserReservations.reservationid = tblReservations.reservationid
                                  WHERE (((tblReservations.time)=$time)
                                  AND ((tblReservations.courtid)=$courtid))";

            $doublesResult = db_query($doublesQuery);
            while($doublesArray = mysql_fetch_array($doublesResult)){
                   if($doublesArray['usertype']==0){
                       if($doublesArray['userid']==get_userid()){
                          $isInReservation = TRUE;
                       }
                   }//end if
                   elseif($doublesArray['usertype']==1){
                           if(isCurrentUserOnTeam($doublesArray['userid'])==1){
                                $isInReservation = TRUE;
                           } //endif
                   }//end elseif
            }//end while
    }

    if(($isCA && !$isInReservation) || ($isCA && $isGuestMatch)){
         return TRUE;
    }
    else{
         return FALSE;
    }

}

/*
  **********************************************************************************************************
  This will check to see if on either a singles reservation or a doubles reservation the user attempting
  to cancel the court in doing so where someone is looking for a match.  As a general rule we are only
  allowing members of an incomplete reservation (and desk users) to cancel the court.
*/
function isUserInPartialReservationSingles($courtid, $time){

         $isOnlyUserInReservation = FALSE;
         $isInReservation = FALSE;
         $isOnlyOnePlayer = FALSE;

          $userlookupQuery = "SELECT tblkpUserReservations.userid
                              FROM tblkpUserReservations
                              INNER JOIN tblReservations ON tblkpUserReservations.reservationid = tblReservations.reservationid
                              WHERE (((tblReservations.time)=$time)
                              AND ((tblReservations.courtid)=$courtid))";

          $userlookupResult = db_query($userlookupQuery);
                   while($userlookupArray = mysql_fetch_array($userlookupResult)){
                        if($userlookupArray['userid']==get_userid()){
                             $isInReservation = TRUE;
                        }
                        if($userlookupArray['userid']==0){
                             $isOnlyOnePlayer = TRUE;
                        }
                   }

        if($isInReservation && $isOnlyOnePlayer){
               $isOnlyUserInReservation = TRUE;
        }


        // Or if tblkpUserReservations are of different usertypes then we kn

        return  $isOnlyUserInReservation;
}


/*
  **********************************************************************************************************
  This will check to see if on either a singles reservation or a doubles reservation the user attempting
  to cancel the court in doing so where someone is looking for a match.  As a general rule we are only
  allowing members of an incomplete reservation (and desk users) to cancel the court.
*/
function isUserInPartialReservationDoubles($courtid, $time){

   $isInReservation = FALSE;
   $doesDoublesReservationNeedAPlayer = FALSE;
   $isUserInPartialReservationDoubles = FALSE;
   $doublesReservationLookingForTeam = FALSE;
   $isOnDoublesTeam = FALSE;
   $isInDoublesReservation = FALSE;

   $usertype = 0;

     $userlookupQuery = "SELECT tblkpUserReservations.userid, tblkpUserReservations.usertype
                         FROM tblkpUserReservations
                         INNER JOIN tblReservations ON tblkpUserReservations.reservationid = tblReservations.reservationid
                         WHERE (((tblReservations.time)=$time)
                         AND ((tblReservations.courtid)=$courtid))";

   $userlookupResult =   db_query($userlookupQuery);

   while($reservationUser = mysql_fetch_array($userlookupResult)){

           //First check to see if current user is the one looking for a match
           //if($reservationUser[usertype]==0 && $reservationUser[userid]==get_userid()){
                  //$isInDoublesReservation = TRUE;

           //}

           //Now check if the current user is in one of the teams
            if($reservationUser['usertype']==1 && isCurrentUserOnTeam($reservationUser['userid'])){
                  $isInDoublesReservation = TRUE;
                  $isOnDoublesTeam = TRUE;

           }

           //We want to check for doubles reservations looking for a team

           if($reservationUser['userid'] == 0){
                  $doublesReservationLookingForTeam = TRUE;
           }

          // print "This is the value of reservationUser[usertype] $reservationUser[usertype]";
           $usertype = $usertype + $reservationUser['usertype'];

   }

   //For a complete doubles reservation the usertypes should add up to 2 (1+1), for a complete singles reservation
   // the usertype should add up to 0 (0+0)

   if($usertype == 1){
        $doesDoublesReservationNeedAPlayer = TRUE;
   }

   //If Both are true then set $isInDoublesReservation to TRUE, then we have established that
   //the current user is in the reservation somehow (either by himself or on a team) and that
   //the this is a doubles reservation that
   if($doesDoublesReservationNeedAPlayer && $isInDoublesReservation){


       $isUserInPartialReservationDoubles = TRUE;
   }


   //Finally we have to check for the scenerio of someone on a team looking for anothher team.  If
   // this person attempts to cancel the court they too should only have the option of canceling
   // (not modifying)

   if($doublesReservationLookingForTeam && $isOnDoublesTeam == TRUE){

       $isUserInPartialReservationDoubles = TRUE;
   }


   return $isUserInPartialReservationDoubles;

}

/**
  This will return the full name of a user for a given userid (first name, last name)
*/
function getFullNameForUserId($userId){
	                                   
	//this may not be set
	if( !isset($userId)) return;
	
	$userResult = getFullNameResultForUserId($userId);
    $userArray = mysql_fetch_array($userResult); 
    $fullname = "";
    
    //For faster results using indexes
    if( mysql_num_rows($userResult) > 0){
    	$fullname = "$userArray[0] $userArray[1]";  
    }
    
    return $fullname;
       
}

/**
  This will return the full name of a and espcaes ', ", and a few others.  Use when putting
  output in database.
*/
function getFullNameForUserIdWithEscapes($userId){
	                                   
	$fullname = getFullNameForUserId($userId); 
    return addslashes($fullname);
       
}

function getFullNameResultForUserId($userId){
	
	$userQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                        FROM tblUsers
                        WHERE tblUsers.userid=$userId";
	                                    
	return db_query($userQuery);
}

/**
 * Returns the the names of the players in the team specified
 */
 function getFullNamesForTeamId($teamId){
 	
 	
 	$teamsQuery = "SELECT teamdetails.userid 
					FROM tblkpTeams teamdetails 
					WHERE teamdetails.teamid =  $teamId";
					
    $teamResult = db_query($teamsQuery);
    $playerOne = mysql_result($teamResult, 0);
    $playerTwo = mysql_result($teamResult, 1);
    
    return getFullNameForUserId($playerOne)." and ".getFullNameForUserId($playerTwo);
 	
 }

/**
 * Returns the events for the site.
 */
function get_site_events($siteid){
	
	if( isDebugEnabled(1) )  logMessage("applicationlib.get_site_events: Getting events for site $siteid");
	  
	$query = "SELECT eventid, eventname
                          FROM tblEvents
                          WHERE siteid = $siteid";

      return  db_query($query);
}


/*
 * Simply determins if the time  past occured before the current time
 */
function isInPast($time){
	
	$clubquery = "SELECT timezone from tblClubs WHERE clubid='".get_clubid()."'";
	$clubresult = db_query($clubquery);
	$clubobj = db_fetch_array($clubresult);

	$tzdelta = $clubobj[timezone]*3600;
	$curtime =   mktime()+$tzdelta;
	

	if($time<$curtime){
		return true;
	}
	else{
		return false;
	}
	
}

/**
 * Logs Stuff
 */
function logMessage($message){

	date_default_timezone_set('GMT');
	
	if( !isset($_SESSION["CFG"]["logFile"])){
		die("This thing isn't configured right, try specifing a log file in application.lib");
	}
	
	$fp = fopen ($_SESSION["CFG"]["logFile"], "a+");
	fwrite($fp,date("r",mktime()).": ".$message."\n");
	fclose($fp);
	
}

/**
 * Set in the application.php
 */
function isDebugEnabled($level){
	
	global $APP_DEBUG;
	
	if( isset($APP_DEBUG) && $APP_DEBUG <=$level){
		return true;
	}
	else{
		return false;
	}
}

/**
 * For sendout out debug mails
 */
function isMailDebugEnabled($level){
	
	global $MAIL_DEBUG;
	
	if( isset($MAIL_DEBUG) && $MAIL_DEBUG <=$level){
		return true;
	}
	else{
		return false;
	}
	
}

/**
 * Will display either today, a day ago, three days, four days, 
 * five days, six days, a week, more than a week ago
 */
function determineLastLoginText($theTimeTheyLastLoggedIn, $clubid){
	
	$clubquery = "SELECT timezone from tblClubs WHERE clubid=$clubid";
	$clubresult = db_query($clubquery);
	$timezoneval = mysql_result($clubresult,0);
	$tzdelta = $timezoneval*3600;
	$theTimeItIsRightNow =   mktime()+$tzdelta;
	
	$timeSinceLastLogin = $theTimeItIsRightNow - $theTimeTheyLastLoggedIn;
	
	if( $timeSinceLastLogin < 86400 ){
		$timeSinceLastLoginString =  "Within the last day";
	}
	elseif($timeSinceLastLogin < (86400 * 2) ){
		$timeSinceLastLoginString = "Two days ago";
	}
	elseif( $timeSinceLastLogin < (86400 * 3) ){
		$timeSinceLastLoginString = "Three days ago";
	}
	elseif( $timeSinceLastLogin < (86400 * 4) ){
		$timeSinceLastLoginString = "Four days ago";
	}
	elseif( $timeSinceLastLogin < (86400 * 5) ){
		$timeSinceLastLoginString = "Five days ago";
    }
	elseif( $timeSinceLastLogin < (86400 * 6 ) ){
		$timeSinceLastLoginString = "Six days ago";
	}
	else{
		$timeSinceLastLoginString = "More than a week ago";
	}
	
	return $timeSinceLastLoginString;
	
}

/**
* A simple Twitter status display script.
* Useful as a status badge for JavaScript non-compliant browsers, where the
* insertion of the status message must be performed on the server.
*
* Example: echo(getTwitterStatus(637073, "\\0"));
*
* @author Manas Tungare, manas@tungare.name
* @version 1.0
* @copyright Manas Tungare, 2007.
* @license Creative Commons Attribution ShareAlike 3.0.
*/

/**
* Retrieves Twitter status from the Twitter server, parses it, and
* linkifies any URLs present.
*
* This code is a textbook example of optimizing at the cost of maintainability
* and reliability. It is utterly susceptible to changes in the XML format
* (that would otherwise be nicely handled by an XML parser.) But XML itself is
* an insanely heavy markup format, and this code neatly teases out the
* interesting bits while ignoring the rest. It was written for performance,
* not elegance. (Though, some would argue about elegance through sheer
* simplicity, but I digress.) :-)
*
* @return string Current status message of given user.
* @param userNumber Your user number; not to be confused with your user id.
* @param linkText Configurable anchor text for linkified URLs
*   "\\0"  : if you want the entire URL to show up.
*   "\\1"  : to show only the domain name (slashdot-style).
*   "blah" : Anything else inserts that text verbatim.
*/
function getTwitterStatus($userNumber, $linkText) {
  $url = "http://twitter.com/statuses/user_timeline/" . $userNumber .
      ".xml?count=$count";
  $feed = "";

  // Fetch feed, read it all into a string.
  // TODO(manas) Cache me if you can.
  $file = fopen($url, "r");
  if (!is_resource($file)) {
    return ("Unable to connect to Twitter!");
  }

  while (!feof($file)) {
    $feed .= fgets($file, 4096);
  }
  fclose ($file);

  // Parse, obtain created_at time, format it nicely.
  $created_at = array();
  preg_match("/<created_at>(.*?)<\/created_at>/", $feed, $created_at);
  $relative_time = niceTime(strtotime(str_replace("+0000", "",
      $created_at[1])));

  // Parse it to extract the <text> element.
  $text = array();
  preg_match("/<text>(.*?)<\/text>/", $feed, $text);
  $status = preg_replace("/http:\/\/(.*?)\/[^ ]*/",
      '<a href="\\0">'.$linkText.'</a>', $text[1]);

  // Linkify URLs
  //return $status . " &mdash; " . $relative_time;
  return $status ;
}

/**
* Formats a timestamp nicely with an adaptive "x units of time ago" message.
* Based on the original Twitter JavaScript badge. Only handles past dates.
* @return string Nicely-formatted message for the timestamp.
* @param $time Output of strtotime() on your choice of timestamp.
*/
function niceTime($time) {
  $delta = time() - $time;
  if ($delta < 60) {
    return 'less than a minute ago.';
  } else if ($delta < 120) {
    return 'about a minute ago.';
  } else if ($delta < (45 * 60)) {
    return floor($delta / 60) . ' minutes ago.';
  } else if ($delta < (90 * 60)) {
    return 'about an hour ago.';
  } else if ($delta < (24 * 60 * 60)) {
    return 'about ' . floor($delta / 3600) . ' hours ago.';
  } else if ($delta < (48 * 60 * 60)) {
    return '1 day ago.';
  } else {
    return floor($delta / 86400) . ' days ago.';
  }
}

/**
 * Goes out to the database and gets the footer message
 */
function getFooterMessage(){
	
	$footerMessageQuery = "SELECT text from tblFooterMessage WHERE enddate is NULL";
	$footMessageResult = db_query($footerMessageQuery);
	if( mysql_num_rows($footMessageResult) > 0){
		return mysql_result($footMessageResult,0);
	}
	else{
		return;
	}
	
	
}

/**
 * 
 * @param $siteId
 */
function getRecentSiteActivity($siteid){
	
	$query = "SELECT activity.description from tblSiteActivity activity 
				WHERE siteid = $siteid AND enddate is NULL
				ORDER BY activity.activitydate DESC LIMIT 3";
	
	return db_query($query);
	
}

/**
 * Gets the Club news
 * @param $siteid
 */
function getClubNews($siteid){
	
		$query = "SELECT message FROM tblMessages messages 
				WHERE siteid = $siteid AND enable = 1 AND messagetypeid = 2
				ORDER BY messages.lastmodified DESC LIMIT 3";
	
	return db_query($query);
	
}

function getClubEvents($clubid){
	
	$query = "SELECT events.id, events.name, events.eventdate, events.description
			   FROM tblClubEvents events 
				WHERE clubid = $clubid 
				AND enddate is NULL
				ORDER BY events.eventdate LIMIT 6";
	
	return db_query($query);
	
}



/**
 * Gets the recent challenges matches
 * @param $siteid
 */
function getMatchesByType($siteid, $matchtype, $limit){
	
	if( isDebugEnabled(1) ) logMessage("applicationlib.getMatchesByType: Getting challenge matches $siteid");
	
	
	$curresidquery = "SELECT reservations.reservationid, reservations.time, courts.courtname
		                  FROM tblReservations reservations,tblCourts courts
						  WHERE reservations.matchtype = $matchtype
		                  AND reservations.usertype=0
						  AND reservations.enddate IS NULL
						  AND courts.courtid = reservations.courtid
						  AND courts.siteid = $siteid
						  ORDER BY reservations.time DESC LIMIT $limit";
	//print $curresidquery;
	
	return db_query($curresidquery);
}

/**
 * 
 * @param unknown_type $clubeventid
 */
function getClubEventParticipants($clubeventid){
	
	
	$query = "SELECT users.userid, users.firstname, users.lastname
			   FROM tblClubEventParticipants participant, tblUsers users
				WHERE users.userid = participant.userid
				AND participant.clubeventid = $clubeventid
				AND participant.enddate is NULL";
	
	return db_query($query);
}

/**
 * 
 * @param $siteid
 */
function logSiteActivity($siteid, $description){
	

	$description = addslashes($description);
	
			 $query = "INSERT INTO tblSiteActivity (
	                activitydate, siteid, description
	                ) VALUES (
	                now(),
	                '$siteid',
	                '$description'
	                )";
			 
			 db_query($query);
}

/**
 * 
 * @param $dateString
 * The string is in this format yyyy-dd-mm
 */
function formatDateString($dateString){
	
	date_default_timezone_set('GMT');
	$dates = explode("-",$dateString);
	$day = $dates[2];
	$month = $dates[1];
	$year = $dates[0];
	
	$time = mktime(0,0,0,$month,$day,$year);
	
	return date("l F j", $time);
	
}


/**
 * 
 * @param $dateString
 */
function convertToDateSlashes($dateString){
	
	if( empty($dateString)){
		return;
	}

	$pos = strpos($dateString, '-');
		
	if ($pos !== false) {
		$dates = explode("-",$dateString);
		$month = $dates[2];
		$day = $dates[1];
		$year = $dates[0];
		return "$month/$day/$year";
	}

	return $dateString;

}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
	$url = 'http://www.gravatar.com/avatar/';
	$url .= md5( strtolower( trim( $email ) ) );
	$url .= "?s=$s&d=$d&r=$r";
	if ( $img ) {
		$url = '<img src="' . $url . '"';
		foreach ( $atts as $key => $val )
			$url .= ' ' . $key . '="' . $val . '"';
		$url .= ' />';
	}
	return $url;
}




?>
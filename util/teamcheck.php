<?
/*
 * Created on May 28, 2007
 *
 */
 include("../application.php");
 
$player1 = $_REQUEST['player1'];
$player2 = $_REQUEST['player2'];
$courttype = $_REQUEST['courttype'];
 
 if( ! isset($player1) || ! isset($player2) || !isset($courttype)){
 	print"Please specify player1 and player2 and courttype";
 	die;
 }
 
 // ********* Basically Copied from getTeamIDForCurrentUser
 
 //find teams for current user
	$currentuserteamquery = "SELECT teamdetails.teamid
	                                  FROM tblTeams teams, tblkpTeams teamdetails
	                                  WHERE teams.teamid = teamdetails.teamid
	                                  AND teamdetails.userid=$player1";

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
	                                         AND teamdetails.userid=$player2";

	// run the query on the database
	$currentuserpartnerteamresult = db_query($currentuserpartnerteamquery);

	//Build an single dimensional array for current users partners teams
	$currentUserPartnerStack = array ();
	while ($currentuserpartnerteamarray = mysql_fetch_array($currentuserpartnerteamresult)) {
		array_push($currentUserPartnerStack, $currentuserpartnerteamarray['teamid']);
	}

	$teamexistsarray = array_intersect($currentUserStack, $currentUserPartnerStack);

	//print "This is my teamarray: $teamexistsarray[0]";
	if (count($teamexistsarray) ==1) {
		//found  a team
		$teamid = current($teamexistsarray);
		print "This is the team: $teamid";

	} 
	elseif(count($teamexistsarray) > 1){
		print "Guys are on more than one team";
	}
	else{
		print "Guys are not teams";
	}
 
 
?>

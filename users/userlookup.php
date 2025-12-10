<?php

$name = $_GET['name'];
$clubid = $_GET['clubid'];
$courtid = $_GET['courtid'];
$courttype = $_GET['courttype'];
$siteid = $_GET['siteid'];
$userid = $_GET['userid'];
$ladderid = $_GET['ladderid'];
$boxid = $_GET['boxid'];

$limit = 17;

//if court id is set, look up the court id

if (isset($courtid)) {
    $courttype = get_courtTypeForCourt($courtid);
}

if( isset($ladderid) ){
	if (isDebugEnabled(1)) logMessage("Users.Userlookup: name: $name ladderid: $ladderid userid: $userid");

} else {
	if (isDebugEnabled(1)) logMessage("Users.Userlookup: name: $name clubid: $clubid siteid: $siteid userid: $userid");

}



//Don't exclude administrators

if (isProgramAdmin($userid)) {
    $userid = 0;
}

// If a courtype isn't defined, then just leave this out of the query.  This will be cases like on the
// my buddies page where a courttype really isn't involved.

if ( isset($boxid) ){

	$query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
	FROM tblUsers users
	INNER JOIN tblClubUser tCU on users.userid = tCU.userid
	INNER JOIN tblClubLadder tCL on tCU.userid = tCL.userid
	INNER JOIN tblkpBoxLeagues tBL on users.userid = tBL.userid
	WHERE tCU.roleid!= 4
	AND tBL.boxid = 922
	AND users.userid = tCU.userid
	AND tCU.enable='y'
	AND tCU.enddate IS NULL
	AND
		(users.firstname LIKE '%$name%'
		 OR users.lastname LIKE '%$name%'
		 OR (
			users.firstname = SPLIT_STR('%$name%', ' ', 1)
			AND users.lastname = SPLIT_STR('%$name%', ' ', 2)
			 ))
	ORDER BY users.lastname
	LIMIT $limit";
}
else if ( isset ($ladderid) ){

	$query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
	FROM tblUsers users, tblClubUser clubuser
	join tblClubLadder tCL on clubuser.userid = tCL.userid
	WHERE clubuser.roleid!= 4
	AND users.userid = clubuser.userid
	AND tCL.ladderid=$ladderid
	AND clubuser.enable='y'
	AND clubuser.enddate IS NULL
	AND tCL.enddate IS NULL
	AND users.userid != $userid
	AND
		(users.firstname LIKE '%$name%'
		 OR users.lastname LIKE '%$name%'
		 OR (
			users.firstname = SPLIT_STR('$name', ' ', 1)
			AND users.lastname = SPLIT_STR('$name', ' ', 2)
			 ))
	ORDER BY users.lastname
	LIMIT $limit";
}
else if (empty($courttype)) {
    $query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
	                FROM tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth, tblClubUser clubuser
					WHERE users.userid = rankings.userid
	                AND users.userid = siteauth.userid
	                AND clubuser.roleid!= 4
					AND users.userid = clubuser.userid
	                AND clubuser.clubid=$clubid
	                AND siteauth.siteid=$siteid
	                AND users.userid != $userid
	                AND rankings.usertype=0
	                AND clubuser.enable='y'
					AND clubuser.enddate IS NULL
					AND 
						(users.firstname LIKE '%$name%'
						 OR users.lastname LIKE '%$name%'
						 OR (
                            users.firstname = SPLIT_STR('$name', ' ', 1)
                            AND users.lastname = SPLIT_STR('$name', ' ', 2)
                             ))
	                ORDER BY users.lastname
					LIMIT $limit";
} else {
    $query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
	                FROM tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth, tblClubUser clubuser
					WHERE users.userid = rankings.userid
	                AND users.userid = siteauth.userid
	                AND clubuser.roleid!= 4
					AND users.userid = clubuser.userid
	                AND clubuser.clubid=$clubid
	                AND siteauth.siteid=$siteid
	                AND users.userid != $userid
	                AND rankings.courttypeid= $courttype
	                AND rankings.usertype=0
	                AND clubuser.enable='y'
					AND clubuser.enddate IS NULL
					AND 
						(users.firstname LIKE '%$name%'
						 OR users.lastname LIKE '%$name%'
						 OR (
                            users.firstname = SPLIT_STR('$name', ' ', 1)
                            AND users.lastname = SPLIT_STR('$name', ' ', 2)
                             ))
	                ORDER BY users.lastname
					LIMIT $limit";
}


$result = db_query($query);

if (isDebugEnabled(1)) logMessage("Users.UserLookup: Found " . mysqli_num_rows($result) . " users");
while ($row = mysqli_fetch_row($result)) {
    echo '<item><name>' . $row[1] . ' ' . $row[2] . '</name><value>' . $row[0] . ' </value></item>';
}
?>

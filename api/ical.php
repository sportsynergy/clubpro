<?php

/**
 Usage: https://www.sportsynergy.net/api/ical.php?user=1237
*/

require '../vendor/autoload.php';
include '../application.php';
include '../lib/Helper_DateTimeZone.php';

$userid = $_REQUEST['user'];

if (!isset($userid)){
	die("sorry");
}


// get club info
$query = "SELECT tblClubs.timezone as tzoffset, tblClubs.clubname 			FROM tblUsers 
		INNER JOIN tblClubUser on tblUsers.userid = tblClubUser.userid
		INNER JOIN tblClubs on tblClubUser.clubid = tblClubs.clubid
		WHERE tblUsers.userid = '$userid'";

$result = db_query($query);
$array = db_fetch_array($result);


// roughly
$monthago = time() - (60*60*24*30);
$intwomonths = time() + (60*60*24*30*2);


// account for timezone
$Dtz = new Helper_DateTimeZone(Helper_DateTimeZone::tzOffsetToName($array['tzoffset'] + date("I") ));



$vTimezone = new \Eluceo\iCal\Component\Timezone($Dtz->getName());
$dtz = new DateTimeZone($Dtz->getName());

$vCalendar = new \Eluceo\iCal\Component\Calendar('www.sportsynergy.net');
$vCalendar->setTimezone($vTimezone);



// Get singles
$query = " (SELECT tblReservations.time,tblCourts.courtname, tblClubs.clubname, tblSportType.sportname, tblMatchType.name AS 'matchtype', tblReservations.duration,
(SELECT concat(u.firstname,' ',u.lastname) FROM tblUsers u
	INNER JOIN tblkpUserReservations p ON u.userid = p.userid
	WHERE p.reservationid = tblReservations.reservationid
AND p.userid != '1023') as 'partner'
FROM tblReservations
			INNER JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
			INNER JOIN tblCourts ON tblReservations.courtid = tblCourts.courtid
			INNER JOIN tblClubs ON tblCourts.clubid = tblClubs.clubid
			INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid
			INNER JOIN tblSportType ON tblCourtType.sportid = tblSportType.sportid
			INNER JOIN tblMatchType ON tblReservations.matchtype = tblMatchType.id
			WHERE tblkpUserReservations.userid = '$userid'
			AND tblReservations.usertype = 0
			AND tblReservations.time > $monthago
			AND tblReservations.time < $intwomonths
			AND tblReservations.enddate is NULL
			LIMIT 500
			)
			UNION ALL

			(SELECT tblReservations.time,tblCourts.courtname, tblClubs.clubname, tblSportType.sportname,tblMatchType.name AS 'matchtype', tblReservations.duration,'' as 'partner'
			FROM tblReservations 
			INNER JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
			INNER JOIN tblCourts ON tblReservations.courtid = tblCourts.courtid
			INNER JOIN tblClubs ON tblCourts.clubid = tblClubs.clubid
			INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid
			INNER JOIN tblSportType ON tblCourtType.sportid = tblSportType.sportid 
			INNER JOIN tblMatchType ON tblReservations.matchtype = tblMatchType.id
			WHERE tblkpUserReservations.userid IN (SELECT tblTeams.teamid
		FROM tblTeams INNER JOIN tblkpTeams ON tblTeams.teamid = tblkpTeams.teamid
		WHERE tblkpTeams.userid ='$userid')
			AND tblReservations.usertype = 1 
		    AND tblkpUserReservations.usertype = 1
		    AND tblReservations.time > $monthago
			AND tblReservations.time < $intwomonths
			AND tblReservations.enddate is NULL
			
			LIMIT 500
			) ORDER by time DESC ";


// Get all reservations for the user including: court name, time and
$result = db_query($query);

while($array = db_fetch_array($result)) {    
	
	$matchtype = '';
	if ($array['matchtype'] == 'lesson') {
		$matchtype = ucfirst ( $array['matchtype'] ).' ';
	}

	$with = '';
	if( isset($array['partner']) ){
		$with = "with ".$array['partner'];
	}


	$start = gmdate("Y-m-d H:i",$array['time']);
	
	$duration = 3600;
	if( isset($array['duration']) ){
		$duration = $array['duration'];
	}




	$endtime = $array['time'] + $duration;
	
	$end = gmdate("Y-m-d H:i",$endtime);

	$vEvent = new \Eluceo\iCal\Component\Event();
	
	$vEvent->setDtStart(new DateTime($start,$dtz))
    ->setDtEnd(new DateTime($end,$dtz))
    ->setLocation($array['clubname'])
    ->setSummary($array['sportname'].' '.$matchtype.'on '.$array['courtname'].' '.$with);
	
	$vEvent->setUseTimezone(true);

	$vCalendar->addComponent($vEvent);

	}

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="cal.ics"');

echo $vCalendar->render();




?>
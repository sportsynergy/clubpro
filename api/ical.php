<?php

/**
* Class and Function List:
* Function list:
* Classes list:
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


$Dtz = new Helper_DateTimeZone(Helper_DateTimeZone::tzOffsetToName($array['tzoffset']));


$vTimezone = new \Eluceo\iCal\Component\Timezone($Dtz->getName());
$dtz = new DateTimeZone($Dtz->getName());

$vCalendar = new \Eluceo\iCal\Component\Calendar('www.sportsynergy.net');
$vCalendar->setTimezone($vTimezone);



// Get singles
$query = "SELECT tblReservations.time,tblCourts.courtname, tblClubs.clubname, tblSportType.sportname
			FROM tblReservations 
			INNER JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
			INNER JOIN tblCourts ON tblReservations.courtid = tblCourts.courtid
			INNER JOIN tblClubs ON tblCourts.clubid = tblClubs.clubid
			INNER JOIN tblCourtType ON tblCourts.courttypeid = tblCourtType.courttypeid
			INNER JOIN tblSportType ON tblCourtType.sportid = tblSportType.sportid
			WHERE tblkpUserReservations.userid = '$userid' 
			ORDER by tblReservations.time DESC
			LIMIT 500";

// union doubles

// Get all reservations for the user including: court name, time and
$result = db_query($query);

while($array = db_fetch_array($result)) {    
	
	$start = gmdate("Y-m-d H:i",$array['time']);
	$endtime = $array['time'] + 3600;
	
	$end = gmdate("Y-m-d H:i",$endtime);

	$vEvent = new \Eluceo\iCal\Component\Event();
	
	$vEvent->setDtStart(new DateTime($start,$dtz))
    ->setDtEnd(new DateTime($end,$dtz))
    ->setLocation($array['clubname'])
    ->setSummary($array['sportname'].' on '.$array['courtname']);
	
	$vEvent->setUseTimezone(true);

	$vCalendar->addComponent($vEvent);

	}

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="cal.ics"');

echo $vCalendar->render();




?>
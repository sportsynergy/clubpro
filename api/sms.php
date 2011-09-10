<?
/**
 * To: <?php echo $_REQUEST['To'] ?>-----From: <?php echo $_REQUEST['From'] ?> -----Body:<?php echo $_REQUEST['Body'] ?>
 */
include("../application.php");
session_start();
date_default_timezone_set('GMT');


//Commands
$next = "next";
$response = "";


//Parse the message body
$parts = explode(" ", $_REQUEST['Body'] );
$id = $parts[0];
$command = $parts[1];

//Handle Errors
if( count($parts)!=2  ){ 
	$response = "Invalid Message. Usage: [sportsynergy id] next";

}
if($command==$next){ 
	
	//Get the timezone
	$query = "SELECT clubs.timezone 
				FROM tblClubs clubs, tblClubUser clubuser 
				WHERE clubuser.userid = $id
				AND clubs.clubid = clubuser.clubid";
	$result = db_query($query);
	
	if(mysql_num_rows($result)==0){
		$response = "no users found";
	}
	else{
		
		$timezone = mysql_result($result,0);
	
	
	
		// Get the current date
		$current =  mktime()+($timezone*3600);
	
		//lookup user and get first reservation that occurs after the current time
		$query = "SELECT reservations.reservationid, reservations.time, courts.courtname
						FROM tblReservations reservations, tblkpUserReservations reservationdetails, tblCourts courts
						WHERE reservations.courtid = courts.courtid
						AND reservations.time > $current
						AND reservations.reservationid = reservationdetails.reservationid
						AND reservations.enddate IS NULL
						AND reservations.usertype = 0
						AND reservationdetails.userid = $id
						ORDER BY reservations.time
						LIMIT 1";
		
		
		$result = db_query($query);
		
		if( mysql_num_rows($result)==0){
			$response = "No upcoming reservations found.";
		}else{
			
			$resObj = db_fetch_object($result);
				
			$query2 = "SELECT users.userid, users.firstname, users.lastname from tblkpUserReservations details, tblUsers users
						 WHERE users.userid = details.userid
						 AND reservationid = $resObj->reservationid
						 ORDER BY users.userid";
			
			$result2 = db_query($query2);
			
			$playerOneObj = db_fetch_object($result2);
			$playerTwoObj = db_fetch_object($result2);
			
			$date = gmdate("l F j", $resObj->time);
			$hour= gmdate("g:i a", $resObj->time);
	
			if( mysql_num_rows($result2)<2){
				$response = "$playerOneObj->firstname $playerOneObj->lastname is looking for a game on $resObj->courtname at $hour";
			}
			else{
				$response = "$playerOneObj->firstname $playerOneObj->lastname and $playerTwoObj->firstname $playerTwoObj->lastname are playing on $resObj->courtname $date at $hour";
			}
			
				
				
			
		}
	
	}
	
	
	
 } else{ 
	$response = "Invalid Message. Usage: [sportsynergy id] next";
}

header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
   <Sms><?=$response?></Sms>
</Response>

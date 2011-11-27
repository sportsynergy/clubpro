<?

/*
 * $LastChangedRevision: 823 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-06 22:50:11 -0600 (Sun, 06 Feb 2011) $

*/

?>


<div>


<?
$challengeMatchResult = getMatchesByType( get_siteid(), 2, 10 );

if(mysql_num_rows($challengeMatchResult) > 0){ ?>
	
	
<h2 style="padding-top: 15px">Recent Challenge Matches</h2>
<hr class="hrline"/>

<ul class="ladderactivity">
<?
	
while($challengeMatch = mysql_fetch_array($challengeMatchResult)){ 

	$playerQuery = "SELECT users.firstname, users.lastname, reservationdetails.outcome, users.userid
						FROM tblkpUserReservations reservationdetails, tblUsers users
						WHERE reservationdetails.reservationid = '$challengeMatch[reservationid]'
						AND users.userid = reservationdetails.userid 
						ORDER BY reservationdetails.outcome DESC";

	$playerResult = db_query($playerQuery);
	
		// only display full reservations
		if( mysql_num_rows($playerResult)==2){ 
	
			$playerArray = mysql_fetch_array($playerResult);
			$scored = $playerArray['outcome'] > 0 ? true : false;
			
			$playerOne = "$playerArray[firstname] $playerArray[lastname]";
			$userid1 = $playerArray['userid'];
			
			$playerArray = mysql_fetch_array($playerResult);
			$playerTwo = "$playerArray[firstname] $playerArray[lastname]";
			$userid2 = $playerArray['userid'];
			$loserscore = $playerArray['outcome'];
			  
			$inreservation = get_userid() == $userid1 || get_userid() == $userid2 ? true : false;
			
			printLadderEvent($playerOne, $playerTwo, $challengeMatch['courtname'], $challengeMatch['time'], $challengeMatch['reservationid'], $scored, $loserscore, $inreservation )
			?>
			
		<? } ?>
	

</ul>

<? }} ?>





</div>
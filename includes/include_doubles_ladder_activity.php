


<div>

<h2 style="padding-top: 15px">Recent Challenge Matches</h2>
<hr class="hrline"/>

<?

//hard coding courttype id for now
$challengeMatchArray = getDoublesChallengeMatches( get_siteid(), $courttypeid, 10 );

if( count($challengeMatchArray) > 0){ ?>
	


<ul class="ladderactivity">	

<?
	
for($i=0; $i < count($challengeMatchArray); ++$i){
	$challengeMatch = $challengeMatchArray[$i];
	
	$scored = $challengeMatch['score'];
	$userid = get_userid();
	$inreservation = in_array($userid,$challengeMatch['ids']) ;
	
	//don't include timestamp
	$challengeDate = explode(" ",$challengeMatch['date']);
	
	printLadderEvent($challengeMatch['id'], $challengeMatch['challenger_team'], $challengeMatch['challengee_team'], $challengeDate[0], $scored, $inreservation, false);
	
} ?>


</ul>

<? } else{ ?>
	
	No challenge matches found.
	
<? } ?>





</div>
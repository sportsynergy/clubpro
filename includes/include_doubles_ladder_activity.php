


<div>

<h2>Recent Challenge Matches</h2>
<hr class="hrline"/>

<?

//hard coding courttype id for now
$challengeMatchArray = getDoublesChallengeMatches( get_siteid(), $courttypeid, 10 );

if( count($challengeMatchArray) > 0){ ?>
	


<table class="activitytable" width="450">
<tr>
	<th>Date</th>
	<th>Challenger</th>
	<th>Challengee</th>
	<th>Winner</th>
</tr>

<?
	
for($i=0; $i < count($challengeMatchArray); ++$i){
	$challengeMatch = $challengeMatchArray[$i];
	
	$scored = $challengeMatch['score'];
	$userid = get_userid();
	$inreservation = in_array($userid,$challengeMatch['ids']) ;
	
	//don't include timestamp
	$challengeDate = explode(" ",$challengeMatch['date']);
	
	printLadderEventRow($challengeMatch['id'], $challengeMatch['challenger_team'], $challengeMatch['challengee_team'], $challengeDate[0], $scored, $inreservation, false);
	
} ?>


</table>

<div style="margin-top: 20px">
	<span class="smallbold">note:</span>
	<span class="normalsm">mouse over winner's name to see the score</span>
</div>

<? } else{ ?>
	
	No challenge matches found.
	
<? } ?>


</div>